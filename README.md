# OLAINDEX

✨ Another OneDrive Directory Index.

[![Latest Stable Version](https://poser.pugx.org/wangningkai/olaindex/v/stable)](https://packagist.org/packages/wangningkai/olaindex)
[![Latest Unstable Version](https://poser.pugx.org/wangningkai/olaindex/v/unstable)](//packagist.org/packages/wangningkai/olaindex)
[![GitHub stars](https://img.shields.io/github/stars/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/network)
[![GitHub license](https://img.shields.io/github/license/WangNingkai/OLAINDEX.svg?style=flat-square)](https://github.com/WangNingkai/OLAINDEX/blob/master/LICENSE)
![visitor badge](https://visitor-badge.glitch.me/badge?page_id=WangNingkai.OLAINDEX)
![Lint Code Base](https://github.com/WangNingkai/OLAINDEX/workflows/Lint%20Code%20Base/badge.svg?branch=5.0)
![Laravel](https://github.com/WangNingkai/OLAINDEX/workflows/Laravel/badge.svg?branch=5.0)
    
> **[中文README](./README_CN.md)**

## Introduction

A simple, full-featured OneDrive directory index web app, Built on top of the Laravel framework. Using the `Microsoft Graph API` present content,support multiple accounts,multiple themes.

## Features

- OneDrive directory index
- Support for different types of resources preview
- support multiple accounts

## Project address

- [https://github.com/WangNingkai/OLAINDEX](https://github.com/WangNingkai/OLAINDEX)

## Demo

- [https://share.imwnk.cn](https://share.imwnk.cn)

## Preview

![Preview](https://ojpoc641y.qnssl.com/FpR4_obUhswLJXCEBgKOV4Pz7qg3.png)

## Installation

> This project is base of the Laravel framework，Please refer to the relevant documentation.

[Documentation](https://wangningkai.github.io/OLAINDEX)


```bash
cd web-project
git clone https://github.com/WangNingkai/OLAINDEX.git tmp 
mv tmp/.git . 
rm -rf tmp 
git reset --hard 
composer install -vvv  # Install Dependencies
chmod -R 777 storage # Important！！！Ensure that the cache directory has read and write permissions
chown -R www:www * # this 'www' refer to the serve user group
composer run install-app # installation app

```


### Branch

- release - Stable version

- master - Latest version

- test -  Not Recommend

## Bug report

> Please read before bug report[《How To Ask Questions The Smart Way》](http://www.catb.org/~esr/faqs/smart-questions.html)

3 way to bug report：

1. [GitHub issue](https://github.com/WangNingkai/OLAINDEX/issues) 
2. [Blog](https://imwnk.cn)
3. [i@ningkai.wang](mailto:i@ningkai.wang)

### Othre

1. OLAINDEX Command Line Version.Project Address [OLAINDEX-CMD](https://git.io/OLACMD)

2. This software is only for daily study and should not be used for any commercial purposes. Please abide by the laws of your country. Any illegal behavior is the responsibility of the user.

3. If you use this application, please try to keep the copyright at the bottom and share it with more people.Thanks.

## Support

**Support Address ： [https://pay.ningkai.wang](https://pay.ningkai.wang)**

---

## License
The OLAINDEX is open-source software licensed under the MIT license.
