# OLAINDEX

✨ Another OneDrive Directory Index.

[![Latest Stable Version](https://poser.pugx.org/wangningkai/olaindex/v/stable)](https://packagist.org/packages/wangningkai/olaindex)
[![GitHub stars](https://img.shields.io/github/stars/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/network)
[![GitHub license](https://img.shields.io/github/license/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/blob/master/LICENSE)

![OLAINDEX](https://i.loli.net/2018/10/11/5bbf40831f294.jpg)

此图来自 [如有乐享](https://51.ruyo.net/)，感谢推广

> 👋 本项目受 Oneindex 启发，借鉴其部分功能，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提交 issue.

## 简介

项目地址：[https://github.com/WangNingkai/OLAINDEX](https://github.com/WangNingkai/OLAINDEX)

OLAINDEX-CMD [https://github.com/WangNingkai/OLAINDEX-CMD](https://github.com/WangNingkai/OLAINDEX-CMD)

- 1.`OneDrive` 目录索引；
- 2.后端采用最新 `PHP` 框架 `laravel5` ；
- 3.前端采用 `bootswatch` 主题，支持PC、平板、手机自适应；
- 4.资源接口来源于 `Microsoft Graph`。

## 功能

- OneDrive 目录查看索引分页查看；
- 支持代码、图片、文本文件即时预览、图片列表栏展示；
- 支持音视频播放（兼容大部分格式），视频播放采用 Plyr.js，音乐播放采用 Aplayer；
- 支持自定义创建文件夹、文件夹加密、文件/文件夹删除、文件/文件夹的复制与移动；
- 支持文件搜索、文件上传、文件直链分享与删除、文件直链一键下载；
- 支持管理 readme/head 说明文件；
- 支持图床（国内不太稳低）；
- 支持命令行操作；
- 支持文件离线下载（个人版）；
- 后台基本显示管理，多主题管理，文件预览管理等等（清理缓存后及时生效）；
- 支持世纪互联（一键切换）；
- 配置文件化，不依赖数据库；
- 支持 Heroku 搭建（亲测地址：`http://imwnk-olaindex.herokuapp.com`）。

**注：** 部分功能需登录。

## 演示链接

- 演示地址：[https://dev.ningkai.wang](https://dev.ningkai.wang)

## 安装使用

> 本项目基于 Laravel 开发，新手建议查看 laravel 的环境要求再进行部署。

**再次强调：请参考 wiki 说明，确保操作环境达到要求！如出现错误提示，建议提供完整截图或相关完整报错代码**

**帮助文档 ：** [Wiki](https://github.com/WangNingkai/OLAINDEX/wiki)

## 更新升级

**更新日志：** [CHANGELOG](https://github.com/WangNingkai/OLAINDEX/blob/master/CHANGELOG.md)

**版本升级：**

```
git pull 
composer install -vvv # 无版本更新只需执行到此
php artisan od:update # 跨版本更新
chmod -R 755 storage # 补充，保证缓存的写入权限，否则500
chown -R www:www *
```

## 分支说明

- release: 稳定版

- master: 开发版

- test: 测试版（不稳定）

## TODO

- 共享目录支持
- 添加自定义视频字幕，音频歌词

## 捐赠

项目完全个人喜好开发，不提供免费定制。

打赏可以获得一次安装服务。

**打赏 ： [https://pay.ningkai.wang](https://pay.ningkai.wang)**

## 作者

Blog : [https://imwnk.cn](https://imwnk.cn)

Email : [imwnk@live.com](mailto:imwnk@live.com)

### 附：

1.本软件仅供日常学习使用，不得用于任何商业用途；学习使用请遵守您所在国家的法律，任何非法行为由使用者本身承担。

2.如使用本应用，请保留底部版权，并分享给更多人，谢谢。


## 预览

![68747470733a2f2f692e6c6f6c692e6e65742f323031382f31312f31312f356265383238303063653862352e706e67.png](https://img02.sogoucdn.com/app/a/100520146/cd2311797c818d8f37bd5a7474080be4)
![68747470733a2f2f692e6c6f6c692e6e65742f323031382f31312f31312f356265383238363530316235662e706e67.png](https://img03.sogoucdn.com/app/a/100520146/f5ba120d0c44e7d57c8ff076da20cb9f)
