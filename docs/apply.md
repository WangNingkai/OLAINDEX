## 公共账号

- 暂不提供

## 回调地址

- 非中转域名回调，必须是 `https` 协议，格式一般为 `https://your.domain/callback` 所以配置服务器需要为域名添加 `SSL`，否则无法生效。

- 中转域名进行回调，可不使用 `https` 协议。需通过 `https://olaindex.ningkai.wang` 申请 `client_id` 和 `client_secret`，前提是在执行安装时填写了正确的绑定域名。

- 本地回调，默认 `http://localhost:8000/callback`。

## 世纪互联账号申请

**初始安装页面一键申请方法与世纪互联申请方法不兼容，需要单独到Azure申请。(参考[issue 40](https://github.com/WangNingkai/OLAINDEX/issues/40))**

**申请需要添加Graph Api权限（包括文件的读写和基本的个人信息读取）**


## 一键申请(推荐)

**申请密钥建议使用个人账号注册应用，使用企业账号绑定网盘，同一账号申请可能会导致登录异常**


-  安装

![install](https://i.loli.net/2018/10/27/5bd46f7f160a6.png)

-  获取密钥

![secret](https://i.loli.net/2018/10/27/5bd47070cd1b0.png)

-  获取id

![id](https://i.loli.net/2018/10/27/5bd470721f1a3.png)


-  保存提交

![submit](https://i.loli.net/2018/10/27/5bd470719602a.png)


## 错误处理

如果填写过程出现错误，可以执行命令，重置数据文件，重新安装。 

`composer run uninstall-app` 此操作会重置配置文件未初始化状态；

也可以通过页面的返回修改重置数据，并进行再一次绑定。

