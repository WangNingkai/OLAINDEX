# OLAINDEX

✨ Another OneDrive Directory Index.

[![Latest Stable Version](https://poser.pugx.org/wangningkai/olaindex/v/stable)](https://packagist.org/packages/wangningkai/olaindex)
[![Latest Unstable Version](https://poser.pugx.org/wangningkai/olaindex/v/unstable)](//packagist.org/packages/wangningkai/olaindex)
[![GitHub stars](https://img.shields.io/github/stars/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/network)
[![GitHub license](https://img.shields.io/github/license/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/blob/master/LICENSE)
![visitors](https://visitor-badge.laobi.icu/badge?page_id=WangNingkai.OLAINDEX)


> 👋 本项目受 oneindex 启发，借鉴其部分功能，在这里感谢。 项目持续开发，会加入更多功能，欢迎大家提交 issue.

## 简介

一款 `OneDrive` 目录文件索引应用，基于优雅的 `PHP` 框架 `Laravel` 搭建，并通过 `Microsoft Graph` 接口获取数据展示，支持多类型帐号登录，多种主题显示，简单而强大。

## 功能

- OneDrive 文件目录索引
- 支持多种资源即时预览
- 支持多账号

## 项目地址

- [https://github.com/WangNingkai/OLAINDEX](https://github.com/WangNingkai/OLAINDEX)

## 演示地址

- [https://demo.olaindex.com](https://demo.olaindex.com)

## 预览

![预览](https://ojpoc641y.qnssl.com/FpR4_obUhswLJXCEBgKOV4Pz7qg3.png)

## 安装

> 本项目基于 Laravel 开发，新手建议查看 laravel 的环境要求再进行部署。

**强调：文档中包含常见的错误与解决，以及安装。如出现错误提示，建议到issues提供完整截图或相关完整报错代码，并仔细参考文档说明，进行修改！**

[查看帮助文档](https://wangningkai.github.io/OLAINDEX)

## 更新升级

### 版本更新

```bash
git pull 
composer install # 安装依赖更新包

chmod -R 755 storage # 注意！！！确保缓存目录具有读写权限，否则500
chown -R www:www * # 确保目录权属
```

### 更新日志

本次更新进行部分重构，不再兼容老版本，请删除原先代码重新拉取部署更新

**2020.12 v6.0**

- 简化功能
- 修复已知问题
- 添加目录搜索功能（不调用接口）
- 优化加密、隐藏功能
- 更完善的路径模式
- 新增短链模式，方便分享
- 资源预加载，加速访问

## 问题反馈

> 进行任何操作前请先阅读 [《提问的智慧》](https://github.com/ruby-china/How-To-Ask-Questions-The-Smart-Way/blob/master/README-zh_CN.md)

当前获取帮助有三种方式：

1. 通过 [GitHub issue](https://github.com/WangNingkai/OLAINDEX/issues) 提交问题（仅限问题反馈）
2. 通过 [个人博客](https://imwnk.cn) 评论留言 或者 [关于&反馈](https://olaindex.ningkai.wang)  页面最下角留言
3. 通过个人邮箱联系（每周不定时查看）

无论采用哪种方式，请务必注意自己的言行举止，尊重他人，遵守最基本的社区行为规范。 在求(伸)助(手)前请确保已经仔细 [Github Wiki](https://github.com/WangNingkai/OLAINDEX/wiki)
内的所有说明。 使用 [GitHub issue](https://github.com/WangNingkai/OLAINDEX/issues) 提交问题时请确保提供信息完整准确，否则不予跟进。

使用 [GitHub discussions](https://github.com/WangNingkai/OLAINDEX/discussions)

Blog [https://imwnk.cn](https://imwnk.cn)

Email [i@ningkai.wang](mailto:i@ningkai.wang)

### 其他

1. 本项目同样存在命令行版本，包含基本的显示下载，功能与此版本一致。项目地址（能力有限，暂不更新） [OLAINDEX-CMD](https://git.io/OLACMD)

2. 本软件仅供日常学习使用，不得用于任何商业用途；学习使用请遵守您所在国家的法律，任何非法行为由使用者本身承担。

3. 如使用本应用，请尽量保留底部版权，并分享给更多人，感谢。

## :sparkling_heart: 支持这个项目

我尽己所能地进行开源，并且我尽量回复每个在使用项目时需要帮助的人。很明显，这需要时间，但你可以免费享受这些。

然而, 如果你正在使用这个项目并感觉良好，或只是想要支持我继续开发，你可以通过如下方式：

- Star 并 分享这个项目 :rocket:
- [![paypal.me/wangningkai](https://ionicabizau.github.io/badges/paypal.svg)](https://www.paypal.me/wangningkai) - 你可以通过
  PayPal 一次性捐款. 我多半会买一杯 咖啡 茶. :tea:
- [Wechat & AliPay](https://pay.ningkai.wang)

谢谢! :heart:

## License

The OLAINDEX is open-source software licensed under the MIT license.

---

欢迎贡献哦! <3

Made with ❤️ and PHP.
