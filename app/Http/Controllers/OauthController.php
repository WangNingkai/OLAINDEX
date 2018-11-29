<?php

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * 授权操作
 * Class OauthController
 * @package App\Http\Controllers
 */
class OauthController extends Controller
{

    /**
     * @var string
     */
    protected $client_id;

    /**
     * @var string
     */
    protected $client_secret;

    /**
     * @var string
     */
    protected $redirect_uri;

    /**
     * @var string
     */
    protected $authorize_url;

    /**
     * @var string
     */
    protected $access_token_url;

    /**
     * @var string
     */
    protected $scopes;

    /**
     * OauthController constructor.
     */
    public function __construct()
    {
        $this->middleware('checkInstall');
        $this->client_id = Tool::config('client_id');
        $this->client_secret = Tool::config('client_secret');
        $this->redirect_uri = Tool::config('redirect_uri');
        $this->authorize_url = Tool::config('account_type', 'com') === 'com' ? Constants::AUTHORITY_URL . Constants::AUTHORIZE_ENDPOINT : Constants::AUTHORITY_URL_21V . Constants::AUTHORIZE_ENDPOINT_21V;
        $this->access_token_url = Tool::config('account_type', 'com') === 'com' ? Constants::AUTHORITY_URL . Constants::TOKEN_ENDPOINT : Constants::AUTHORITY_URL_21V . Constants::TOKEN_ENDPOINT_21V;
        $this->scopes = Constants::SCOPES;
    }

    /**
     * 处理授权
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function oauth(Request $request)
    {
        // 检测是否已授权
        if (Tool::hasBind()) {
            return redirect()->route('home');
        }
        if ($request->isMethod('get')) {
            if (!$request->has('code')) {
                return $this->authorizeLogin();
            } else {
                if (empty($request->get('state')) || !Session::has('state') || ($request->get('state') !== Session::get('state'))) {
                    Tool::showMessage('Invalid state', false);
                    Session::forget('state');
                    return view('message');
                }
                Session::forget('state'); // 兼容下次登陆
                $code = $request->get('code');
                try {
                    $client = new Client();
                    $form_params = [
                        'client_id' => $this->client_id,
                        'client_secret' => $this->client_secret,
                        'redirect_uri' => $this->redirect_uri,
                        'code' => $code,
                        'grant_type' => 'authorization_code',
                    ];
                    if (Tool::config('account_type', 'com') === 'cn') $form_params = array_add($form_params, 'resource', Constants::REST_ENDPOINT_21V);
                    $response = $client->post($this->access_token_url, [
                        'form_params' => $form_params
                    ]);
                    $token = json_decode($response->getBody()->getContents(), true);
                    $access_token = $token['access_token'];
                    $refresh_token = $token['refresh_token'];
                    $expires = (int)$token['expires_in'] !== 0 ? time() + $token['expires_in'] : 0;
                    $data = [
                        'access_token' => $access_token,
                        'refresh_token' => $refresh_token,
                        'access_token_expires' => $expires
                    ];
                    Tool::updateConfig($data);
                    return redirect()->route('home');
                } catch (ClientException $e) {
                    Tool::showMessage($e->getMessage(), false);
                    return view('message');
                }
            }
        } else {
            Tool::showMessage('Invalid Request', false);
            return view('message');
        }
    }

    /**
     * 请求授权登陆
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authorizeLogin()
    {
        // 跳转授权登录
//        $state = str_random(32);
        $state = urlencode(config('app.url')); // 添加中转
        Session::put('state', $state);
        $values = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => $this->scopes,
            'response_type' => 'code',
            'state' => $state,
        ];
        $query = http_build_query($values, '', '&', PHP_QUERY_RFC3986);
        $authorizationUrl = $this->authorize_url . "?{$query}";
        return redirect()->away($authorizationUrl);
    }

    /**
     * 刷新授权
     * @param bool $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshToken($redirect = true)
    {
        $expires = Tool::config('access_token_expires', 0);
        $hasExpired = $expires - time() <= 0;
        if (!$hasExpired) return response()->json(['code' => 400, 'msg' => 'Bad Request']);
        $existingRefreshToken = Tool::config('refresh_token');
        try {
            $client = new Client();
            $form_params = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri' => $this->redirect_uri,
                'refresh_token' => $existingRefreshToken,
                'grant_type' => 'refresh_token',
            ];
            if (Tool::config('account_type', 'com') === 'cn') $form_params = array_add($form_params, 'resource', Constants::REST_ENDPOINT_21V);
            $response = $client->post($this->access_token_url, [
                'form_params' => $form_params,
            ]);
            $token = json_decode($response->getBody()->getContents(), true);
            $access_token = $token['access_token'];
            $refresh_token = $token['refresh_token'];
            $expires = $token['expires_in'] != 0 ? time() + $token['expires_in'] : 0;
            $data = [
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
                'access_token_expires' => $expires
            ];
            Tool::updateConfig($data);
            if ($redirect) {
                $redirect = Session::get('refresh_redirect') ?? '/';
                return redirect()->away($redirect);
            } else {
                return json_encode(['code' => 200, 'msg' => 'ok']);
            }
        } catch (ClientException $e) {
            return json_encode(['code' => $e->getCode(), 'msg' => $e->getMessage()]);
        }
    }
}
