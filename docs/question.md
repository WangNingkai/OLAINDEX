## 加密资源问题

**注意：必须退出后台管理员账号才可以生效，否则无法测试加密**

原先的加密文件形式为目录创建 `.password` 文件， **3.2.1** 版本后，此种方式已经**失效**，加密的方式改为**路径模式**

受影响的路由包括（文件列表、下载、文件展示、图片查看）

加密设置请严格按照如下格式填写：

```
/路径1,/路径2:密码|
/路径3,/路径4,/路径5:密码|
/路径6:密码|
```

**路径与路径之间空格隔开，最后为密码，每组加密以英文逗号隔开。加密层级从最底层到最高层**

## 后台日志

从 `3.2.1` 版本过后（如未升级，请升级后在进行反馈，升级命令 `php artisan od:update`），后台添加调试日志，方便发现问题并进行调试。

如发现应用出现错误，建议打开调试日志查看，并截图到issues 反馈问题。

![image.png](https://i.loli.net/2018/12/14/5c134165acc2c.png) 

此处 404 代表接口返回的数据为空。

其它报错根据实际情况，如出现错误建议提供完整报错信息，并附上截图。

## 500报错

- 请确保每个步骤没有出现报错

- 确保执行 `chmod` 和 `chown` 此处两个命令，解决目录权限问题

- 不知情的情况，请勿修改文件

- 其他情况，请在issue中提交最新log报错代码，位置 `storage/logs/laravel.log`\

如
```
[2018-12-05 11:01:29] local.ERROR: curl_setopt() expects parameter 2 to be integer, string given {"exception":"[object] (ErrorException(code: 0): curl_setopt() expects parameter 2 to be integer, string given at D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\php-curl-class\\php-curl-class\\src\\Curl\\Curl.php:1004)
[stacktrace]
#0 [internal function]: Illuminate\\Foundation\\Bootstrap\\HandleExceptions->handleError(2, 'curl_setopt() e...', 'D:\\\\Work\\\\my\\\\my-p...', 1004, Array)
#1 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\php-curl-class\\php-curl-class\\src\\Curl\\Curl.php(1004): curl_setopt(Resource id #422, 'CURLOPT_CUSTOMR...', 'GET')
#2 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\php-curl-class\\php-curl-class\\src\\Curl\\Curl.php(1024): Curl\\Curl->setOpt('CURLOPT_CUSTOMR...', 'GET')
#3 D:\\Work\\my\\my-project\\OLAINDEX\\app\\Helpers\\GraphRequest.php(85): Curl\\Curl->setOpts(Array)
#4 D:\\Work\\my\\my-project\\OLAINDEX\\app\\Console\\Commands\\Test.php(42): App\\Helpers\\GraphRequest->request('GET', '/me')
#5 [internal function]: App\\Console\\Commands\\Test->handle()
#6 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(29): call_user_func_array(Array, Array)
#7 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(87): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()
#8 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(31): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))
#9 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(572): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)
#10 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(183): Illuminate\\Container\\Container->call(Array)
#11 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\symfony\\console\\Command\\Command.php(255): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))
#12 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(170): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))
#13 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\symfony\\console\\Application.php(901): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))
#14 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\symfony\\console\\Application.php(262): Symfony\\Component\\Console\\Application->doRunCommand(Object(App\\Console\\Commands\\Test), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))
#15 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\symfony\\console\\Application.php(145): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))
#16 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Application.php(89): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))
#17 D:\\Work\\my\\my-project\\OLAINDEX\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(122): Illuminate\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))
#18 D:\\Work\\my\\my-project\\OLAINDEX\\artisan(37): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))
#19 {main}
"} 
```

## 依赖安装失败

- `composer` 未安装或安装有问题，建议卸载重新安装

- 相关PHP 扩展未安装 （通常为fileinfo扩展）
![fileinfo-error.png](https://i.loli.net/2018/11/30/5c008b32bc7b4.png)

- 相关 PHP 禁用函数未开启 参考环境配置

**请按照环境需求，来执行完善环境**

## 权限问题

执行初始化安装后，请确保缓存的权限，执行命令 `chmod -R 777 storage` 否则无法写入缓存。

同时每次更新后，执行权限命令。

## 防盗链问题

防盗链包括** 站点防盗链**设置和 **web服务器防盗链**（这里是nginx）。出现此问题，请确保配置一致。

## 404报错

- 防盗链问题
- 图片、视频显示 `The requested content cannot be loaded.Please try again later.` 问题
- `PHP` 文件无法查看报错问题

**解决方法详见下图：**

注释相关参数

![nginx.conf](https://i.loli.net/2018/10/27/5bd472feb50a6.png)

## 搜索结果不匹配

微软用于搜索的接口查询字段的值可以跨多个字段匹配，包括文件名，元数据和文件内容，所以会导致搜索结果不准确。
注意：在OneDrive for Business和SharePoint中，在文件夹层次结构下搜索时，可能不会在结果中返回图像文件类型。
所以搜索结果仅供参考。

## 目录文件数目过多，出现 500 问题

根据接口的反馈，如果结果超出默认限制大小（200个项目），则会在响应中返回 `@ odata.nextLink` 属性，以指示可用的项目更多，并为下一页项目提供请求URL。

本项目是递归索引全部的项目，如果文件过多可能会由于网络原因传输中断或者其他原因，无法获取数据。

## 特殊文件显示问题`.password/reademe.md/head.md`

默认用户登录后台，可显示 OneDrive 中全部的文件，退出登录则不再显示。

## 世纪互联版绑定成功无法查看文件，显示 `0B` 问题

这里需要修改你申请的世纪互联APP的权限，放开读取文件和个人信息的权限。

## 文本文件无法渲染问题

目前文本文件支持的默认编码为 `utf-8`，请确保文件为此编码形式，否则会出现乱码问题

## open_basedir 类问题

这类问题通常这样报错
```
Warning: require(): open_basedir restriction in effect. File(/www/wwwroot/xxx/public/../vendor/autoload.php) is not within the allowed path(s): (/www/wwwroot/xxx/public/:/tmp/:/proc/) in /www/wwwroot/xxx/public/index.php on line 24
```
请自行google防跨目录设置。如果使用的军哥的lnmp一键安装，建议参考这篇文章：

[https://lnmp.org/faq/lnmp-vhost-add-howto.html#user.ini](https://lnmp.org/faq/lnmp-vhost-add-howto.html#user.ini)

## 图片视频 404 问题

访问图片 `nginx` 通常会返回 `404` 页面。

建议注释下图内容同时关闭防盗链或者加入白名单：

![image.png](https://i.loli.net/2018/10/27/5bd472fe4d310.png)

## 数据库迁移问题

从 3.0 版本开始，不在集成数据库文件，使用json文件作为配置文件。强烈建议升级！！！

从 3.2 版本开始，不在兼容旧版。强烈建议升级！！！

## `.env` 文件报错问题

通常报错显示：`The environment file is invalid: Dotenv values containing spaces must be surrounded by quotes.`

由于安装时，根目录下 `.env` 设置错误

所以确保 `.env` 文件的正确性

```
APP_NAME=OLAINDEX
APP_ENV=local
APP_KEY=pq0qfj9mWCNSrnlC6c69SD5vILJkp9YU
APP_DEBUG=true
APP_URL=http://localhost:8000 #通常是此处错误

LOG_CHANNEL=stack
# 缓存配置
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
# redis配置
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

```

![image.png](https://i.loli.net/2018/11/29/5bff2fd918ae0.png)
