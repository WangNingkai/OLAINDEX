OLAINDEX - Another OneDrive Directory Index
============================

> 本项目受 Oneindex 启发，功能借鉴其思想，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提issue.

![OLAINDEX](https://i.loli.net/2018/09/29/5baf23aa9d5ec.png)

### 简介

- 1.OneDrive 目录索引；
- 2.基于最新 PHP 框架 laravel5 搭建而成；
- 3.基于 bootswatch 响应式页面布局，适配PC、平板、手机；

### 功能
- 前台 OneDrive 目录索引；
- 代码、图片、文件预览；
- 文件一键复制、下载；
- 后台基本管理，支持主题，预览设置等等（清理缓存后及时生效）。
- 加密文件夹访问

### 链接
- 演示地址：[https://dev.ningkai.wang](https://dev.ningkai.wang)

### 安装使用

#### 基础安装

```bash
git clone -b master https://github.com/WangNingkai/OLAINDEX.git tmp 
mv tmp/.git . 
rm -rf tmp 
git reset --hard 
composer install -vvv 
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
chmod -R 755 storage/
chown -R www:www *
```

#### 申请 client_id、client_secret
__首次安装需要填写相关配置文件，申请 `client_id` 和 `client_secret`__

申请地址：https://apps.dev.microsoft.com/ 

申请完毕还有一个回调地址 `redirect_uri` 注意不要填错！

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

#### 数据库配置

数据库可以使用 `mysql` 或者 `sqlite` 等 建议使用 `sqlite` 方便迁移。

mysql 参考 laravel 文档配置

sqlite ：在 database 目录新建 database.sqlite 文件

`.env` 文件中，删除其他数据库配置，只需填写如下:

```markup
DB_CONNECTION=sqlite
```  

### 操作

后台密码 ： `12345678`;
也可通过命令行工具 `php artisan reset:password` 生成一个8位数的密码

### TODO

- 后台文件上传
- 后台目录创建
- 文件夹加密，密码访问
