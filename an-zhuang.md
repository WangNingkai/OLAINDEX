# 安装



### 自动安装（推荐）

```bash
cd web目录
git clone https://github.com/WangNingkai/OLAINDEX.git tmp 
mv tmp/.git . 
rm -rf tmp 
git reset --hard 
composer install -vvv # 这里确保已经安装composer成功  # 如果报权限问题，建议先执行权限命令
chmod -R 755 storage/
chown -R www:www *
php artisan init:install
```

### 宝塔面板安装

虽然宝塔简单，但是本人不建议使用宝塔安装，这样反而会出现问题，如果需求较少，只用来搭建olaindex，建议使用oneinstack安装。

这里还是贴出一个宝塔安装参考 ： [https://www.ulu.app/archives/27.html](https://www.ulu.app/archives/27.html)

### Web服务器rewrite配置

应用的目录指向的是 项目目录下的public目录，如 OLAINDEX/public

![image.png](https://i.loli.net/2018/10/27/5bd46e542f23f.png)

**Apache**

```text
Options +FollowSymLinks
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

**Nginx**

```text
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

**详细的nginx配置可以参考**

```text
server {
    listen 80;
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
        fastcgi_param  SCRIPT_FILENAME you/app/path/public/index.php;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

