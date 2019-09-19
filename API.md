## 1.注册接口

**简要描述：** 

- 用户注册

**请求URI：** 
- `/users/register`

**请求方式：**
- POST 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|name|*是*|string|用户名|
|password|*是*|string|登录密码|
|confirm_password|*是*|string|确认登录密码|


 **正确返回示例**

``` 
{
    "code": 1,
    "message": "注册成功，请登录",
    "data": []
}
```

 **返回参数说明** 

|参数名|类型|说明|
|:-----  |:-----|-----                           |
|code|int|业务状态码 1-正确 其他值-错误|
|message|string|业务信息|
|data|array|返回数据|


**错误返回示例**
```
{
    "code": -2,
    "message": "抱歉，用户名已被注册",
    "data": []
}
```

 **备注** 



----------


## 2.登录接口

**简要描述：** 

- 输入已注册用户名及密码获取JWT

**请求URI：** 
- `/jwt/login`

**请求方式：**
- get 

**参数：** 

|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|username|*是*|string|用户名|
|password|*是*|string|登录密码|


 **正确返回示例**

``` 
{
    "code": 1,
    "message": "登录成功",
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJub25lIn0.eyJpc3MiOiJzd29mdC5sb2NhbGhvc3QiLCJhdWQiOiJzd29mdC5sb2NhbGhvc3QiLCJqdGkiOiJUaGlzSXNLZXlWYWx1ZSIsImlhdCI6MTU2ODg4MTU0MywibmJmIjoxNTY4ODgxNTQzLCJleHAiOjE1Njg4ODUxNDMsInVpZCI6MX0."
    }
}
```

 **返回参数说明** 

|参数名|类型|说明|
|:-----  |:-----|-----                           |
|code|int|业务状态码 1-正确 其他值-错误|
|message|string|业务信息|
|data|array|返回数据|
|token|string|JWT 令牌|


**错误返回示例**
```
{
    "code": -1,
    "message": "抱歉，您输入的账号或密码错误",
    "data": []
}
```

 **备注** 


--------

## 3.个人信息接口

**简要描述：** 

- 此接口是JWT令牌有效性的测试接口

**请求URI：** 
- `/jwt/me`

**请求方式：**
- get 

**参数：** 

Request Header 中要包含token，值为登录接口获取到的令牌值

 **正确返回示例**

``` 
{
    "code": 1,
    "message": "权限有效果，返回个人信息",
    "data": {
        "uid": 1
    }
}
```

 **返回参数说明** 

|参数名|类型|说明|
|:-----  |:-----|-----                           |
|code|int|业务状态码 1-正确 其他值-错误|
|message|string|业务信息|
|data|array|返回数据|
|uid|string|JWT 令牌所属用户ID|


**错误返回示例**
```
{
    "code": -1,
    "message": "Token不能为空",
    "data": []
}
```

 **备注** 
 
 
 ----------
 
 
 ## 4.MQTT消息发布接口
 
 **简要描述：** 
 
 - 此接口为MQTT消息发布接口，调用前可以通过`docker-compose exec php php /usr/share/nginx/html/swoft.localhost/bin/swoft mqtt:sub --topic=test --server=emqx` 开启订阅消息的客户端
 
![Snipaste_2019-09-19_16-34-37.png](https://i.loli.net/2019/09/19/oIc7YXfJbsCyTOn.png)
 
 **请求URI：** 
 - `/mqtt/publish`
 
 **请求方式：**
 - get 
 
 **参数：** 
 
|参数名|必选|类型|说明|
|:----    |:---|:----- |-----   |
|topic|*是*|string|主题|
|content|*是*|string|载荷|
 
  **正确返回示例**
 
 ``` 
{
    "code": 1,
    "message": "推送成功",
    "data": {
        "topic": "test",
        "content": "http://swoft.localhost"
    }
}
 ```
 
  **返回参数说明** 
 
 |参数名|类型|说明|
 |:-----  |:-----|-----                           |
 |code|int|业务状态码 1-正确 其他值-错误|
 |message|string|业务信息|
 |data|array|返回数据|
 |topic|string|主题|
 |content|string|载荷|
 
 
 **错误返回示例**

 
  **备注** 