<?php

namespace App\Console\Commands\OneDrive;

use App\Models\Setting;
use App\Service\Authorize;
use App\Service\CoreConstants;
use App\Utils\Tool;
use Curl\Curl;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Log;

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
        if (Tool::hasBind()) {
            $this->error('Already bind account');
            exit;
        }
        if (!Tool::hasConfig()) {
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
                    CoreConstants::DEFAULT_REDIRECT_URI
                );
                $cache_expires = $this->ask('cache expires (s)');
                $data = [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri' => $redirect_uri,
                    'account_type' => $account_type,
                    'expires' => $cache_expires,
                ];
                Setting::batchUpdate($data);
                $this->info('Configuration completed!');
                $this->warn('Please run this command again!');
            }
            exit('Already out!');
        }

        $authorizationUrl = Authorize::getInstance(setting('account_type'))->getAuthorizeUrl();

        $this->info("Please copy this link to your browser to open.\n{$authorizationUrl}");
        $code = $this->ask('Please enter the code obtained by the browser.');

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

        $this->info('Login Success!');
        $this->info('Account [' . one_account('account_email') . ']');
    }
}
