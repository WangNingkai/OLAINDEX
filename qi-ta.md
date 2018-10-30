# 其它



### 后台登录

后台地址：`https://you.domain/admin`

初始后台密码： `12345678`; 也可通过命令行工具 `php artisan reset:password` 生成一个新的8位数的密码

路由修改地址：`routes/web.php`

![image.png](https://i.loli.net/2018/10/27/5bd47191e7a90.png)

### 重置帐号

通过命令行工具 `php artisan reset:app` 重置全部数据，删除数据库数据

通过命令行工具 `php artisan reset:install` 重置 OneDrive 登陆账号

### 特殊文件功能

**不建议创建和以下同名的文件夹和文件，否则会导致文件无法查看下载**

`README.md`、`HEAD.md` 、 `.password` 、 `.deny`特殊文件使用

**在文件夹底部添加说明:**

> 在 onedrive 的文件夹中添加`README.md`文件，使用markdown语法。

**在文件夹头部添加说明:**

> 在 onedrive 的文件夹中添加`HEAD.md` 文件，使用markdown语法。

**加密文件夹:**

> 在 onedrive 的文件夹中添加`.password`文件，填入密码，密码不能为空。

**禁止访问文件夹:**

> 在 onedrive 的文件夹中添加`.deny`文件，该文件夹被禁止访问。

管理功能：支持添加/编辑/删除。直接在当前目录加密

![image.png](https://i.loli.net/2018/10/27/5bd4718b74864.png)

### 关于防盗链

目前大部分网站采用的是判断referrer是否是当前域名或指定白名单域名下的url。而没有referrer的请求都会放行。 referrer策略普及后，单从referrer判断防盗链的方法会失效，所以需要考虑其他的技术手段实现防盗链机制。

Apache/Nginx禁止某些User Agent抓取网站

**apache**

修改 `.htaccess` 文件

```text
RewriteEngine On
RewriteCond %{HTTP_USER_AGENT} (^$|FeedDemon|Indy Library|Alexa Toolbar|AskTbFXTV|AhrefsBot|CrawlDaddy|CoolpadWebkit|Java|Feedly|UniversalFeedParser|ApacheBench|Microsoft URL Control|Swiftbot|ZmEu|oBot|jaunty|Python-urllib|lightDeckReports Bot|YYSpider|DigExt|HttpClient|MJ12bot|heritrix|EasouSpider|Ezooms) [NC]
RewriteRule ^(.*)$ - [F]
```

**nginx**

```text
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

