<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use App\Helpers\Tool;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Console\Command;

class Login extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:login';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Account Login';

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client_id = Tool::config('client_id');
        $this->client_secret = Tool::config('client_secret');
        $this->redirect_uri = Tool::config('redirect_uri');
        $this->authorize_url = Tool::config('app_type') == 'com' ? Constants::AUTHORITY_URL . Constants::AUTHORIZE_ENDPOINT : Constants::AUTHORITY_URL_21V . Constants::AUTHORIZE_ENDPOINT_21V;
        $this->access_token_url = Tool::config('app_type') == 'com' ? Constants::AUTHORITY_URL . Constants::TOKEN_ENDPOINT : Constants::AUTHORITY_URL_21V . Constants::TOKEN_ENDPOINT_21V;
        $this->scopes = Constants::SCOPES;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $defined_redirect = Constants::REDIRECT_URI;
        if (has_bind()) {
            $this->warn('已登录绑定');
            return;
        }
        if ($this->client_id == '' || $this->client_secret == '' || $this->redirect_uri == '') {
            if ($this->confirm('未配置client_id、client_secret，现在配置吗？')) {
                $client_id = $this->ask('请输入 client_id');
                $client_secret = $this->ask('请输入 client_secret');
                $redirect_uri = $defined_redirect;
                $data = [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $redirect_uri
                ];
                Tool::updateConfig($data);
                $this->warn('请重新运行此命令登录');
            }
            return;
        }
        if ($this->redirect_uri !== $defined_redirect) {
            $this->warn("此方法仅适用于以 {$defined_redirect} 作中转的应用");
            return;
        };
        $values = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => $this->scopes,
            'response_type' => 'code',
        ];
        $query = http_build_query($values, '', '&', PHP_QUERY_RFC3986);
        $authorizationUrl = $this->authorize_url . "?{$query}";
        $this->info("请复制此链接到浏览器打开获取 【code】\n{$authorizationUrl}");
        $code = $this->ask('请输入浏览器获取 【code】');
        try {
            $client = new Client();
            $form_params = [
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'redirect_uri' => $this->redirect_uri,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ];
            if (Tool::config('app_type') == 'cn') $form_params = array_add($form_params, 'resource', Constants::REST_ENDPOINT_21V);
            $response = $client->post($this->access_token_url, [
                'form_params' => $form_params
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
            $this->info('登陆成功');
            $this->info('Account [' . bind_account() . ']');
        } catch (ClientException $e) {
            $this->warn($e->getMessage());
            return;
        }
    }
}
