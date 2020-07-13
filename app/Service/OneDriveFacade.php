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
 * @method static OneDrive account(int $account_id)
 * @method static array|mixed|null fetchInfo()
 * @method static array|mixed|null fetchMe()
 * @method static array|mixed|null fetchList(string $query)
 * @method static array|mixed|null fetchListById(string $id)
 * @method static array|mixed|null fetchItem(string $query)
 * @method static array|mixed|null fetchItemById(string $id)
 * @method static array|mixed|null search(string $query, string $keyword)
 * @method static array|mixed|null copy(string $id, string $target_id, string $fileName)
 * @method static array|mixed|null move(string $id, string $target_id, string $fileName)
 * @method static array|mixed|null mkdir(string $fileName, string $target_id)
 * @method static array|mixed|null deleteItem(string $id, string $eTag)
 * @method static array|mixed|null fetchThumbnails(string $id, string $size)
 * @method static array|mixed|null upload(string $query, mixed $content)
 * @method static array|mixed|null uploadById(string $id, mixed $content)
 * @method static array|mixed|null id2Path(string $id)
 * @method static array|mixed|null path2Id(string $query)
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
