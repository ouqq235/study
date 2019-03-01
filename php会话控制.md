# 一、session 
  PHP的会话也称为Session。PHP在操作Session时，当用户登录或访问一些初始页面时服务器会为客户端分配一个SessionID。SessionID是一个加密的随机数字，在Session的生命周期中保存在客户端。它可以保存在用户机器的Cookie中，也可以通过URL在网络中进行传输。
  用户通过SessionID可以注册一些特殊的变量，称为会话变量，这些变量的数据保存在服务器端。在一次特定的网站连接中，如果客户端可以通过Cookie或URL找到SessionID，那么服务器就可以根据客户端传来的SessionID访问会话保存在服务器端的会话变量。
  Session的生命周期只在一次特定的网站连接中有效，当关闭浏览器后，Session会自动失效，之前注册的会话变量也不能再使用。具体的使用步骤如下：
## 1）初始化会话。
  在实现会话功能之前必须要初始化会话，初始化会话使用session_start()函数。
```php
  bool session_start(void)
```
  该函数将检查SessionID是否存在，如果不存在，则创建一个，并且能够使用预定义数组$_SESSION进行访问。如果启动会话成功，则函数返回TRUE，否则返回FALSE。会话启动后就可以载入该会话已经注册的会话变量以便使用。
## 2）注册会话变量。
  自PHP 4.1以后，会话变量保存在预定义数组$_SESSION中，所以可以以直接定义数组单元的方式来定义一个会话变量，格式如下：
```php
  $_SESSION["键名"]="值";
```
  会话变量定义后被记录在服务器中，并对该变量的值进行跟踪，直到会话结束或手动注销该变量
## 3）访问会话变量。
  要在一个脚本中访问会话变量，首先要使用session_start()函数启动一个会话。之后就可以使用$_SESSION数组访问该变量了。
## 4）销毁会话变量。
  会话变量使用完后，删除已经注册的会话变量以减少对服务器资源的占用。删除会话变量使用unset()函数，语法格式如下：
```php
  void unset(mixed $var [, mixed $var [, $... ]])
  unset($_SESSION['键名'])
``` 
  说明：$var是要销毁的变量，可以销毁一个或多个变量。要一次销毁所有的会话变量，使用session_unset();。
## 5）销毁会话
  使用完一个会话后，要注销对应的会话变量，然后再调用session_destroy()函数销毁会话，语法格式如下：
```php
  bool session_destroy ( void )
``` 
  该函数将删除会话的所有数据并清除SessionID，关闭该会话。
# 二、cookie
  Cookie可以用来存储用户名、密码、访问该站点的次数等信息。在访问某个网站时，Cookie将html网页发送到浏览器中的小段信息以脚本的形式保存在客户端的计算机上。
  一般来说，Cookie通过HTTP Headers从服务器端返回浏览器。首先，服务器端在响应中利用Set Cookie Header来创建一个Cookie。然后浏览器在请求中通过Cookie Header包含这个已经创建的Cookie，并且将它返回至服务器，从而完成浏览器的验证。
  Cookie技术有很多局限性，例如
  1）多人共用一台计算机，Cookie数据容易泄露。
  2）一个站点存储的Cookie信息有限。
  3）有些浏览器不支持Cookie。
  4）用户可以通过设置浏览器选项来禁用Cookie。
  正是由于以上Cookie的一些局限性，所以，在进行会话管理时，SessionID通常会选择Cookie和URL两种方式来保存，而不是只保存在Cookie中。
具体而言，Cookie的使用步骤如下
## 1）创建Cookie。
  在PHP中创建Cookie使用setcookie()函数，语法格式如下：
 ```php
  bool setcookie(string $name [, string $value [, int $expire [, string $path [, string $domain [, bool $secure [, bool $httponly ]]]]]])
``` 
  ① $name：表示Cookie的名字。
  ② $value：表示Cookie的值，该值保存在客户端，所以不要保存比较敏感的数据。
  ③ $expire：表示Cookie过期的时间，这是一个UNIX时间戳，即从UNIX纪元开始的秒数。对于$expire的设置一般通过当前时间戳加上相应的秒数来决定。例如，time()+1200表示Cookie将在20min后失效。如果不设置则Cookie将在浏览器关闭之后失效。
  ④ $path：表示Cookie在服务器上的有效路径。默认值为设定Cookie的当前目录。
  ⑤ $domain：表示Cookie在服务器上的有效域名。例如，要使Cookie能在example.com域名下的所有子域都有效，该参数应设为".example.com"。
## 2）访问Cookie。
  通过setcookie()函数创建的Cookie是作为数组的单元，存放在预定义变量$_COOKIE中。也就是说，直接对$_COOKIE数组单元进行赋值也可以创建Cookie。但$_COOKIE数组创建的Cookie在会话结束后就会失效。
##  3）删除Cookie。
  Cookie在创建时指定了一个过期时间，如果到了过期时间，那么Cookie将自动被删除。在PHP中没有专门删除Cookie的函数。如果为了安全方面的考虑，在Cookie过期之前就想删除Cookie，那么可以使用setcookie()函数或$_COOKIE数组将已知Cookie的值设为空。
 ```php
  <?php
    $_COOKIE["user"]="administrator";
    setcookie("password","123456",time()+3600);
    $_COOKIE["user"]="";                    //使用$_COOKIE清除Cookie
    setcookie("password","");                //使用setcookie()函数清除Cookie
    print_r($_COOKIE);                    //输出：Array ( [user] => )
?>
```   
  Cookie和Session都是用来实现会话机制的，由于HTTP协议是无状态的，所以要想跟踪一个用户在同一个网站之间不同页面的状态，需要有一个机制，称为会话机制。
