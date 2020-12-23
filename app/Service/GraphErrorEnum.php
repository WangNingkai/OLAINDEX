<?php
/**
 * This file is part of the wangningkai/OLAINDEX.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service;


class GraphErrorEnum
{
    public const ERROR_DESC = [
        [
            'Code' => 'accessDenied',
            'Description' => '调用方没有执行该操作的权限。The caller doesn’t have permission to perform the action.',
        ],
        [
            'Code' => 'activityLimitReached',
            'Description' => '应用或用户已被限制。The app or user has been throttled.',
        ],
        [
            'Code' => 'extensionError',
            'Description' => '邮箱位于本地，并且 Exchange Server 不支持联合的 Microsoft Graph 请求，或者应用程序策略会阻止应用程序访问邮箱。The mailbox is located on premises and the Exchange server does not support federated Microsoft Graph requests, or an application policy prevents the application from accessing the mailbox.',
        ],
        [
            'Code' => 'generalException',
            'Description' => '发生未指定错误。An unspecified error has occurred.',
        ],
        [
            'Code' => 'invalidRange',
            'Description' => '指定的字节范围无效或不可用。The specified byte range is invalid or unavailable.',
        ],
        [
            'Code' => 'invalidRequest',
            'Description' => '该请求格式有误或不正确。The request is malformed or incorrect.',
        ],
        [
            'Code' => 'itemNotFound',
            'Description' => '找不到资源。The resource could not be found.',
        ],
        [
            'Code' => 'malwareDetected',
            'Description' => '所请求的资源中检测到恶意软件。Malware was detected in the requested resource.',
        ],
        [
            'Code' => 'nameAlreadyExists',
            'Description' => '指定的项目名称已存在。The specified item name already exists.',
        ],
        [
            'Code' => 'notAllowed',
            'Description' => '系统不允许执行此操作。The action is not allowed by the system.',
        ],
        [
            'Code' => 'notSupported',
            'Description' => '系统不支持该请求。The request is not supported by the system.',
        ],
        [
            'Code' => 'resourceModified',
            'Description' => '正在更新的资源自上次调用方读取时已进行了更改，通常是 eTag 不匹配。The resource being updated has changed since the caller last read it, usually an eTag mismatch.',
        ],
        [
            'Code' => 'resyncRequired',
            'Description' => '增量令牌将不再有效，并且应用必须重置同步状态。The delta token is no longer valid, and the app must reset the sync state.',
        ],
        [
            'Code' => 'serviceNotAvailable',
            'Description' => '服务不可用。过段时间后再次尝试请求。可能会有 Retry-After 标头。The service is not available. Try the request again after a delay. There may be a Retry-After header.',
        ],
        [
            'Code' => 'syncStateNotFound',
            'Description' => '找不到同步状态生成。The sync state generation is not found. 增量令牌已过期，必须重新进行数据同步。The delta token is expired and data must be synchronized again.',
        ],
        [
            'Code' => 'quotaLimitReached',
            'Description' => '用户已达到其配额限制。The user has reached their quota limit.',
        ],
        [
            'Code' => 'unauthenticated',
            'Description' => '调用方未进行身份验证。The caller is not authenticated.',
        ],
    ];

    public static function get($code)
    {
        $err = [];
        foreach (self::ERROR_DESC as $item) {
            $err[$item['Code']] = $item['Description'];
        }
        return $err[$code] ?? '';
    }
}
