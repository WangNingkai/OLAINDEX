<?php

namespace App\Console\Commands\OneDrive;

use App\Helpers\Constants;
use App\Helpers\Tool;
use Curl\Curl;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

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
    }

    /**
     * @throws \ErrorException
     */
    public function handle()
    {
        getDefaultOneDriveAccount($this->option('one_drive_id'));
        if (app('onedrive')->is_binded) {
            $this->error('Already bind account');
            exit;
        }
        if (!app('onedrive')->is_configuraed) {
            if ($this->confirm('Missing client_id & client_secret,continue?')) {
                $account_type = $this->choice(
                    'Please choose a version (com:World cn:21Vianet)',
                    ['com', 'cn'],
                    'com'
                );
                $client_id = $this->ask('client_id');
                $client_secret = $this->ask('client_secret');
                $redirect_uri = $this->ask(
                    'redirect_uri',
                    Constants::DEFAULT_REDIRECT_URI
                );
                $cache_expires = $this->ask('cache expires (s)');
                $data = [
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri'  => $redirect_uri,
                    'account_type'  => $account_type,
                    'expires'       => $cache_expires,
                ];
                // Tool::updateConfig($data);
                app('onedrive')->update($data);
                $this->info('Configuration completed!');
                $this->warn('Please run this command again!');
            }
            exit('Already out!');
        }
        $values = [
            'client_id'     => app('onedrive')->client_id,
            'redirect_uri'  => app('onedrive')->redirect_uri,
            'scope'         => app('onedrive')->scopes,
            'response_type' => 'code',
        ];
        $query = http_build_query($values, '', '&', PHP_QUERY_RFC3986);
        $authorizationUrl = app('onedrive')->authorize_url . "?{$query}";
        $this->info("Please copy this link to your browser to open.\n{$authorizationUrl}");
        $code = $this->ask('Please enter the code obtained by the browser.');
        $form_params = [
            'client_id'     => app('onedrive')->client_id,
            'client_secret' => app('onedrive')->client_secret,
            'redirect_uri'  => app('onedrive')->redirect_uri,
            'code'          => $code,
            'grant_type'    => 'authorization_code',
        ];

        if (app('onedrive')->account_type == 'cn') {
            $form_params = Arr::add(
                $form_params,
                'resource',
                Constants::REST_ENDPOINT_21V
            );
        }

        $curl = new Curl();
        $curl->post($this->access_token_url, $form_params);
        if ($curl->error) {
            Log::error(
                'OneDriveGraph Login Err',
                [
                    'code' => $curl->errorCode,
                    'msg'  => $curl->errorMessage,
                ]
            );
            $msg = 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";

            exit($msg);
        } else {
            $token = collect($curl->response)->toArray();
            $access_token = $token['access_token'];
            $refresh_token = $token['refresh_token'];
            $expires = $token['expires_in'] != 0 ? time() + $token['expires_in']
                : 0;
            $data = [
                'access_token'         => $access_token,
                'refresh_token'        => $refresh_token,
                'access_token_expires' => $expires,
            ];
            // Tool::updateConfig($data);
            app('onedrive')->update($data);
            $this->info('Login Success!');
            $this->info('Account [' . Tool::getBindAccount() . ']');
        }
    }
}
