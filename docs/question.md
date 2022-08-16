!> 此文档待完善

**Q: 文件加密、隐藏功能在哪里**

A: 账号列表 -> 操作 -> 账号设置 -> 加密隐藏


**Q: 路径兼容模式如何设置**

A: 初期6.0 集成了 5.0 的多账号功能，方便标识账号会在路径上加入账号标识字符，后期由于许多用户的需求，加入了路径兼容模式。后台直接开启即可，为兼容原先模式会在链接加入 `?hash=xxx` 标识

**Q: 手动安装或自动安装失败如何解决**

A: 不通过 `composer run install-app` 方式手动安装方式如下

```
cd web目录
git clone https://github.com/WangNingkai/OLAINDEX.git tmp 
mv tmp/.git . 
rm -rf tmp 
git reset --hard 
composer install -vvv # 这里确保已成功安装 composer ，如果报权限问题，建议给予用户完整权限。
chmod -R 777 storage 
chown -R www:www * # 此处 www 根据服务器具体用户组而定
```

前提已通过上面步骤，使用 `composer` 安装依赖，确保 `storage` 目录有写入权限

1. 复制根目录 `.env.example` 为 `.env`
2. 修改了 `.env` 文件的数据库配置及其它配置
3. 执行 `php artisan key:generate` 生成运行所需配置
4. 执行数据库迁移 `php artisan migrate --seed`
5. 访问网站，设置其它数据

**Q: 如何重置应用**

A: 删除 `storage/install` 文件夹下的 `install.lock` 文件 以及 `data` 目录的 `sqlite` 文件

**Q: 国际版账号访问超时报错500**

A: 适当调整链接超时时间和重试次数，修改文件具体位置

https://github.com/WangNingkai/OLAINDEX/blob/6.0/app/Service/GraphClient.php#L121

https://github.com/WangNingkai/OLAINDEX/blob/6.0/app/Service/GraphRequest.php#L217

https://github.com/WangNingkai/OLAINDEX/blob/6.0/app/Service/GraphRequest.php#L216

