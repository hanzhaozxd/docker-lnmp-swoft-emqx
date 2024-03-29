# docker-lnmp-swoft-emqx

### 说明

一个学习记录，最终实现基于Docker的Swoft开发环境，同时运行emqx（TODO：集群）服务器。在Swoft实现简单登录注册，及mqtt协议消息的发布等。

### 安装流程

1. git clone https://github.com/hanzhaozxd/docker-lnmp-swoft-emqx.git
2. cd docker-lnmp-swoft-emqx
3. docker-compose up # 启动容器组，第一次耗时较久，根据网速大概需要10分钟至半小时左右。第一次建议不加-d 观察启动是否正常 
4. cd www/swoft.localhost && composer install #安装 swoft vendor依赖
5. cd ../ && docker-compose exec php php /usr/share/nginx/html/swoft.localhost/bin/swoft http:start -d # 启动swoft HTTP服务，此步骤后续找一个自动执行的方案，此步骤耗时在1分半，也需要优化一下。
6. cp .env.example .env # 生成.env文件
7. 修改host文件 添加 127.0.0.1 swoft.localhost
8. git update-index --assume-unchanged redis/redis.log # 忽略日志
9. git update-index --assume-unchanged nginx/log/error.log # 忽略日志
10. docker-compose exec php php /usr/share/nginx/html/swoft.localhost/bin/swoft migrate:up # 执行数据库迁移

### 服务正常性检查
1. localhost/phpinfo.php # phpinfo
2. localhost/db_redis_tes.php # 两个ok
3. swoft.localhost  # 正常可看到下图1的Swoft框架首页
4. localhost:18083  # 正常可看到emqx管理台登录页 默认账号密码 admin public

---------

### 运行截图

![n7Y75j.jpg](https://s2.ax1x.com/2019/09/18/n7Y75j.jpg)
![n7YoVg.png](https://s2.ax1x.com/2019/09/18/n7YoVg.png)

---------

### 代码变更后快速重启Swoft
```shell script
vim ~/.zshrc
加入如下命令
function docker_swoft_restart() {
    z docker-lnmp;
    docker-compose exec php php /usr/share/nginx/html/swoft.localhost/bin/swoft http:restart -d
}
后续执行 docker_swoft_restart 即可快速重启Swoft
这个步骤平均耗时1分钟，一定是有问题的，推测可能是Docker for Mac磁盘性能低的问题。
```

------- 

### 实现的接口

[API](https://github.com/hanzhaozxd/docker-lnmp-swoft-emqx/blob/master/API.md)