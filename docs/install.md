## 手动安装


**注意：**

这里的命令需要一步一步执行

```bash
cd web目录
git clone https://github.com/WangNingkai/OLAINDEX.git tmp 
mv tmp/.git . 
rm -rf tmp 
git reset --hard 
cp database/database.sample.sqlite database/database.sqlite  # 数据库文件
composer install -vvv # 这里确保已成功安装 composer ，如果报权限问题，建议给予用户完整权限。
chmod -R 777 storage 
chown -R www:www * # 此处 www 根据服务器具体用户组而定
php artisan od:install # 此处绑定域名需根据实际域名谨慎填写（包含http/https）

# 安装完成后，不要忘记配置 nginx ，将域名指向应用目录的 public 下，参考下面nginx配置。

```

***

## 宝塔面板安装

**不会配置的请慎重，这里我也亲自写的一篇BT面板安装教程，请按照指示操作**

[BT 面板安装 OLAINDEX 全方位指南](https://imwnk.cn/archives/bt-olaindex)

***

## Web服务器配置

将应用的运行目录指向的是 根目录下的 `public` 目录，如 `www/OLAINDEX/public`

![image.png](https://i.loli.net/2018/10/27/5bd46e542f23f.png)

**Apache**

```
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

**Nginx**

```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**这里 `nginx` 配置仅供参考**

```
server {
    listen 80; # 80端口 仅支持通过https://olaindex.ningkai.wang中转的域名，否则请配置ssl证书，放通443端口
    server_name example.com;
    root /example.com/public; # 这里填写你的程序目录，注意不要少了public

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php7.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param  SCRIPT_FILENAME you/app/path/public/index.php; # 注意这里根据目录填写
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
