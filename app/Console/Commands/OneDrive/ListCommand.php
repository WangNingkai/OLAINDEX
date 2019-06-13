<?php

namespace App\Console\Commands\OneDrive;

use App\Service\CoreConstants;
use Illuminate\Console\Command;

class ListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'od:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List Command';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info(CoreConstants::LOGO);
        $output = <<<'EOF'
OLAINDEX Console Tool

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:

  od:cache     Cache Dir
  od:command   List Command
  od:cp        Copy Item
  od:direct    Create Direct Share Link
  od:download  Download File
  od:find      Find Items
  od:info      OneDriveGraph Info
  od:install   Install App
  od:login     Account Login
  od:logout    Account Logout
  od:ls        List Items
  od:mkdir     Create New Folder
  od:mv        Move Item
  od:offline   Remote download links to your drive
  od:password  Reset Password
  od:refresh   Refresh Token
  od:reset     Reset App
  od:rm        Delete Item
  od:share     ShareLink For File
  od:upload    UploadFile File
  od:whereis   Find The Item's Remote Path


EOF;

        $this->info($output);
    }
}
