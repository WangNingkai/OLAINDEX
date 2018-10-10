OLAINDEX - Another OneDrive Directory Index
==========

> 本项目受 Oneindex 启发，功能借鉴其思想，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提issue.
> 由于本项目基于Laravel 开发，新手建议查看 laravel 的环境搭建再进行部署。


![list](https://share.imwnk.cn/item/origin/view/01FGBPEHT2TSRM4K4ZEVCJ3A2AVBOVKTOE)

![image](https://share.imwnk.cn/item/origin/view/01FGBPEHV3KII7GWXKMFHKGVTV5M6URPBW)

### 简介

- 1.OneDrive 目录索引；
- 2.基于最新 PHP 框架 laravel5 搭建而成；
- 3.基于 bootswatch 响应式页面布局，适配PC、平板、手机；

### 功能
- 前台 OneDrive 目录索引；
- 代码、图片、文件预览；
- 文件一键复制、下载；
- 后台基本管理，支持主题，预览设置等等（清理缓存后及时生效）；
- 加密文件夹访问；
- 图床功能；
- 后台文件上传。

### 演示链接
- 演示地址：[https://dev.ningkai.wang](https://dev.ningkai.wang)

### 安装使用

#### 服务器要求

- PHP >= 7.1.3
- OpenSSL PHP
- PHP PDO 扩展
- PHP Mbstring 扩展
- PHP Tokenizer 扩展
- PHP XML 扩展
- PHP Ctype 扩展
- PHP JSON 扩展


#### 基础安装

```bash
git clone -b release https://github.com/WangNingkai/OLAINDEX.git tmp 
mv tmp/.git . 
rm -rf tmp 
git reset --hard 
composer install -vvv 
cp .env.example .env
php artisan key:generate
touch database/database.sqlite # 这里演示的是sqlite数据库（强烈推荐，便于数据迁移）
php artisan migrate # 必须先创建数据库执行以下操作
php artisan db:seed
chmod -R 755 storage/
chown -R www:www *
```

#### 数据库配置

数据库可以使用 `mysql` 或者 `sqlite` 等 建议使用 `sqlite` 方便迁移。

mysql 参考 laravel 文档配置

sqlite ：在 database 目录新建 database.sqlite 文件

不熟悉laravel请注意，sqlite的话请在目录下的database里创建

或者在根目录直接执行

```bash
touch database/database.sqlite
```


`.env` 文件中，删除其他数据库配置，只需填写如下:

```markup
DB_CONNECTION=sqlite
```  

#### Web服务器配置

**Apache**

```
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

**Nginx**

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

#### 关于申请 client_id、client_secret
__首次安装需要填写相关配置文件，申请 `client_id` 和 `client_secret`__

申请地址：https://apps.dev.microsoft.com/ 

申请完毕，还有一个回调地址 `redirect_uri` 注意不要填错！

`redirect_uri` 请写 `https://you.domain/oauth` ，api配置和项目env配置请保持一致。

![添加应用](https://i.loli.net/2018/09/29/5baf1b04c30d7.png)
![注册名称](https://i.loli.net/2018/09/29/5baf1b05b58e3.png)
![获取client_id、 client_secret并填写回调地址](https://i.loli.net/2018/09/29/5baf1b06e42d6.png)
![勾选权限](https://i.loli.net/2018/09/29/5baf1b07db8f3.png)

获取完成后请到 `.env` 文件中填写。

```markup
GRAPH_CLIENT_ID=xxx
GRAPH_CLIENT_SECRET="xxx"
GRAPH_REDIRECT_URI=https://xxx
```

### 后台操作

初始后台密码 ： `12345678`;
也可通过命令行工具 `php artisan reset:password` 生成一个新的8位数的密码

### TODO

- 优化安装流程（包括client_id、client_secret的申请，这里感谢 @donwa 的指导）
- 后台目录创建与删除
- 文件夹加密，密码访问
- 后台大文件上传，断点续传等
- 更多视频以及字幕支持


> 小弟的服务器性能有限，所以图片的上传和预览添加了路由请求次数限制，大家可以根据需求更改

![image.png](https://image.ningkai.wang/item/origin/view/01HS36VADQV35WPMQ3AFHZ25AUTVCJIEVN)
