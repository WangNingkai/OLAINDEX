**PHP 扩展要求**

- PHP >= 7.4
- PHP OpenSSL 扩展
- PHP PDO 扩展
- PHP Mbstring 扩展
- PHP Tokenizer 扩展
- PHP XML 扩展
- PHP Ctype 扩展
- PHP JSON 扩展
- PHP BCMath 扩展
- PHP Fileinfo 扩展 *

**Laravel 文件系统模块要求，为保证成功安装，建议安装 `PHP Fileinfo 扩展` **

推荐使用 oneinstack 安装php环境 [https://oneinstack.com/auto](https://oneinstack.com/auto)

可以根据需要安装扩展，具体扩展安装请参考官方文档。

最低安装标准 `nginx+php`

![image.png](https://i.loli.net/2018/10/27/5bd46cbf4efe3.png)

使用一下命令即可自动安装 `nginx+php`

```
wget http://mirrors.linuxeye.com/oneinstack-full.tar.gz && tar xzf oneinstack-full.tar.gz && ./oneinstack/install.sh --nginx_option 1 --php_option 7 --phpcache_option 1 --reboot
```

**oneinstack安装fileinfo扩展**

![image.png](https://i.loli.net/2018/11/18/5bf155d4455b5.png)

![image.png](https://i.loli.net/2018/11/18/5bf155607859a.png)

**注意：**

OLAINDEX 基于 `Laravel` 安装需要开启禁用的几个函数方法，步骤如下：

oneinstack php安装路径 `/usr/local/php/etc/php.ini`

```
1、进入php.ini文件，找到disable_function=，删除proc_open函数，即可。
2、进入php.ini文件，找到disable_function=，删除proc_get_status函数，即可。
3、进入php.ini文件，找到disable_function=，删除putenv函数，即可。
4、sudo service php-fpm restart # 重启 php 进程
```

另外使用composer包管理器， 需要下载 composer 并且全局处理，步骤如下：

```
1、curl -sS https://getcomposer.org/installer | php  
2、mv composer.phar /usr/local/bin/composer 
3、composer config -g repo.packagist composer https://mirrors.aliyun.com/composer # 更换源为国内源，国外服务器可忽略此步骤
```

