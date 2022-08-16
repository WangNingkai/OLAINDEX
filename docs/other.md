## 后台登录

后台默认地址：`https://you.domain/admin`

初始后台账号密码：  `admin` `123456`;

路由修改地址：`routes/web.php`

```
https://github.com/WangNingkai/OLAINDEX/blob/6.0/routes/web.php
```

![route](https://i.loli.net/2018/10/27/5bd47191e7a90.png)


## 特殊文件功能

**不建议创建与特殊文件同名的文件夹或文件，否则会导致文件无法查看及下载**

` README.md `、`HEAD.md` 特殊文件使用

**在文件夹底部添加说明:**  
>在 onedrive 的文件夹中添加` README.md `文件，使用markdown语法。  

**在文件夹头部添加说明:**  
>在 onedrive 的文件夹中添加`HEAD.md` 文件，使用markdown语法。  


## 缓存配置

默认 `OLAINDEX` 使用文件缓存，可以先修改为 `Laravel `支持的缓存类型，如 "apc", "array", "database", "file", "memcached", "redis"等，只需修改配置文件 `.env`。具体配置请参考 `laravel` 文档 [缓存系统](https://laravel-china.org/docs/laravel/5.7/cache/2278)

如使用 `redis`作为缓存的话，还需要安装 php `redis` 扩展包

修改.env文件后需要执行 `php artisan config:cache` 确保配置生效

**配置文件**

```
# 缓存配置
CACHE_DRIVER=file # 这里是缓存类型

# 如配置redis 缓存需要填写以下配置
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 版本升级

```
git pull 
composer update -vvv # 无版本更新只需执行到此

chmod -R 777 storage # 补充，执行此处两条命令，确保缓存的写入权限，否则500
chown -R www:www *
```

