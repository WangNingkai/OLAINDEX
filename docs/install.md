## 安装


**注意：**

这里的命令需要一步一步执行

```bash
cd web目录
git clone https://github.com/WangNingkai/OLAINDEX.git tmp 
mv tmp/.git . 
rm -rf tmp 
git reset --hard 
composer install -vvv # 这里确保已成功安装 composer ，如果报权限问题，建议给予用户完整权限。
chmod -R 777 storage 
chown -R www:www * # 此处 www 根据服务器具体用户组而定
composer run install-app (此为自动安装，默认sqlite存储数据)
```

如果或上述步骤安装错误、新装需要自定义数据库等数据参考下面：

前提已通过上面步骤，使用 `composer` 安装依赖，确保 `storage` 目录有写入权限

1. 复制根目录 `.env.example` 为 `.env`
2. 修改了 `.env` 文件的数据库配置及其它配置
3. 执行 `php artisan key:generate` 生成运行所需配置
4. 执行数据库迁移 `php artisan migrate --seed`
5. 访问网站，修改其它设置

安装完成后，不要忘记配置 nginx ，将域名指向应用目录的 public 下，参考下面nginx配置。

***

## Web服务器配置

将应用的运行目录指向的是 根目录下的 `public` 目录，如 `www/OLAINDEX/public`

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
