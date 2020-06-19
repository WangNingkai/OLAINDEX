<?php
/**
 * This file is part of the wangningkai/olaindex.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Console\Commands;

use App\Models\Account;
use App\Service\Constants;
use App\Service\OneDrive;
use Illuminate\Console\Command;
use Cache;

class RefreshCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:data {--id= : Account Id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh Data';

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
     * @throws \ErrorException
     */
    public function handle()
    {
        $this->info(Constants::LOGO);
        // 默认刷新主账号
        $account_id = (int)($this->option('id') ?? setting('primary_account', 0));
        $account = Account::find((int)$account_id);
        if (!$account) {
            exit('帐号不存在');
        }
        $hash = $account->hash_id;
        $root = array_get(setting($hash), 'root', '/');
        $root = trim($root, '/');
        $query = '/';
        $query = trim("{$root}/$query", '/');
        $this->getRecursive($account_id, $query);
    }

    /**
     * @param $account_id
     * @param $query
     *
     * @return mixed
     */
    public function getChildren($account_id, $query)
    {
        $response = (new OneDrive($account_id))->fetchList($query);
        if (array_key_exists('code', $response)) {
            exit(array_get($response, 'message', '404NotFound'));
        }
        Cache::put(
            "d:list:{$account_id}:{$query}",
            $response,
            setting('cache_expires')
        );
        return $response;
    }

    /**
     * @param $account_id
     * @param $query
     *
     * @return mixed
     */
    public function getItem($account_id, $query)
    {
        $response = (new OneDrive($account_id))->fetchItem($query);
        if (array_key_exists('code', $response)) {
            exit(array_get($response, 'message', '404NotFound'));
        }
        Cache::put(
            "d:item:{$account_id}:{$query}",
            $response,
            setting('cache_expires')
        );
        return $response;
    }

    /**
     * @param $account_id
     * @param $query
     *
     * @throws \ErrorException
     */
    public function getRecursive($account_id, $query): void
    {
        $query = trans_absolute_path($query);
        set_time_limit(0);
        $this->info($query);
        $list = $this->getChildren($account_id, $query);
        $this->getItem($account_id, $query);
        foreach ((array)$list as $item) {
            if (array_has($item, 'folder')) {
                $this->getRecursive($account_id, $query . $item['name']);
            }
        }
    }
}
