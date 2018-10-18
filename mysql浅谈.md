# 概述
## 背景

 * （1）Mysql作为一种开放源代码的关系型数据库管理系统（RDBMS），由于其快速、多线程、多用户等倍受青睐，并且几乎是免费的
 * （2）我们的业务中在使用，需要大家都能熟悉并掌握

## 定位
 * （1）重点分享通用、实用技巧
 * （2）项目中遇到的问题或有影响性能的地方分享出来
 * （3）项目开发中，可作为工具书用

## 要求
 * （1）所有沉淀下来的东西，必须得亲测没有问题
 
重点知识梳理

## 数据库设计范式


关系数据库有六种范式：第一范式（1NF）、第二范式（2NF）、第三范式（3NF）、巴德斯科范式（BCNF）、第四范式（4NF）和第五范式（5NF）

 * **（1）第一范式(确保每列保持原子性)**
 *第一范式是最基本的范式。如果数据库表中的所有字段值都是不可分解的原子值，就说明该数据库表满足了第一范式*



 * **（2）第二范式(确保表中的每列都和主键相关)**
 *需要确保数据库表中的每一列都和主键相关，而不能只与主键的某一部分相关（主要针对联合主键而言）。也就是说在一个数据库表中，一个表中只能保存一种数据，不可以把多种数据保存在同一张数据库表中*。

 
 * **（3）第三范式(确保每列都和主键列直接相关,而不是间接相关)**
 *需要确保数据表中的每一列数据都和主键直接相关，而不能间接相关。*


## 第一范式
1NF是关系型数据库的基本要求，也就是说，在关系型数据库中，比如MySQL，只要表的实际符合业务需求，那么表就符合1NF。比如下面这张表的设计符合1NF，ID，姓名，学生编号组成，并且满足业务需求：
```
|ID|姓名|学生编号|
|-
|1|张三|BJ203343439|
|2|李四|TJ362343443|
|3|王五|SH619343442|
```
但是突然有一天，学校规定前面两个字母表示学生所在的省份缩写，然后后面的值是唯一值的学号，来更详细标注学生信息。并且要在前台显示学生的所在省份缩写。尽管上面的设计能够通过php或者其他语言解析出来学号和所在省份，单这种设计又也不符合1NF，因为编号可以再分。把表修改成下面形式，又符合1NF
```
|ID|姓名|学号|所在省份缩写|
|-
|1|张三|203343439|BJ|
|2|李四|362343443|TJ|
|3|王五|619343442|SH|
```
`如果一个应用中从来不用获取学生的所在地和学号，只有编号的概念，那第一种设计就符合1NF，如果需要使用获取学生所在省份，有学号的概念那么第二中设计才符合1NF`
1NF的缺点
1. 数据冗余，商品分类信息大量重复
2. 修改异常，假如要修改BJ这个缩写改成小写形式，改动量很大

## 第二范式
如果刚好业务场景决定我们的商品类目经常变动，那么显然1NF的种种缺点将暴露出来，这个时候可以通过范式升级，升级到2NF来解决这些问题。太简单的表没办法升级到2NF，比如一个系统就做一个收录员工姓名这一件事情，然后就一个name表，name表里就两列字段，ID和姓名，这种表只能符合1NF。所以为了介绍2NF，随手建一个符合1NF的复杂一点的表如下：

|商品ID|商品名称|颜色|价格|商家名字|商家联系方式|
```
|-
|1|韩版休闲裤|蓝|50.0|张三|13634589384|
|1|韩版休闲裤|黄|60.0|张三|13634589384|
|2|欧式西装|黑|140.0|李四|13234534543|
```
这个表中有联合主键（商品ID，颜色），商品名称只和联合主键的（商品ID）相关，所以这张表的设计不符合2NF，只能认为是1NF。按照第二范式的规定，必须消除这种部分相关（其他列只与主键的某一部分相关）。对表进行拆分：

`商品价格表`
```
|商品ID|颜色|价格|
|-
|1|蓝|50.0|
|1|黄|60.0|
|2|黑|140.0|
```
`商品信息表`
```
|商品ID|商品名称|商家名称|商家联系方式|
|-
|1|韩版休闲裤|张三|13634589384|
|2|欧式西装|李四|13234534543|
```
商品价格表的主键（商品ID，颜色）两者决定价格，而不是价格只和主键中的一个相关，商品信息表主键（商品ID）决定了商品名称，商家名称，商家联系方式。所以这个表是符合2NF的，2NF表设计的优点:
1. 减少了数据冗余，商家信息不会重复，1NF有重复的商家信息
2. 修改数据带来了方便，假如要修改商品对应的商家联系方式，2NF只需要改一处，1NF需要修改两处

##第三范式
在2NF的基础上，如果商品继续增多，商品信息表会变成这样：
`商品信息表`
```
|商品ID|商品名称|商家名称|商家联系方式|
|-
|1|韩版休闲裤|张三|13634589384|
|2|韩版西装|张三|13634589384|
|3|欧式西装|李四|13234534543|
|4|欧式西裤|李四|13234534543|
```
结果，你会发现，数据又开始出现了冗余，当有一天想修改商家联系方式的时候，发现要修改好多行信息。这个时候需要再将表升级到3NF来解决问题。3NF要确保其他列都和主键（商品ID）直接相关，而不能间接相关，就目前这个商品信息表，由于商品ID->商家名称->商家联系方式；所以主键（商品ID）和商家联系方式间接相关联了。所以再将商品信息表拆分：
`商品价格表`
```
|商品ID|颜色|价格|
|-
|1|蓝|50.0|
|1|黄|60.0|
|2|黑|140.0|
```
`商品信息表`
|商品ID|商品名称|商家ID|
|-
|1|韩版休闲裤|1|
|2|欧式西装|2|
`商家信息表`
|商家ID|商家名称|商家联系方式|
|-
|1|张三|13634589384|
|2|李四|13234534543|
按照第三方的定义，每列都必须和主键直接相关，而不是间接相关。`商品价格表`主键（商品ID，颜色）直接决定价格。`商品信息表`中主键（商品ID）直接决定商品名称，同时也直接决定商家ID。`商家信息表`中主键（商家ID）直接决定商家名称和联系方式。所以这样的表符合3NF。

目的：
---
*大家在设计表的时候要意识到自己的设计是否满足以上条件，尽可能的做到规范*

## 定位
 * （1）熟悉数据存储的空间结构
 *  （2）什么是MySQL插件式的存储引擎
 * （2）存储引擎的原理、存储引擎的选择
### 存储引擎在MySQL架构中的位置
![enter image description here](http://b260.photo.store.qq.com/psb?/ed2bc575-473b-4fd0-b52e-fd97291fe793/8H8UxgpAP8SOk6vASl87RvA5F5kXLe0w75.CkaVdNTc!/c/dAQBAAAAAAAA&bo=QgKKAUICigEDACU! "mysql_clu")

1. Connectors指的是不同语言中与SQL的交互
2. Management Serveices & Utilities： 系统管理和控制工具
3. Connection Pool: 连接池，权限验证
4. SQL Interface: SQL接口
5. Parser: 解析器
6. Optimizer: 查询优化器。
7. Cache和Buffer： 查询缓存。
8. Engine ：存储引擎。
存储引擎是MySql中具体的与文件打交道的子系统。也是Mysql最具有特色的一个地方。
Mysql的存储引擎是插件式的。它根据MySql AB公司提供的文件访问层的一个抽象接口来定制一种文件访问机制（这种访问机制就叫存储引擎）。现在有很多种存储引擎，各个存储引擎的优势各不一样，最常用的InnoDB,BDB,MyISAM,。
默认下MySql是使用 Innodb 引擎( mysql 5.5.5以前的版本默认存储引擎是 Myisam )。
Mysql也支持自己定制存储引擎，甚至一个库中不同的表使用不同的存储引擎，这些都是允许的。
### MySQL存储引擎特点
#### MyISAM
1. MyISAM不支持事务，也不支持外键，优势在于访问速度快，应用场景是以insert和select为主的事务完整性要求没那么高的应用
2. MyISAM实现的是表级锁
每个MyISAM表在磁盘中存储成3个文件，文件名和表名相同。dbname.frm(存储表定义)，dbname.MYD(存储数据)，dbname.MYI(存储索引)
3. MyISAM表有3种存储格式：静态表，动态表，压缩表。
#### InnoDB
1. InnoDB存储引擎提供了具有提交，回滚，和奔溃恢复能力的事务安全。但是相对于MyISAM而言，InnoDB写的处理效率要低得多。
2. 外键约束，MySQL支持外键的只有InnoDB。
3. InnoDB实现的是行级锁
4. 多版本并发控制（MVCC）实现高并发支持，实现了SQL标准中四种隔离级别
#### MEMORY
1. MEMORY存储引擎使用存在内存中的内容来创建表。每个MEMORY表实际对应一个磁盘文件，格式.frm。MEMORY表的访问速度非常快，因为数据是放在内存中的，但是一旦服务关闭，表中的数据就会丢失。
2. MEMORY类型存储引擎主要用于那些内容变化不频繁的代码表，或者作为统计操作的中间结果表，便于高效得对中间结果表进行分析并取得最终统计结果
3. 特别注意，MEMORY存储数据是不会写到磁盘中的，但是可以在启动的时候使用--init-file选项，把insert into...select 或者load data infile这样的语句放入到这个文件中，就可以在启动MySQL的时候装载表
4. 表级别锁，不支持Text类型
#### ARCHIVE
1. 只提供insert和select操作
2. 近乎1：10的压缩性能
3. 行级别锁实现高并发插入
4. 很适合日志归档性存储需求
#### MERGE
1. MERGE存储引擎是一组MyISAM表的集合，这些MyISAM表结构完全相同。MERGE表本身没有数据，但是对MERGE表可以进行查询，更新，删除操作，这些操作实际上是针对内部的MyISAM表进行的。
2. MERGE表的创建只需要联合两张或者多张MyISAM表。
create table payment_all(和子表一样的表结构)engine=merge union=(payment_2006,payment_2007) insert_method=last;
# 数据类型特点及优缺点
## 举个例子
![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/7Abu.UHCssNhwOjSqPM0ZI3uxNG*DEigYd2w6TXNojk!/b/dK4AAAAAAAAA&bo=zgSAAgAAAAAFB2w!&rf=viewer_4 "mysql-type")
这个例子中同样的SQL，执行效率相差千倍
为什么会产生这样的问题，怎么样去预防？
## 整数类型
![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/JadsVy.ZEN5cUrBWOKczu07HEwCPyF3AOPNMXKbiIXI!/b/dAIBAAAAAAAA&bo=VgSAAgAAAAAFB*Q!&rf=viewer_4 "int")
误区：
int(size)之类的定义是没有意义的，对一些MySQL交互工具而言，size的作用是结合zerofill用来显示size宽度，对于数值范围和存储来说，int(1)和int(10)的一样的

Tips：
1. 创建一个int类型数据时，只关心数字范围，不需要指定size
2. 选择合适的整数类型，在合适的基础上，越小越好
3. 必要时，指定为unsigned，存储数值范围可提升近一倍
4. ip存储伪int类型，IP转数字函数inet_aton()，数字转IP函数inet_ntoa()，存储，效率将提升
## 实数类型
在 mysql 中 float、double（或 real）是浮点数，decimal（或 numberic）是定点数。
浮点数相对于定点数的优点是在长度一定的情况下，浮点数能够表示更大的数据范围；它的缺点是会引起精度问题。
浮点数和定点数的区别：
![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/xFeT0ty44vtJAMX30Qk2KMiOahVdBS6Z*6lBhykIvXI!/b/dAIBAAAAAAAA&bo=OAS6AQAAAAAFB6M!&rf=viewer_4 "float_2")
我们看到 c1 列的值由 131072.32 变成了 131072.31，这就是浮点数的
不精确性造成的。浮点型数据的误差问题，不管是MySQL，或者其余编程语言，必须考虑的一个问题。
消失的数据
![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/BA3pbmEDWM2g99UKaRNVc1PZOhxxnpPKctwmc.vJPr8!/b/dP8AAAAAAAAA&bo=VANIAgAAAAAFBzk!&rf=viewer_4 "float")
单独查询一条数据时，查询结果出现Empty set
Tips：
 1. 浮点数存在误差问题；
 2. 对货币等对精度敏感的数据，应该用定点数表示或存储；
 3. 编程中，如果用到浮点数，要特别注意误差问题，并尽量避免做浮点数比较；
##字符串类型
![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/KsbPHRqo0VeeFPqLk1xB295uF.N0TuXJngEbchQeAr8!/b/dK0AAAAAAAAA&bo=5wOAAgAAAAAFB0I!&rf=viewer_4 "char")
 误区：
 char(size)和varcahr(size)里的size是指字符数长度，而非字节数，无论是中文，数字，字母，size只跟数量挂钩。例如char(5)最多可以存储5个中文汉字。
```
mysql> create table test2(t1 char(5) CHARACTER SET utf8)CHARACTER SET utf8;
Query OK, 0 rows affected (0.02 sec)

mysql> insert into test2 values ('我是中国人');
Query OK, 1 row affected (0.00 sec)

mysql> select * from test2;
+-----------------+
| t1              |
+-----------------+
| 我是中国人      |
+-----------------+
1 row in set (0.00 sec)
```
Tips:
1. 对于定长的字符类型，比如密码MD5值等，建议用char类型
2. 当列的最大长度比平均长度大很多，并且列很少更新的时候用vachar合适（碎片问题） 
3. 用tinyint类型代替enum类型，enum如果需要新增枚举的话，得全表更新，set也不要用
## 日期和时间类型

![enter image description here](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/Uatg0CC4yneic9U6cbjajFH5jrGv1V4YdGVbrfKuJuU!/b/dK8AAAAAAAAA&bo=cQOAAgAAAAAFB9Q!&rf=viewer_4 "date")
Tips:
时间，年：year，日期：date，时间：尽量用timestamp类型更好


# 索引引进

## 一、索引的本质
------
 - MySQL官方对索引的定义为：**索引（Index）是帮助MySQL高效获取数据的数据结构**。
 - 在数据之外，数据库系统还维护着满足特定查找算法的数据结构，这些数据结构以某种方式引用（指向）数据，这样就可以在这些数据结构上实现高级查找算法。这种数据结构，就是索引。
## 二、索引类型
------

 - 索引的类型 - 按照维护与管理索引角度分为
 ![enter image description here](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/K01jFMEq.UPNnfoVKMfWZdtL4w3v*leLbWldeokMFsQ!/b/dP0AAAAAAAAA&bo=6gIKAgAAAAADB8I!&rf=viewer_4 "FE9AE113-577C-45E0-9D86-047673B77FE2")
 
1. 普通索引
普通索引(由关键字KEY或INDEX定义的索引)的唯一任务是加快对数据的访问速度。因此，应该只为那些最经常出现在查询条件(WHERE column = …)或排序条件(ORDER BY column)中的数据列创建索引。只要有可能，就应该选择一个数据最整齐、最紧凑的数据列(如一个整数类型的数据列)来创建索引。
* 创建索引，例如create index index_name ON table_name (col1);
* 增加索引，例如alter table table_name add index index_name(col1); 
* 创建表的时候指定索引，例如create table table_name ( [...], INDEX 索引的名字 (col1.) );
* 删除索引，例如alter table table_name drop index index_name;

2. 唯一索引
普通索引允许被索引的数据列包含重复的值。比如说，因为人有可能同名，所以同一个姓名在同一个“员工个人资料”数据表里可能出现两次或更多次。
如果能确定某个数据列将只包含彼此各不相同的值，在为这个数据列创建索引的时候就应该用关键字UNIQUE把它定义为一个唯一索引。这么做的好处：一是简化了MySQL对这个索引的管理工作，这个索引也因此而变得更有效率；二是MySQL会在有新记录插入数据表时，自动检查新记录的这个字段的值是否已经在某个记录的这个字段里出现过了；如果是，MySQL将拒绝插入那条新记录。也就是说，唯一索引可以保证数据记录的唯一性。事实上，在许多场合，人们创建唯一索引的目的往往不是为了提高访问速度，而只是为了避免数据出现重复。
* 创建索引，例如create unique index index_name on table_name (col1);  
* 增加索引，例如alert table table_name add unique index_name (col1);  
* 创建表的时候指定索引，例如create table table_name ( [...], unique index_name (col1) );

3. 主键索引
主索引与唯一索引的唯一区别是：前者在定义时使用的关键字是 PRIMARY而不是UNIQUE。
4. 单列索引和多列索引（复合索引） 
单列索引就是常用的一个列字段的索引，常见的索引。 
多列索引就是含有多个列字段的索引，对于多列索引:Mysql会从左到右的使用索引中的字段，一个查询可以只使用索引中的一部份，但只能是最左侧部分。例如索引是key index (a,b,c). 可以支持a | a,b| a,b,c 3种组合进行查找，但不支持 b,c进行查找 .当最左侧字段是常量引用时，索引就十分有效。
> 修改表 alter table table_name add index_name(col1,col2,col3)。

5. 全文索引
文本字段上的普通索引只能加快对出现在字段内容最前面的字符串(也就是字段内容开头的字符)进行检索操作。如果字段里存放的是由几个、甚至是多个单词构成 的较大段文字，普通索引就没什么作用了。这种检索往往以LIKE %word%的形式出现，这对MySQL来说很复杂，如果需要处理的数据量很大，响应时间就会很长。
这类场合正是全文索引(full-text index)可以大显身手的地方。在生成这种类型的索引时，MySQL将把在文本中出现的所有单词创建为一份清单，查询操作将根据这份清单去检索有关的数 据记录。全文索引即可以随数据表一同创建。
注解：InnoDB引擎在MySQL5.6及以上版本才支持全文索引。
> 修改表 ALTER TABLE tablename ADD FULLTEXT(column1, column2)


 - 按照存储方式分为：聚集索引、非聚集索引
 
 1. 聚簇索引
InnoDb的聚簇索引实际上是在同一个结构中保存了B-Tree索引和数据行。 
当表有聚簇索引时，它的数据行实际上存放在索引的叶子页（leaf page）中。“聚簇”就是索引和记录紧密在一起。因为无法同时把数据行存放在两个不同的地方，所以一个表只能有一个聚簇索引（不过，覆盖索引可以模拟多个聚簇索引的情况）。
下图展示了聚簇索引中的记录是如何存放的。叶子页包含行的全部数据，但是节点页只包含了索引列。
![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/Yg0tPcKev.uQPiBpArWkJ7bUDz8DtqlF4CF.bIOU3xI!/b/dPsAAAAAAAAA&bo=ogOAAgAAAAADBwE!&rf=viewer_4 "0FB16F6B-AF2C-40A7-AE28-A94DF8588A7F")
[拓展链接1](http://www.th7.cn/db/mysql/201409/70794.shtml) [拓展链接2](http://www.admin10000.com/document/5372.html) [拓展链接3](http://my.oschina.net/xinxingegeya/blog/495308)

2. 非聚簇索引
索引文件和数据文件分开存放，索引文件的叶子页只保存了主键值，要定位记录还要去查找相应的数据块。

3. 聚簇索引与非聚簇索引对比
InnoDB使用的是聚簇索引，将主键组织到一棵B+树中，而行数据就储存在叶子节点上，若使用"where id = 14"这样的条件查找主键，则按照B+树的检索算法即可查找到对应的叶节点，之后获得行数据。若对Name列进行条件搜索，则需要两个步骤：第一步在辅助索引B+树中检索Name，到达其叶子节点获取对应的主键。第二步使用主键在主索引B+树种再执行一次B+树检索操作，最终到达叶子节点即可获取整行数据。
MyISM使用的是非聚簇索引，非聚簇索引的两棵B+树看上去没什么不同，节点的结构完全一致只是存储的内容不同而已，主键索引B+树的节点存储了主键，辅助键索引B+树存储了辅助键。表数据存储在独立的地方，这两颗B+树的叶子节点都使用一个地址指向真正的表数据，对于表数据来说，这两个键没有任何差别。由于索引树是独立的，通过辅助键检索无需访问主键的索引树。
为了更形象说明这两种索引的区别，我们假想一个表如下图存储了4行数据。其中Id作为主索引，Name作为辅助索引。图示清晰的显示了聚簇索引和非聚簇索引的差异。
**注**：每个InnoDB引擎的表都有一个聚簇索引，除此之外的表上的每个非聚簇索引都是二级索引，又叫辅助索引（secondary indexes）。
　　![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/OKMms4OawIMpb2bqU*XL3fW.DnnDQRJCRKjnkeJ4e9c!/b/dLAAAAAAAAAA&bo=wwKAAgAAAAADAGY!&rf=viewer_4 "64B59F1E-D02D-412B-B068-0FB360AF3C3D")



三、索引的存储结构
-------
- b-tree
当人们在谈论索引的时候，如果没有指定类型，那么多半说的是B-Tree索引，它使用B-Tree数据结构来存储数据。（InnoDB使用的是B+Tree） 关于B-树 和 B+ 见：http://my.oschina.net/xinxingegeya/blog/472668
B-Tree索引能够加快访问数据的速度，因为存储引擎不再需要进行全表扫描来获取需要的数据，取而代之的是从索引的根节点开始进行搜索。根节点的槽中存放了指向子节点的指针，存储引擎根据这些指针向下层查找。通过比较节点页的值和要查找的值可以找到合适的指针进入下层子节点，这些指针实际上定义了子节点页中值的上限和下限。最终存储引擎要么是找到对应的值，要么该记录不存在。
      ![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/Mh.v0mAibvC01WX6a2zfdUeRdkypSdjkTtRN14Sk89I!/b/dKIAAAAAAAAA&bo=.gOaAQAAAAADAEc!&rf=viewer_4 "9A172447-2C33-4181-9F53-5C7A483AD405")[拓展链接](http://blog.jobbole.com/24006/)

- hash
哈希索引基于哈希表实现，只有精确匹配索引所有列的查询才有效。对于每一行数据，存储引擎都会对所有的索引列计算一个哈希吗（hash code），哈希吗是一个较小的值，并且不同键值的行计算出来的哈希码也不一样。哈希索引将所有的哈希码存储在索引中，同时在哈希表中保存指向每个数据行的指针。
在MySQL中，只有Memory引擎显式支持哈希索引。
InnoDB引擎有一个特殊的功能叫做“自适应哈希”，当InnodeDB注意到某些索引值被使用的非常频繁时，它会在内存中基于B-Tree索引之上在创建一个哈希索引，这样就让B-Tree索引也具有哈希索引的一些优点，比如快速的哈希查找。
- 空间数据索引（R-Tree）
MyISAM表支持空间索引，可以用做地理数据存储。和B-Tree索引不同，这类索引无须前缀查询。空间索引会从所有维度来索引数据。查询时，可以有效地使用任意维度来组合查询。必须使用mysql的GIS相关函数如MBRCONTAINS()等来维护数据。mysql的GIS支持并不完善，所以大部分人都不会使用这个特性。开源数据库中对GIS的解决方案做的比较好的是PostgreSQL的postGIS。

- 全文索引（FULL TEXT INDEX）
全文索引是一种特殊类型的索引，他查找的是文本中的关键词，而不是直接比较索引中的值。全文索引和其他几类索引的匹配方式完全不一样。他有许多需要注意的细节，如停用词、词干和复数、布尔搜索等。全文索引更类似与搜索引擎做的事情，而不是简单的where条件匹配。
在相同的列上同时创建全文索引和基于值的B-Tree索引不会有冲突，全文索引适用于MATCH AGAINST操作，而不是普通的where条件操作。
