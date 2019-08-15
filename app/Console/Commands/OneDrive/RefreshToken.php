<?php

namespace App\Console\Commands\OneDrive;

use App\Http\Controllers\OauthController;
use App\Models\OneDrive;

class RefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:refresh 
                            {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Token';

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
        if ($this->option('all')) {
            $onedrives = OneDrive::get();
        } else {
            getDefaultOneDriveAccount($this->option('one_drive_id'));
            $onedrives = collect([
                app('onedrive')
            ]);
        }

        foreach ($onedrives as $onedrive) {
            app()->instance('onedrive', $onedrive);
            $expires = app('onedrive')->access_token_expires;
            $hasExpired = $expires - time() <= 0 ? true : false;

            if (!$hasExpired) {
                continue;
            } else {
                $oauth = new OauthController();
                $res = json_decode($oauth->refreshToken(false), true);

                if (!in_array($res['code'], [200, 400])) {
                    exit('Refresh Token Error!');
                }
            }
        }
    }
}
