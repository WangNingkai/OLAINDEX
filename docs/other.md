## 常用命令行命令

```
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
  od:info      OneDrive Info
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

```


## 版本升级

```
git pull 
composer update -vvv # 无版本更新只需执行到此

chmod -R 755 storage # 补充，执行此处两条命令，确保缓存的写入权限，否则500
chown -R www:www *
```

## 后台登录

后台默认地址：`https://you.domain/admin`

初始后台密码： `12345678`;

也可通过命令行工具 `php artisan od:password` 生成一个新的8位数的密码

路由修改地址：`routes/web.php`

![route](https://i.loli.net/2018/10/27/5bd47191e7a90.png)

## 重置操作

*  `php artisan od:reset` 重置全部应用数据

*  `php artisan od:logout` 重置当前绑定账号数据

*  `php artisan od:login` 登陆账号

## 特殊文件功能

**不建议创建与特殊文件同名的文件夹或文件，否则会导致文件无法查看及下载**

` README.md `、`HEAD.md` 特殊文件使用

**在文件夹底部添加说明:**  
>在 onedrive 的文件夹中添加` README.md `文件，使用markdown语法。  

**在文件夹头部添加说明:**  
>在 onedrive 的文件夹中添加`HEAD.md` 文件，使用markdown语法。  


**3.2.1 最新版本不再依托 `.password` 文件加密，参考后台配置，使用新方式加密**

管理功能：支持添加/编辑/删除。

![action](https://i.loli.net/2018/10/27/5bd4718b74864.png)

## 文件加密与隐藏

### 加密

加密需要后台填写加密的文件或文件夹路径

添加路径需去除设置的显示的初始路径，填写加密路径以 `/` 开始

加密的目录使用英文 `,` 隔开；`:` 后是设置加密的密码，每个组加密使用 `|` 隔开

假设 初始路径为 `/share`

格式如： `/path1,/path2:password1|/path3,/path4:password2`  

则加密的路径对应 `OneDrive` 

`/share/path1` `/share/path2` 密码为 `password1`


`/share/path3` `/share/path4` 密码为 `password2`

### 隐藏

隐藏需要后台填写隐藏的文件或文件夹路径

添加路径需去除设置的显示的初始路径，填写加密路径以 `/` 开始

每个组隐藏使用 `|` 隔开

假设 初始路径为 `/share`

格式如： `/path1|/path3`  

则加密的路径对应 `OneDrive` 对应的隐藏目录为 `/share/path1` 和`/share/path3`

## 任务调度

推荐配置此任务调度器（可选）。后台定时刷新token和缓存，可适当加速页面的访问

```
* * * * * /php/bin/path/php /you/site/path/artisan schedule:run >> /dev/null 2>&1 &
```  
这个 Cron 为每分钟执行一次 Laravel 的命令行调度器，每30分钟刷新token，每10分钟刷新缓存。

## 队列刷新

更改根目录 `.env` 文件中 `PHP_PATH` 变量 添加服务器中php所在目录 可通过 `which php` 获取

推荐使用 `supervisor` 管理守护任务

```
php artisan queue:work database --queue=olaindex --tries=3
```  

## 缓存配置

默认 `OLAINDEX` 使用文件缓存，可以先修改为 `Laravel `支持的缓存类型，如 "apc", "array", "database", "file", "memcached", "redis"等，只需修改配置文件 `.env`。具体配置请参考 `laravel` 文档 [缓存系统](https://laravel-china.org/docs/laravel/5.7/cache/2278)

如使用 `redis`作为缓存的话，还需要安装 `predis` 包,手动执行 `composer require predis/predis` 

修改.env文件后需要执行 `php artisan config:cache` 确保配置生效

**配置文件**

```
# 缓存配置
CACHE_DRIVER=file # 这里是缓存类型

# 如配置redis 缓存需要填写以下配置
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```


## 主题切换

目前 OLAINDEX 存在两套主题，`bootswatch`(默认) 和 `mdui`，两套主题的切换方法是，更改根目录 `.env` 文件中 `THEME`变量，`default` 默认为 `bootswatch` 主题，`mdui` 为 `mdui` 主题。

切换主题后，请执行 `php artisan config:cache` 确保配置生效

## 页面排序

#### 支持字段 (limit|page|orderBy)

* 演示：https://dev.ningkai.wang/home?limit=3&page=2&orderBy=name,desc

* `limit={n}` 限制每页n条

* `page={n}` 第n页

* `orderBy={field},desc` 以field 排序，desc->倒序 asc-> 正序 （field 支持 id/name/size/lastModifiedDateTime）


## 防盗链

目前大部分网站采用的是判断 `referrer` 是否是当前域名或指定白名单域名下的url。而没有 `referrer` 的请求都会放行。
`referrer` 策略普及后，单从 `referrer` 判断防盗链的方法会失效，所以需要考虑其他的技术手段实现防盗链机制。


`Apache/Nginx` 禁止某些 `User Agent` 抓取网站

-  apache

修改 `.htaccess` 文件

```
RewriteEngine On
RewriteCond %{HTTP_USER_AGENT} (^$|FeedDemon|Indy Library|Alexa Toolbar|AskTbFXTV|AhrefsBot|CrawlDaddy|CoolpadWebkit|Java|Feedly|UniversalFeedParser|ApacheBench|Microsoft URL Control|Swiftbot|ZmEu|oBot|jaunty|Python-urllib|lightDeckReports Bot|YYSpider|DigExt|HttpClient|MJ12bot|heritrix|EasouSpider|Ezooms) [NC]
RewriteRule ^(.*)$ - [F]
```

-  nginx 

```
#禁止Scrapy等工具的抓取
if ($http_user_agent ~* (Scrapy|Curl|HttpClient)) {
     return 403;
}
#禁止指定UA及UA为空的访问
if ($http_user_agent ~* "FeedDemon|Indy Library|Alexa Toolbar|AskTbFXTV|AhrefsBot|CrawlDaddy|CoolpadWebkit|Java|Feedly|UniversalFeedParser|ApacheBench|Microsoft URL Control|Swiftbot|ZmEu|oBot|jaunty|Python-urllib|lightDeckReports Bot|YYSpider|DigExt|HttpClient|MJ12bot|heritrix|EasouSpider|Ezooms|^$" ) {
     return 403;             
}
#禁止非GET|HEAD|POST方式的抓取
if ($request_method !~ ^(GET|HEAD|POST)$) {
    return 403;
}
```

## 其他 

> 由于测试服务器性能有限，所以图片、文件的上传以及搜索的次数添加了路由请求次数限制，可以根据需求更改

**路由路径 ：** `routes/web.php`

