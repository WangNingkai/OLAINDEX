# OLAINDEX

✨ Another OneDrive Directory Index.

![OLAINDEX](https://i.loli.net/2018/10/11/5bbf40831f294.jpg)

此图来自 [如有乐享](https://51.ruyo.net/)，感谢推广

> 本项目受 Oneindex 启发，功能借鉴其思想，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提issue.

### 简介

项目地址：[https://github.com/WangNingkai/OLAINDEX](https://github.com/WangNingkai/OLAINDEX)

- 1.OneDrive 目录索引；
- 2.后端采用最新 PHP 框架 laravel5 ；
- 3.前端采用 bootswatch 主题，支持PC、平板、手机自适应；

### 功能

- OneDrive 目录查看索引，支持分页查看；
- 支持图床（国内不太稳低）；
- 支持文件直链下载；
- 支持图片列表栏展示；
- 支持代码、图片、文本文件即时预览；
- 支持音视频播放（兼容大部分格式），视频播放采用Dplayer，音乐播放采用Aplayer；
- 支持文件夹加密；（需登陆）
- 支持文件上传；（需登陆）
- 支持readme/head说明文件添加/编辑/删除；（需登陆）
- 支持文件搜索；（需登陆）
- 支持自定义创建文件夹；（需登陆）
- 支持文件/文件夹删除；（需登陆）
- 支持 文件/文件夹的复制与移动；（需登陆）
- 支持离线下载功能（个人版）；（需登陆）
- 支持文件直连分享与删除；（需登陆）
- 命令行管理文件
- 配置文件化，不依赖数据库
- 后台基本设置管理，支持主题，文件预览设置等等（清理缓存后及时生效）（需登陆）。
- 支持通过 Heroku 搭建（亲测地址：`http://imwnk-olaindex.herokuapp.com`）。

### 演示链接

- 演示地址：[https://dev.ningkai.wang](https://dev.ningkai.wang)

### 安装使用

> 由于本项目基于 Laravel 开发，新手建议查看 laravel 的环境要求再进行部署。

**再次强调：请参考 wiki 说明，确保操作环境达到要求！如出现错误提示，建议提供完整截图或相关完整报错代码**

**帮助文档 ：** [Wiki](https://github.com/WangNingkai/OLAINDEX/wiki)

### 更新升级

- **2018.10.10 / v1.0**
安装流程优化；
- **2018.10.15 / v1.1**
加入防盗链
- **2018.10.16 / v1.2**
支持dash视频流播放（理论支持大多数视频）
- **2018.10.20 / v2.0**
全新路径显示(原先模式的图床路径会失效，请谨慎升级)
- **2018.10.28 / v3.0**
抛弃数据库，数据json格式保存
- **2018.11.02 ~ / v3.0（test）**
 - 重构部分接口逻辑，减少composer依赖
 - 添加离线下载（个人版）
 - 添加文件的基础操作（移动复制）
 - 添加文件的直连分享
 - 修改重置账户代码 查看命令行 `php artisan list od` 具体命令行操作帮助，例如： `php artisan od:upload -h`
 - 命令行上传文件，支持分片上传
 - 添加命令行操作支持

**从原先版本升级请自行执行以下代码：**

```
git pull
composer install -vvv
php artisan update:install
```


#### 版本

- release: 稳定版

- master: 开发版

- test: 测试版（不稳定）

### TODO

- 共享目录支持
- 添加自定义视频字幕，音频歌词

### 捐赠

提供免费搭建，也可以赏作者一杯咖啡钱。😊

##### [打赏](https://pay.ningkai.wang) ： https://pay.ningkai.wang

### 其他 

> 小弟的服务器性能有限，所以图片的上传和预览添加了路由请求次数限制，大家可以根据需求更改

**路由路径 ：** `routes/web.php`

![image.png](https://i.loli.net/2018/10/27/5bd473aa6bc75.png)

**附：** 本软件仅供日常学习使用，不得用于任何商业用途；学习使用请遵守您所在国家的法律，任何非法行为由使用者本身承担。

## 预览

![image.png](https://i.loli.net/2018/10/27/5bd473a992fa6.png)
![image.png](https://i.loli.net/2018/10/27/5bd473a7c6716.png)

