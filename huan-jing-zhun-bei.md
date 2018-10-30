# 环境准备

### 代理搭建

本人提供有偿代理搭建环境。由于环境搭建比较费时费力，所以收取一定的费用，打赏费用随个人意愿。[打赏](https://pay.ningkai.wang) ： [https://pay.ningkai.wang](https://pay.ningkai.wang)

### 服务器环境要求

**首先确保服务器满足以下要求**

* PHP &gt;= 7.1.3
* OpenSSL PHP
* PHP PDO 扩展
* PHP Mbstring 扩展
* PHP Tokenizer 扩展
* PHP XML 扩展
* PHP Ctype 扩展
* PHP JSON 扩展
* PHP Fileinfo 扩展 \*

**Laravel 文件系统的要求，为保证成功安装，建议将** `PHP Fileinfo 扩展` **一并安装**

推荐使用 oneinstack 安装php环境 [https://oneinstack.com/auto](https://oneinstack.com/auto)

可以根据需要安装扩展

最低安装标准 `nginx+php`

![image.png](https://i.loli.net/2018/10/27/5bd46cbf4efe3.png)

使用一下命令即可安装 `nginx+php`

```text
wget http://mirrors.linuxeye.com/oneinstack-full.tar.gz && tar xzf oneinstack-full.tar.gz && ./oneinstack/install.sh --nginx_option 1 --php_option 7 --phpcache_option 1 --reboot
```

**注意：** laravel程序安装需要开启禁用的两个方法，步骤如下：

oneinstack php安装路径 `/usr/local/php/etc/php.ini`

```text
1、进入php.ini文件，找到disable_function=，删除proc_open函数，即可。
2、进入php.ini文件，找到disable_function=，删除proc_get_status函数，即可。
3、sudo service php-fpm restart # 重启 php 进程
```

另外使用composer包管理 需要下载 composer 并且全局处理，步骤如下：

```text
1、curl -sS https://getcomposer.org/installer | php  
2、mv /tmp/composer.phar /usr/local/bin/composer 
3、 composer config -g repo.packagist composer https://packagist.laravel-china.org # 更换源为国内源，国外服务器可忽略此步骤
```

具体Laravel安装参考:[https://segmentfault.com/a/1190000010157731](https://segmentfault.com/a/1190000010157731)

