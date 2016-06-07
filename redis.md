#redis 执行流程图 具体分析 src/redis.c文件
redis处理请求模式为单进程单线程异步处理，核心在于它的事件驱动，有些类似于epoll，都是基于I/O多路复用技术实现的主要函数位于下图中的最后两步  aeMain 和 aeSetBeforeSleepProc，如下图所示

![enter image description here](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/c5ybsa67rbWMQg1bAZsEYAfqA8tZHMtyxmj.m9u.miY!/b/dPoAAAAAAAAA&bo=qQIuBAAAAAADB6M!&rf=viewer_4 "redis服务端执行流程图")

redis的IO事件驱动库原理和实现图（主要文件有：ae.c  ae.h  ae_epoll.c  ae_evport.c  ae_kqueue.c  ae_select.c, 其中ae.c是事件处理模块主体，ae_epoll.c  ae_kqueue.c  ae_select.c  ae_evport.c是事件处理的四种实现方式，分别对应了epoll、select、kqueue、event ports，提供了相同的接口。）

![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/qUs0bEowilAky5BiS8Dzygl680WyuLow..ZOgPHBXDA!/b/dP8AAAAAAAAA&bo=HAItAgAAAAAFBxU!&rf=viewer_4 "913DFA22-C321-4A3F-B5B0-312FE21672AE")
#数据类型与应用场景
1：string类型 直接存储内容 所以单个读写的时间复杂度都为O(1)
      场景：我们的库存系统  商品状态 以及价格
      
2：hash类型 当成员数量少于512的时候使用zipmap(一种很省内存的方式实现hash table),反之使用hash表(key存储成员名,value存储成员数据) 所以单个读写的时间复杂度都为O(1)
     场景： 例如 一个用户ID  对应的 姓名  年龄  性别 等字段

3：set集合类型 使用hash表(key存储数据,内容为空) 所以单个读写的时间复杂度都为O(1)
     场景： goods_id 对应的sku_id

4：zset有序集合类型 使用跳表(skip list) 所以单个读写的时间复杂度都为O(M*log(1))
     场景：我们业务中暂无 不推荐使用

5：list队列 当成员数量少于512的时候使用ziplist(一种很省内存的方式实现list),反之使用双向链表(list) 所以单个进出时间复杂度都是O(1)
   场景：redis队列

6：HyperLogLog 3.0版本以及以上

7：GEO地理位置 3.0版本以上

8：redis有N个DB(默认为16个DB),并且每个db有一个hash表负责存放key,同一个DB不能有相同的KEY，但是不同的DB可以相同的KEY; 进入DB命令 select N 拓展阅读网址 http://redisdoc.com/
