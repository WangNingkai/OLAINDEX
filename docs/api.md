!> 此文档待完善

> 版本 v4.0 后，添加第三方访问接口，目前支持图床接口，以后会支持更多接口。

## 用法

1. 后台设置第三方访问密钥 {access_token}

2. Header 头信息 添加认证信息 

    `"Authorization":"Bearer {access_token}"`

3. 接口调用

## 接口列表

### 上传图床

- 地址：{domain}/api/image

- 访问方式： POST

- 参数：

```
Content-Disposition: form-data; 
name="olaindex_img"; 
filename="onedrive.png"
Content-Type: image/png
```

- 返回参数：

```json
{
    "errno": 200,
    "data": {
        "id": "01FGBPEFYAMCC",
        "filename": "onedrive.png",
        "size": 32303,
        "time": "2019-06-14T03:15:08Z",
        "url": "http://{domain}/view/2019/06/14/UwNZdw2M/onedrive.svg.png",
        "delete": "http://{domain}/file/delete/eyJpdiI6IktZaVc0Z01OWU4wUzRuYXpuRjFBZVE9PSIsInZhbHVlIjoiWEFzTGtLWnhibUQxVWVxTlRoU3RYRmpuTE40YlwvVnJYdWhPUTJwcStSd1JaRGpGZ0hwMFpVTnd1QWU0NCtBcGhiZ011UnRwRnBac3Jjd2RHTGZ4clRHWUFIeWFpT2VqMTE3M2dDZk9ibkpaMjRxODBjdUhIRzBSd0VoRk9TMGRwQWNcL29TQ3lvbDR1U3hUcGE3QzVqQUZvZ1hLTmI2emlVbnNtaWdmMVJsQ1hUY096cFB1aFZKajNhOW41eEVHQ3ZONEJkM09wQXRORjVoWGtrZExzaHg3U0llbXFsa0VKQlwvR0pzVXBvd0YxNkpuVDVyYWhIeFI3UHFJK0szV09Gc3hyUlBTb2JyeG5XRTg4RFlnZjFQUnNZcDh3V0xDM1ZLOGRcL0QycUNjNk1acU1aQmhMbUZ6SFVuRU84MkwyXC9VOURKRit6TERBeEVZNHhPd1p6ZkhSOGpJNlNrTUp0cjU0MFRma25vVGxxemJTenFKclBjV1dCOGpSdEp2dU5TUG5wVFNxSzVqNWFvSXJ4M2hoNmNNNzhiS0dmbkRBVkhiSGdEbk1UIiwibWFjIjoiNWQzMjYyZjllMjhlMjVlZTViMjE5MTVlOGQzNTEzNjE4ZmE1ZTBjOTFjYzcxOTlkMTgxZTBjNjIzM2QyMTFhMyJ9"
    }
}
```
- 举例

```php
<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://{domain}/api/image",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"olaindex_img\"; filename=\"onedrive.png\"\r\nContent-Type: image/png\r\n\r\n\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
  CURLOPT_HTTPHEADER => array(
    "Accept: */*",
    "Authorization: Bearer 123123",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    "Content-Type: application/json",
    "Host: 127.0.0.1:8000",
    "Postman-Token: b09bec02-8905-4c8d-9476-79787df9da4c,bceb64fb-2208-45c7-81ab-b131eba1aaf0",
    "User-Agent: PostmanRuntime/7.15.0",
    "accept-encoding: gzip, deflate",
    "cache-control: no-cache",
    "content-length: 32524",
    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
```
