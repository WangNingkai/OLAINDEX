OLAINDEX
===

## Another OneDrive Directory Index

![OLAINDEX](https://i.loli.net/2018/10/11/5bbf40831f294.jpg)



此图来自 [如有乐享](https://51.ruyo.net/)，感谢推广

> 本项目受 Oneindex 启发，功能借鉴其思想，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提issue.


![1.jpg](https://image.ningkai.wang/item/origin/view/01HS36VAGA2Q2RP3ZSKVGYAZW2IMBBTB3N)
![2.jpg](https://image.ningkai.wang/item/origin/view/01HS36VADJ5MEMI6RGVVCJ3SJBBKRWOSRS)

### 简介

- 1.OneDrive 目录索引；
- 2.基于最新 PHP 框架 laravel5 搭建而成；
- 3.基于 bootswatch 响应式页面布局，适配PC、平板、手机；

### 功能
- OneDrive 目录查看索引，支持分页查看；
- 代码、图片、文本文件即时预览；
- 文件l路径一键复制、下载；
- 后台基本管理，支持主题，预览设置等等（清理缓存后及时生效）（需管理员）；
- 文件夹加密（需管理员）；
- 图床功能（国内不太稳低）；
- 文件上传（需管理员）；
- readme/head说明文件添加/编辑/删除；
- 视频播放器采用Dplayer，音乐播放器采用Aplayer；
- 文件搜索（需管理员）；
- 自定义创建文件夹（需管理员）。

### 演示链接

- 演示地址：[https://dev.ningkai.wang](https://dev.ningkai.wang)

### 安装使用

> 由于本项目基于Laravel 开发，新手建议查看 laravel 的环境搭建再进行部署。

**帮助文档 ：**[Wiki](https://github.com/WangNingkai/OLAINDEX/wiki)

### 更新升级

- **2018.10.10 / v1.0**
安装流程优化；
- **2018.10.15 / v1.1**
加入防盗链
- **2018.10.16 / v1.2**
支持dash视频流播放（理论支持大多数视频）

**从原先版本升级请自行执行以下代码：**

```
git pull
composer install -vvv
php artisan update:install
```


#### 版本

- release: 测试相对稳定代码

- master: 开发最新代码

### TODO

- 视频（已加入avi等格式的支持）字幕，音频歌词支持
- 后台大文件上传，断点续传
- 目录友好显示
- 后台文本文件的编辑
- 共享目录支持
- 搜索功能（已加入文件搜索）

### 其他 

> 小弟的服务器性能有限，所以图片的上传和预览添加了路由请求次数限制，大家可以根据需求更改

![route](https://image.ningkai.wang/item/origin/view/01HS36VADQV35WPMQ3AFHZ25AUTVCJIEVN)

![routes/web.php](https://image.ningkai.wang/item/origin/view/01HS36VAARRNW3WPF6LJDYNYCE5CSPRDCP)


**附：** 本软件仅供日常学习使用，不得用于任何商业用途；学习使用请遵守您所在国家的法律，任何非法行为由使用者本身承担。
