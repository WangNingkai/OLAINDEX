# OLAINDEX

✨ Another OneDrive Directory Index.

![OLAINDEX](https://i.loli.net/2018/10/11/5bbf40831f294.jpg)

此图来自 [如有乐享](https://51.ruyo.net/)，感谢推广

> 本项目受 Oneindex 启发，功能借鉴其思想，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提issue.

## 简介

项目地址：[https://github.com/WangNingkai/OLAINDEX](https://github.com/WangNingkai/OLAINDEX)

- 1.OneDrive 目录索引；
- 2.后端采用最新 PHP 框架 laravel5 ；
- 3.前端采用 bootswatch 主题，支持PC、平板、手机自适应；

## 功能

- OneDrive 目录查看索引，支持分页查看；
- 支持世纪互联（一键切换）
- 支持图床（国内不太稳低）；
- 支持文件直链下载；
- 支持图片列表栏展示；
- 支持代码、图片、文本文件即时预览；
- 支持音视频播放（兼容大部分格式），视频播放采用 Plyr.js，音乐播放采用 Aplayer；
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

## 演示链接

- 演示地址：[https://dev.ningkai.wang](https://dev.ningkai.wang)

## 安装使用

> 由于本项目基于 Laravel 开发，新手建议查看 laravel 的环境要求再进行部署。

**再次强调：请参考 wiki 说明，确保操作环境达到要求！如出现错误提示，建议提供完整截图或相关完整报错代码**

**帮助文档 ：** [Wiki](https://github.com/WangNingkai/OLAINDEX/wiki)

## 更新升级

更新日志：[CHANGELOG](https://raw.githubusercontent.com/WangNingkai/OLAINDEX/master/CHANGELOG.log)

**版本升级：**

```
git pull 
composer install -vvv # 无版本更新只需执行到此
php artisan od:update # 跨版本更新
```


## 版本

- release: 稳定版

- master: 开发版

- test: 测试版（不稳定）

## TODO

- 共享目录支持
- 添加自定义视频字幕，音频歌词

## 捐赠

项目完全个人喜好开发，不提供免费定制。

打赏可以获得一次安装服务。

**打赏 ： https://pay.ningkai.wang**


### 附：

1.本软件仅供日常学习使用，不得用于任何商业用途；学习使用请遵守您所在国家的法律，任何非法行为由使用者本身承担。

2.如使用本应用，请保留底部版权，并分享给更多人，谢谢。


## 预览

![68747470733a2f2f692e6c6f6c692e6e65742f323031382f31312f31312f356265383238303063653862352e706e67.png](https://image.ningkai.wang/view/2018/11/14/0TGZrdf0/68747470733a2f2f692e6c6f6c692e6e65742f323031382f31312f31312f356265383238303063653862352e706e67.png)

![68747470733a2f2f692e6c6f6c692e6e65742f323031382f31312f31312f356265383238363530316235662e706e67.png](https://image.ningkai.wang/view/2018/11/14/rIzLPP33/68747470733a2f2f692e6c6f6c692e6e65742f323031382f31312f31312f356265383238363530316235662e706e67.png)
