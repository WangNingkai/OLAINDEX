**✨ Another OneDrive Directory Index.**

## 简介

一款 `OneDrive` 目录文件索引应用，基于优雅的 `PHP` 框架 `Laravel5.7` 搭建，并通过 `Microsoft Graph` 接口获取数据展示，支持多类型帐号登录，多种主题显示，简单而强大。

## 项目地址

- [https://github.com/WangNingkai/OLAINDEX](https://git.io/OLAINDEX)

## 预览

![预览](https://i.loli.net/2018/11/11/5be82800ce8b5.png)
 
## 演示地址

- [https://dev.ningkai.wang](https://dev.ningkai.wang)

## 功能

- OneDrive 目录查看索引分页查看；
- 支持代码、图片、文本文件即时预览、图片列表栏展示；
- 支持音视频播放（兼容大部分格式），视频播放采用 Dplayer.js，音乐播放采用 Aplayer；
- 支持自定义创建文件夹、文件夹加密、文件/文件夹删除、文件/文件夹的复制与移动；
- 支持文件搜索、文件上传、文件直链分享与删除、文件直链一键下载；
- 支持管理 readme/head 说明文件；
- 支持图床；
- 支持命令行操作；
- 支持文件离线下载（个人版）；
- 后台基本显示管理，多主题管理，文件预览管理等等（清理缓存后及时生效）；
- 支持世纪互联；
- 支持多种缓存系统（Redis、Memcached等）；
- 支持 Heroku 搭建（亲测地址：`http://imwnk-olaindex.herokuapp.com`）。
- 支持第三方接口

**注：** 部分功能需登录。

## 更新

本次更新进行部分重构，不再兼容老版本，请删除原先代码重新拉取部署更新

v4.0

1.搜索功能增强(前台可搜索、后台设置搜索频次)
2.目录额外功能优化（文件隐藏、加密）
3.文件排序功能加强
4.静态资源CDN更换
5.路径优化（可自定义兼容模式）
6.后台刷新任务执行
7.整合部分命令
8.上传、搜索频次设置
9.更换信息存储为数据库存储
10.添加第三方接口

## 安装

> 本项目基于 Laravel 开发，新手建议查看 laravel 的环境要求再进行部署。

**强调：Wiki 中包含常见的错误与解决，以及安装。如出现错误提示，建议到issues提供完整截图或相关完整报错代码，并仔细参考 wiki 说明，进行修改！**

**帮助文档 ：**

[Github Wiki](https://github.com/WangNingkai/OLAINDEX/wiki)

## 更新升级

**更新日志：** [CHANGELOG](https://raw.githubusercontent.com/WangNingkai/OLAINDEX/master/CHANGELOG.md)

**版本升级：**

```
git pull 
composer install -vvv # 无版本更新只需执行到此（同时执行最后两条权限命令）

php artisan od:update # 跨版本更新

chmod -R 755 storage # 补充，保证缓存的写入权限，否则500
chown -R www:www *
```

## 分支说明

- release: 稳定版

- master: 开发版

- test: 测试版（不推荐使用）

## 捐赠

项目开发纯属个人爱好，如果你喜欢此项目，欢迎捐赠。

同时捐赠可以获得一次安装技术服务。

另可根据需求提供付费定制服务。

**捐赠 ： [https://pay.ningkai.wang](https://pay.ningkai.wang)**

## 问题反馈

> 进行任何操作前请先阅读 [《提问的智慧》](https://github.com/ruby-china/How-To-Ask-Questions-The-Smart-Way/blob/master/README-zh_CN.md)

当前获取帮助有三种方式：

1. 通过 [GitHub issue](https://github.com/WangNingkai/OLAINDEX/issues) 提交问题（仅限问题反馈）
2. 通过 [个人博客](https://imwnk.cn) 评论留言 或者 [关于&反馈](https://olaindex.ningkai.wang)  页面最下角留言
3. 通过个人邮箱联系（每周不定时查看）

无论采用哪种方式，请务必注意自己的言行举止，尊重他人，遵守最基本的社区行为规范。
在求(伸)助(手)前请确保已经仔细 [Github Wiki](https://github.com/WangNingkai/OLAINDEX/wiki) 内的所有说明。
使用 [GitHub issue](https://github.com/WangNingkai/OLAINDEX/issues) 提交问题时请确保提供信息完整准确，否则不予跟进。

Blog : [https://imwnk.cn](https://imwnk.cn)

Email : [imwnk@live.com](mailto:imwnk@live.com)

## 其他

1. 本项目同样存在命令行版本，包含基本的显示下载，功能与此版本一致。项目地址 [OLAINDEX-CMD](https://git.io/OLACMD)

2. 本软件仅供日常学习使用，不得用于任何商业用途；学习使用请遵守您所在国家的法律，任何非法行为由使用者本身承担。

3. 如使用本应用，请保留底部版权，并分享给更多人，谢谢。

---

## License
The OLAINDEX is open-source software licensed under the MIT license.
