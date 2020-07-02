<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;


use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Service\OneDrive account(int $account_id)
 * @method static array fetchInfo()
 * @method static array fetchMe()
 * @method static array fetchList(string $query)
 * @method static array fetchListById(string $id)
 * @method static array fetchItem(string $query)
 * @method static array fetchItemById(string $id)
 * @method static array search(string $query, string $keyword)
 * @method static array copy(string $id, string $target_id, string $fileName)
 * @method static array move(string $id, string $target_id, string $fileName)
 * @method static array mkdir(string $fileName, string $target_id)
 * @method static array deleteItem(string $id, string $eTag)
 * @method static array fetchThumbnails(string $id, string $size)
 * @method static array upload(string $query, mixed $content)
 * @method static array uploadById(string $id, mixed $content)
 * @method static array id2Path(string $id)
 * @method static array path2Id(string $query)
 * @see \App\Service\OneDrive
 */
class OneDriveFacade extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'OneDrive';
    }
}
