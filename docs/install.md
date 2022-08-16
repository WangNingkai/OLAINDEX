## 手动安装


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

## 使用Docker安装

从DockerHub拉取Docker镜像：

```
docker run -d --init --name olaindex -p 80:8000 xczh/olaindex:6.0
```

现在，访问`http://YOUR_SERVER_IP/`，可以看到你的OLAINDEX应用了 ^_^

当然你也可以选择从Dockerfile自行编译Docker镜像，切换到项目根目录执行：

```
docker build -t xczh/olaindex:dev .

docker run -d --init --name olaindex -p 80:8000 xczh/olaindex:dev
```

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

若使用Docker安装，则Nginx应当配置为以下反向代理模式：

**注意**：使用Nginx时，`docker run`命令无需带`-p 80:8000`参数，否则会出现端口冲突错误。

```
server {
    listen 80;
    listen 443 ssl http2;

    server_name                example.com;
    server_name_in_redirect    on;
    port_in_redirect           on;

    if ( $scheme = http ) {
        return 301 https://$host$request_uri;
    }

    ssl_certificate          example.com.cer;
    ssl_certificate_key      example.com.key;
    ssl_trusted_certificate  example.com.ca.cer; # 可选，仅配置SSL Stapling时需要

    root /usr/share/nginx/html; # 指向Nginx默认页面目录即可
    index index.html index.htm;

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # 仅HTTPS需要：由于HTTPS页面中混合HTTP静态资源会被浏览器阻止，因此需要配置CSP头
    # 这是Bug，后续在代码中修正后可以无需这段配置
    add_header Content-Security-Policy "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdn.staticfile.org; object-src 'none'; child-src 'self'; frame-ancestors 'self'; upgrade-insecure-requests;";

    location / {
        proxy_pass  http://CONTAINER_IP:8000; # 设置为docker容器对应IP
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Port $server_port;
    }
}
```

完成以上反向代理配置后，可通过`https://example.com`访问你的OLAINDEX应用 ^_^
