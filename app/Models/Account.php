<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Models;

use App\Helpers\HashidsHelper;
use App\Models\Traits\HelperModel;
use App\Service\Client;
use App\Service\GraphErrorEnum;
use App\Service\OneDrive;
use Curl\Curl;
use Illuminate\Database\Eloquent\Model;
use Log;
use Cache;

/**
 * Class Account
 *
 * @package App\Models
 * @property $hash_id
 * @property int $id
 * @property string $remark
 * @property string $accountType
 * @property string $clientId
 * @property string $clientSecret
 * @property string $redirectUri
 * @property string $accessToken
 * @property string $refreshToken
 * @property int $tokenExpires
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $config
 * @property-read mixed $open_sp
 * @property-read mixed $sp_id
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccountType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereClientSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereMap(array $map)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRedirectUri($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereTokenExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    use  HelperModel;

    /**
     * @var string[] $fillable
     */
    protected $fillable = [
        'remark',
        'accountType',
        'clientId',
        'clientSecret',
        'redirectUri',
        'accessToken',
        'refreshToken',
        'tokenExpires',
        'status',
        'config',
    ];

    /**
     * @var string[] $casts
     */
    protected $casts = [
        'tokenExpires' => 'int',
        'status' => 'int'
    ];

    /**
     * id => HashId
     * @return string
     */
    public function getHashIdAttribute()
    {
        return HashidsHelper::encode($this->id);
    }

    /**
     * 如果 value 是 json 则转成数组
     *
     * @param $value
     * @return mixed
     */
    public function getConfigAttribute($value)
    {
        return is_json($value) ? json_decode($value, true) : $value;
    }

    /**
     * 如果 value 是数组 则转成 json
     *
     * @param $value
     */
    public function setConfigAttribute($value): void
    {
        $this->attributes['config'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * @return mixed
     */
    public function getSpIdAttribute()
    {
        return $this->config['sp_id'] ?? '';
    }

    /**
     * @return mixed
     */
    public function getOpenSpAttribute()
    {
        return $this->config['open_sp'] ?? 0;
    }


    /**
     * 获取OneDrive服务
     * @param false $useSharepoint
     * @return \App\Service\OneDrive
     */
    public function getOneDriveService($useSharepoint = true)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            abort(500, 'Not Found AccessToken.');
        }
        $clientConfig = (new Client())
            ->setAccountType($this->accountType);
        $restEndpoint = $clientConfig->getRestEndpoint();
        $service = new OneDrive($accessToken, $restEndpoint);
        return $useSharepoint ? $service->sharepoint($this->open_sp, $this->sp_id) : $service;
    }

    /**
     * 获取账号access_token
     * @return mixed|string
     */
    private function getAccessToken()
    {
        if (!$this->accessToken || !$this->refreshToken || !$this->tokenExpires) {
            return '';
        }
        $now = time() + 300;
        if ($this->tokenExpires <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh
            return $this->refreshAccessToken();
        }
        return $this->accessToken;
    }

    /**
     *  刷新账号access_token
     * @return mixed|string
     */
    private function refreshAccessToken()
    {
        $accountType = $this->accountType;
        $clientConfig = (new Client())
            ->setAccountType($accountType);
        $form_params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token',
        ];
        if ($accountType === 'CN') {
            $form_params['resource'] = $clientConfig->getRestEndpoint();
        }
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $curl->post($clientConfig->getUrlAccessToken(), $form_params);
        if ($curl->error) {
            $error = [
                'errorCode' => $curl->errorCode,
                'errorMessage' => $curl->errorMessage,
                'request' => $form_params,
                'response' => collect($curl->response)->toArray()
            ];
            Log::error('Error refreshing access token. ', $error);
            return '';
        }
        $_accessToken = collect($curl->response);
        $data = [
            'accessToken' => $_accessToken->get('access_token'),
            'refreshToken' => $_accessToken->get('refresh_token'),
            'tokenExpires' => $_accessToken->get('expires_in') + time(),
        ];
        $this->update($data);
        $this->refreshOneDriveQuota(true);
        // Log::info('刷新accessToken', ['account_id', $this->account->id]);
        return $_accessToken->get('access_token');
    }

    /**
     * 获取网盘信息
     * @param bool $refresh
     * @return array|mixed|null
     */
    public function refreshOneDriveQuota($refresh = false)
    {
        if ($refresh) {
            Cache::forget("d:quota:{$this->id}");
        }
        $service = $this->getOneDriveService();
        $resp = Cache::remember("d:quota:{$this->id}", setting('cache_expires'), static function () use ($service) {
            return $service->fetchInfo();
        });
        if (array_key_exists('code', $resp)) {
            $msg = array_get($resp, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($resp['code']) ?? $msg;
            Cache::forget("d:quota:{$this->id}");
            Log::error($msg, $resp);
            $resp = [];
        }
        return $resp;
    }

    /**
     * 获取账号信息
     * @param bool $refresh
     * @return array|mixed|null
     */
    public function getOneDriveInfo($refresh = false)
    {
        if ($refresh) {
            Cache::forget("d:me:{$this->id}");
        }
        $service = $this->getOneDriveService();
        $resp = Cache::remember("d:me:{$this->id}", setting('cache_expires'), static function () use ($service) {
            return $service->fetchMe();
        });
        if (array_key_exists('code', $resp)) {
            $msg = array_get($resp, 'message', '404NotFound');
            $msg = GraphErrorEnum::get($resp['code']) ?? $msg;
            Cache::forget("d:me:{$this->id}");
            Log::error($msg, $resp);
            $resp = [];
        }
        return $resp;
    }

    /**
     * 获取全部账号
     * @return mixed
     */
    public static function fetchlist()
    {
        return Account::query()
            ->select(['id', 'remark'])
            ->where('status', 1)->get();
    }
}
