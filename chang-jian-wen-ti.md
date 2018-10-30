# 常见问题

### PHP 后缀文件无法解析问题

![image.png](https://i.loli.net/2018/10/27/5bd472feb50a6.png)

### open\_basedir 类权限问题

这类问题通常这样报错

```text
Warning: require(): open_basedir restriction in effect. File(/www/wwwroot/xxx/public/../vendor/autoload.php) is not within the allowed path(s): (/www/wwwroot/xxx/public/:/tmp/:/proc/) in /www/wwwroot/xxx/public/index.php on line 24
```

请自行google防跨目录设置。如果使用的军哥的lnmp一键安装，建议参考这篇文章：[https://lnmp.org/faq/lnmp-vhost-add-howto.html\#user.ini](https://lnmp.org/faq/lnmp-vhost-add-howto.html#user.ini)

### 图片视频访问 404 问题

访问图片 nginx 通常会返回 404 页面。

建议注释下图内容同时关闭防盗链或者加入白名单：

![image.png](https://i.loli.net/2018/10/27/5bd472fe4d310.png)

### 数据库迁移问题

从 3.0版本开始，不在集成数据库文件，使用json文件作为配置文件。强烈建议升级！！！

