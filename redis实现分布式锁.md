# Redis实现分布式锁



## 1、原理图展示

![redis实现分布式锁](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/2d.x7OgXotmZSP9kQKhDiDzmafrJJ6r7DIZ7JYuLS1Q!/b/dKsAAAAAAAAA&bo=SQMXBQAAAAADB3o!&rf=viewer_4)

## 2、原理解析

### 锁竞争（lock timeout一定要大于业务逻辑操作时间）

* （1）SETNX lock_pid < current millitime + lock timeout + buffer_time(1) >
 * 返回值：1，获取锁成功，可以开始执行业务逻辑操作
 * 返回值：0，继续get lock_pid
 * lock_pid：按业务类型唯一，保证同一类型只有一个锁，只可能有一个获得锁
 * current millitime：自定获取毫秒函数的返回值
 * lock timeout：大小和业务逻辑执行时间有关，不要太大，单位：毫秒
 * buffer_time：大致估计setnx从发送到redis返回结果花的时间（也可以为：0）
* （2）GET lock_pid
 * 返回值：是，说明已经过期或者已被释放，可以尝试调用GETSET方法
 * 返回值：否，说明当前锁被另外一个占用，业务逻辑可以sleep一段时间之后再重试
* （3）GETSET lock_pid < current millitime + lock timeout + buffer_time(1) >
 * 返回值：是，获取锁成功，可以开始执行业务逻辑操作
 * 返回值：否，说明当前锁被另外一个占用，业务逻辑可以sleep一段时间之后再重试
 * 其他参数意思同上

### 锁释放（释放一定要预留合理的buffer_time2）

* （1）核心业务逻辑处理完之后，就到了锁释放环节，先get lock_pid
 *  返回值 > (当前millitime() + buffer_time2)：调用del lock_pid及时释放锁，提高并发效率
 *  返回值 <= (当前millitime() + buffer_time2)：说明马上就要会再第二次锁竞争环节自动释放，再考虑到可能你此时（del操作+传输时间，有延迟）del会导致已经失效分配给其他资源的锁被错误释放，所以，这里绝对不可以再释放
 *  返回值：NULL（null：redis出问题；false：没有这个key或者分片挂）：都不用care

## 3、伪代码示例

```php
/**
 * @purpose:获取redis分布式锁（内部所有时间处理基于毫秒（除非函数不支持毫秒），考虑到方便使用外部基于秒）
 * 
 * @param string/int $pid           业务唯一id
 * @param int        $lock_timeout  锁时间，单位：秒
 * @param int        $sleep_time    重试间隔时间，单位：秒
 * @param int        $retry_times   重试次数，默认：不重试
 * @param int        $buffer_time   操作及连接缓冲时间，单位：秒
 */
public function getPidLock($pid, $lock_timeout, $sleep_time = 0, $retry_times = 0, $buffer_time = 0.001){
    $lock = 0;
    $had_retry_times = 0;
    
    while ($lock != 1 && $had_retry_times <= $retry_times) {
        $now = $this->millitime();//自定获取毫秒函数
        $lock_expire_time = $now + $lock_timeout * 1000 + $buffer_time * 1000;
        $lock = SETNX($this->_getLockKey($pid), $lock_expire_time);
        if ($lock == 1 //第一个判断就成功拿到锁
         || (($now > GET($this->_getLockKey($pid))) && ($now > GETSET($this->_getLockKey($pid), $lock_expire_time)) //第二个判断成功拿到锁
         ) {
            $lock = 1;
            break;
         } else {
            if ($sleep_time) {
                usleep($sleep_time * 1000 * 1000);
            }
         }
         
         $had_retry_times++;
    }
    
    return $lock;
}

/**
 * @purpose:释放redis分布式锁（内部所有时间处理基于毫秒（除非函数不支持毫秒），考虑到方便使用外部基于秒）
 * 
 * @param string/int $pid           业务唯一id
 * @param int        $buffer_time   操作及连接缓冲时间，单位：秒
 */
public function releasePidLock($pid, $buffer_time = 0.003){
    $lock_expire_time = GET($this->_getLockKey($pid));
    $now = $this->millitime();//自定获取毫秒函数
    
    if ($lock_expire_time //处理异常情况
     && (($lock_expire_time - $now) > $buffer_time) //在考虑buffer_time的情况下，锁依旧还没有过期
     ) {
        DEL($this->_getLockKey($pid));//主动释放锁
    }//其他情况，可以通过锁竞争的第二个判断被动释放锁
    
    return true;
}
```
 
