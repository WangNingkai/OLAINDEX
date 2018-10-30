# OLAINDEX

✨ Another OneDrive Directory Index.

![OLAINDEX](https://i.loli.net/2018/10/11/5bbf40831f294.jpg)

此图来自 [如有乐享](https://51.ruyo.net/)，感谢推广

> 本项目受 Oneindex 启发，功能借鉴其思想，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提issue.

### 简介

项目地址：[https://github.com/WangNingkai/OLAINDEX](https://github.com/WangNingkai/OLAINDEX)

- 1.OneDrive 目录索引；
- 2.基于最新 PHP 框架 laravel5 搭建而成；
- 3.基于 bootswatch 响应式页面布局，适配PC、平板、手机；

### 功能
- OneDrive 目录查看索引，支持分页查看；
- 图床功能（国内不太稳低）；
- 支持文件路径一键复制、下载；
- 支持代码、图片、文本文件即时预览；
- 支持音视频播放（兼容大部分格式），视频播放采用Dplayer，音乐播放采用Aplayer；
- 支持文件夹加密（需管理员）；
- 支持文件上传（需管理员）；
- 支持readme/head说明文件添加/编辑/删除；
- 支持文件搜索（需管理员）；
- 支持自定义创建文件夹（需管理员）；
- 支持文件/文件夹删除（需管理员）；
- 后台基本管理，支持主题，预览设置等等（清理缓存后及时生效）（需管理员）。
- v2.0 全新路径显示（pathinfo）
- 添加看图相册
- 配置文件化，不再依赖数据库

### 演示链接

- 演示地址：[https://dev.ningkai.wang](https://dev.ningkai.wang)

### 安装使用

> 由于本项目基于Laravel 开发，新手建议查看 laravel 的环境搭建再进行部署。

**再次强调：希望看完wiki以确保操作环境达到要求！如出现错误提示，建议提供完整截图或相关完整报错错代码**

**帮助文档 ：**[Wiki](https://github.com/WangNingkai/OLAINDEX/wiki)

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
- 支持视频字幕，音频歌词
- 支持后台大文件上传，断点续传

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

