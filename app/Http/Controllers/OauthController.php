<?php

namespace App\Http\Controllers;

use App\Utils\Tool;
use App\Models\Setting;
use App\Service\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use ErrorException;
use Log;
use Session;

/**
 * 授权操作
 * Class OauthController
 *
 * @package App\Http\Controllers
 */
class OauthController extends Controller
{

    /**
     * OauthController constructor.
     */
    public function __construct()
    {
        $this->middleware('verify.installation');
    }

    /**
     * @param Request $request
     *
     * @return Factory|RedirectResponse|View
     * @throws ErrorException
     */
    public function oauth(Request $request)
    {
        // 检测是否已授权
        if (Tool::hasBind()) {
            return redirect()->route('home');
        }
        if ($request->isMethod('get')) {
            if (!$request->has('code')) {
                return $this->authorizeLogin(request()->getHttpHost());
            }
            if (empty($request->get('state')) || !Session::has('state')
                || ($request->get('state') !== Session::get('state'))) {
                Tool::showMessage('Invalid state', false);
                Session::forget('state');

                return view(config('olaindex.theme') . 'message');
            }
            Session::forget('state'); // 兼容下次登录
            $code = $request->get('code');

            $token = Authorize::getInstance(setting('account_type'))->getAccessToken($code);

            $token = $token->toArray();
            Log::info('access_token', $token);
            $access_token = Arr::get($token, 'access_token');
            $refresh_token = Arr::get($token, 'refresh_token');
            $expires = Arr::get($token, 'expires_in') !== 0 ? time() + Arr::get($token, 'expires_in') : 0;

            $data = [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'access_token_expires' => $expires,
            ];
            Setting::batchUpdate($data);
            Tool::refreshAccount(one_account());

            return redirect()->route('home');
        }
        Tool::showMessage('Invalid Request', false);

        return view(config('olaindex.theme') . 'message');
    }

    /**
     * @param string $url
     * @return RedirectResponse
     */
    public function authorizeLogin($url = ''): RedirectResponse
    {
        // 跳转授权登录
        // $state = str_random(32);
        $state = urlencode($url ? 'http://' . $url : config('app.url')); // 添加中转
        Session::put('state', $state);
        $authorizationUrl = Authorize::getInstance(setting('account_type'))->getAuthorizeUrl($state);

        return redirect()->away($authorizationUrl);
    }

    /**
     * @param bool $redirect
     * @return false|Factory|RedirectResponse|View|string
     * @throws ErrorException
     */
    public function refreshToken($redirect = true)
    {
        $existingRefreshToken = setting('refresh_token');
        $token = Authorize::getInstance(setting('account_type'))->refreshAccessToken($existingRefreshToken);

        $token = $token->toArray();
        $access_token = Arr::get($token, 'access_token');
        $refresh_token = Arr::get($token, 'refresh_token');
        $expires = Arr::get($token, 'expires_in') !== 0 ? time() + Arr::get($token, 'expires_in') : 0;
        $data = [
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'access_token_expires' => date('Y-m-d H:i:s', $expires),
        ];
        Log::info('refresh_token', $data);
        Setting::batchUpdate($data);
        Tool::refreshAccount(one_account());
        if ($redirect) {
            $redirect = Session::get('refresh_redirect', '/');

            return redirect()->away($redirect);
        }
        return json_encode(['code' => 200, 'msg' => 'ok']);
    }
}
