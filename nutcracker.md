# Nutcracker（Twemproxy）

* author : yongfeimiao
* date : 2015.12.01

## 1、什么是Nutcracker
* Nutcracker，又称Twemproxy（读音："two-em-proxy"）是支持memcached和redis协议的快速、轻量级代理；
* 它的建立旨在减少后端缓存服务器上的连接数量；
* 再结合管道技术（pipelining*）、及分片技术可以横向扩展分布式缓存架构；

## 2、特点
* （1）快 √
* （2）轻量级 √
* （3）减少与第三方的直接连接数  √
  * （1）保持与第三方的长连接
  * （2）可配置代理与后台每个第三方分片连接的数目
* （4）支持管道技术
* （5）支持平行多服务器代理，防止单点  √
* （6）支持多个服务器池  √
* （7）支持memcached ASCII和Redis协议  √
* （8）通过一个简单的YAML文件来实现服务器池的配置  √
* （9）支持多种hash模式，包括最常用的一致性hash模式，可以设置后端实例的权重  √
* （10）支持配置一定次数失败剔出节点  √
* （11）支持状态监控  √
* （12）跨平台

√ 为我们用它的原因，或者目前已经在用的

## 3、缺点
* （1）不支持Redis的事物操作
* （2）不支持部分批量操作，如mset（支持但是变成非原子性操作）
* （3）出错提示不够完善，不利于追查问题


## 4、配置参数
eg.当前商品服务的nutcracker配置：

![memcached_vs_redis_nutcracker](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/iq3Wm6wMUpqD5*5DJAhdqd7lHi3XUHWH6BKnapl5*jA!/b/dAABAAAAAAAA&bo=MASAAgAAAAAFB5I!&rf=viewer_4)

参数配置详解（注意YAML文件格式比较死板key: value）：
* listen: 监听
 * （1）socket file（当前memcached方式）
 * （2）hostname:port或者ip:port（当前redis方式）
* hash:hash函数名
 * （1）md5（当前memcached方式）
 * （2）fnv1a_64（当前redis方式）
 * （3）...
* hash_tag:可以自己设定将两个key hash到同一个实例上
* distribution:key hash完之后的分配模式
 * （1）ketama（一致性hash：当前memcached、redis方式）详见：http://bizfe.meilishuo.com/agg?name=rd_tech_sharing&doc=rd_tech_sharing/consistent_hashing
 * （2）modula（取模）
 * （3）random（随机模式）
* timeout:超时时间（单位在毫秒）我们等待建立连接到服务器或从服务器接收响应。默认情况下，无限期地等待
* backlog:默认512
* preconnect:开启长连接，默认为false，布尔型参数
* redis:第三方服务是否是redis，默认为false，布尔型参数
* redis_auth:redis连接安全认证码
* server_connections:当前服务最大连接数，默认情况下：最多一个
* auto_eject_hosts:当连续失败次数达到配置的server_failure_limit时，是否自动剔除当前分片,默认为false（不踢出）
* server_retry_timeout:被剔出分片间隔多长时间在尝试去请求一次（单位：毫秒），默认是30000毫秒
* server_failure_limit:连续失败次数，默认为2次
* servers:第三方服务地址列表
 * （1）hostname/ip:port:weight（当前memcached方式）
 * （2）hostname/ip:port:weight nodeName（当前redis方式）

## 5、监控详解
```bash
$ /home/service/nutcracker/bin/nutcracker --describe-stats
This is nutcracker-0.2.4

pool stats:
  client_eof          "# eof on client connections"
  client_err          "# errors on client connections"
  client_connections  "# active client connections"
  server_ejects       "# times backend server was ejected"
  forward_error       "# times we encountered a forwarding error"
  fragments           "# fragments created from a multi-vector request"

server stats:
  server_eof          "# eof on server connections"
  server_err          "# errors on server connections"
  server_timedout     "# timeouts on server connections"
  server_connections  "# active server connections"
  requests            "# requests"
  request_bytes       "total request bytes"
  responses           "# respones"
  response_bytes      "total response bytes"
  in_queue            "# requests in incoming queue"
  in_queue_bytes      "current request bytes in incoming queue"
  out_queue           "# requests in outgoing queue"
  out_queue_bytes     "current request bytes in outgoing queue"

$ telnet 0 4201
{
    "service": "nutcracker",
    "source": "dfz-goods-php-01.meilishuo.com",#hostname
    "version": "0.2.4",
    "uptime": 1687141,#服务已经启动的时间（单位：秒）√
    "timestamp": 1448939464,#当前时间戳 √
    "web": {#配置名称
        "client_eof": 10552370,
        "client_err": 0,#客户端连接错误次数
        "client_connections": 2,#当前活跃的客户端连接数
        "server_ejects": 0,#后端服务被踢出次数 √
        "forward_error": 1,#转发错误次数 √
        "fragments": 157341762,
        "10.5.12.67": {
            "server_eof": 0,
            "server_err": 0,#服务端连接错误次数
            "server_timedout": 0,#因连接超时的服务端错误次数
            "server_connections": 200,#当前活跃的服务端连接数
            "requests": 15151080,#已请求次数
            "request_bytes": 922022844,#已请求字节数
            "responses": 15151080,#已响应次数
            "response_bytes": 15929562310,#已相应字节数
            "in_queue": 0,
            "in_queue_bytes": 0,
            "out_queue": 0,
            "out_queue_bytes": 0
        },
        "10.5.12.68": {
            "server_eof": 0,
            "server_err": 0,
            "server_timedout": 0,
            "server_connections": 200,
            "requests": 13543421,
            "request_bytes": 843329330,
            "responses": 13543421,
            "response_bytes": 11472952692,
            "in_queue": 0,
            "in_queue_bytes": 0,
            "out_queue": 0,
            "out_queue_bytes": 0
        },
        ...
    }
}
```

## 6、名词解释
* redis pipelining（流式批处理、管道技术）：将一系列请求连续发送到Server端，不必每次等待Server端的返回，而Server端会将请求放进一个有序的管道中，在执行完成后，会一次性将结果返回（解决Client端和Server端的网络延迟造成的请求延迟）

## 7、参考文献
* https://github.com/twitter/twemproxy
* https://github.com/twitter/twemproxy/blob/master/notes/redis.md
* https://github.com/twitter/twemproxy/blob/master/notes/memcache.md
