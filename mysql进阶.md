#概述
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
 char(size)和varcahr(size)里的size是指字符数长度，而非字节数，无论是中文，数字，字母，size只跟数量挂钩。例如char(5)最多可以存储5个中文汉字。而真正的存储字节数，看编码
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
1. 时间，年：year，日期：date，时间：尽量用timestamp类型更好


#索引引进

一、索引的本质
------
 - MySQL官方对索引的定义为：**索引（Index）是帮助MySQL高效获取数据的数据结构**。
 - 在数据之外，数据库系统还维护着满足特定查找算法的数据结构，这些数据结构以某种方式引用（指向）数据，这样就可以在这些数据结构上实现高级查找算法。这种数据结构，就是索引。
二、索引类型
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
注：每个InnoDB引擎的表都有一个聚簇索引，除此之外的表上的每个非聚簇索引都是二级索引，又叫辅助索引（secondary indexes）。
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

三、高性能的索引策略
----------

 - 独立的列作为索引
 如果查询中的列不是独立的，则mysql就不会使用索引，独立的列是指列不能是表达式的一部分，也不能是函数的参数。
 不是独立的列 例：explain select * from campaign_info where id +1 = 2;
 独立的列         例：explain select * from campaign_info where id = 2;
 
 - 使用字段选择性高的的字段来建立索引
  show index from table 查看表上的索引。

	<table>
<tr>
	<th>Table</th>
	<td>表的名称</td>
</tr>
<tr>
	<th>Non_unique</th>
	<td>如果索引不能包括重复词，则为0。如果可以，则为1。</td>
</tr>
<tr>
	<th>Key_name</th>
	<td>索引的名称。</td>
</tr>
<tr>
	<th>Seq_in_index</th>
	<td>索引中的列序列号，从1开始</td>
</tr>
<tr>
	<th>Column_name</th>
	<td>列名称。</td>
</tr>
<tr>
	<th>Collation</th>
	<td>列以什么方式存储在索引中。在MySQL中，有值‘A’（升序）或NULL（无分类）。
</td>
</tr>
<tr>
	<th>Cardinality</th>
	<td>索引中唯一值的数目的估计值。通过运行ANALYZE TABLE或myisamchk -a可以更新。基数根据被存储为整数的统计数据来计数，所以即使对于小型表，该值也没有必要是精确的。基数越大，当进行联合时，MySQL使用该索引的机会就越大</td>
</tr>
<tr>
	<th>Sub_part</th>
	<td>如果列只是被部分地编入索引，则为被编入索引的字符的数目。如果整列被编入索引，则为NULL。</td>
</tr>
<tr>
	<th>Packed</th>
	<td>指示关键字如何被压缩。如果没有被压缩，则为NULL。</td>
</tr>
<tr>
	<th>Null</th>
	<td>如果列含有NULL，则含有YES。如果没有，则该列含有NO。
</td>
</tr>
<tr>
	<th>Index_type</th>
	<td>用过的索引方法（BTREE, FULLTEXT, HASH, RTREE）。</td>
</tr>
</tbody>
	</table>

索引选择性=索引列唯一值/表记录数；
选择性较低索引 可能带来的性能问题
选择性越高索引检索价值越高，消耗系统资源越少；选择性越低索引检索价值越低，消耗系统资源越多；
查询条件含有多个字段时，不要在选择性很低字段上创建索引
可通过创建组合索引来增强低字段选择性和避免选择性很低字段创建索引带来副作用；
尽量减少possible_keys，正确索引会提高sql查询速度，过多索引会增加优化器选择索引的代价，不要滥用索引；

 - 选择合适的索引列顺序
在一个多列的B－Tree索引中，索引列的顺序意味着索引首先按照最左列进行排序，其次是第二列，等等。所以，索引可以按照升序或者降序进行扫描，以满足精确符合列顺序的ORDER BY ,GROUP BY和DISTINCT等子句的查询要求。
对于如何选择索引的列的顺序有一个经验法则：将选择性最高的列放到索引最前列。当不需要考虑排序和分组时，将选择性最高的列放在前面通常是很好的。这时候索引的作用只是用于优化WHERE条件的查找，可以最快的过滤出需要的数据行。但有时我们需要根据那些运行频率最高的查询来调整索引列的顺序。
计算出列的选择性：
select count(distinct twitter_id)/count(*) as twitter_count , count(distinct aid)/count(*) as aid_count from campaign_goods_info; 
 
 - 使用覆盖索引
如果索引包含满足查询的所有数据，就称为覆盖索引。覆盖索引是一种非常强大的工具，能大大提高查询性能。只需要读取索引而不用读取数据有以下一些优点：
(1)索引项通常比记录要小，所以MySQL访问更少的数据；
(2)索引都按值的大小顺序存储，相对于随机访问记录，需要更少的I/O；
(3)大多数据引擎能更好的缓存索引。比如MyISAM只缓存索引。
(4)覆盖索引对于InnoDB表尤其有用，因为InnoDB使用聚集索引组织数据，如果二级索引中包含查询所需的数据，就不再需要在聚集索引中查找了。
覆盖索引不能是任何索引，只有B-TREE索引存储相应的值。而且不同的存储引擎实现覆盖索引的方式都不同，并不是所有存储引擎都支持覆盖索引(Memory就不支持)。
例：explain select aid, goods_id from campaign_goods_info;

四、索引优化案例
------
 - 优化LIMIT分页
在系统中需要进行分页操作的时候，我们通常会用LIMIT加上偏移量的方法来实现，同时加上合适的ORDER BY子句。如果有对应的索引，通常效率会不错，否则，MySQL需要做大量的文件排序操作。
例如可能是LIMIT 1000,20这样的查询，这是MySQL需要扫描10020条记录然后只返回最后20条，前面的10000条记录都将被抛弃，这样做的代价非常高。
优化方法一：
尽可能的使用覆盖索引，而不是查询所有的列。
优化方法二：
LIMIT和OFFSET的问题，其实就是OFFSET的问题，它会导致M有SQL扫描大量不需要的行然后再抛弃掉。如果可以使用书签记录上次取数据的位置，那么下次就可以直接从该书签记录的位置开始扫描，这样就可以避免使用OFFSET。
该技术的好处是，无论翻页到多么后面，其性能都会很好！

# 查询性能优化&分析方式
MySQL查询分析器EXPLAIN或DESC
-------
 - **EXPLAIN描述**
 在分析查询性能时，考虑EXPLAIN关键字很管用。EXPLAIN关键字一般放在SELECT查询语句的前面，用于描述MySQL如何执行查询操作、以及MySQL成功返回结果集需要执行的行数。explain 可以帮助我们分析 select 语句,让我们知道查询效率低下的原因,从而改进我们查询,让查询优化器能够更好的工作。
 
 - **基本用法**
 explain article; —— 相当于desc表结构
 explain select * from article a where a.author_id in (select author_id from user);   —— 表示select查询语句的查询计划
 
 - **每列的含义**
 ```html
<table>
<tr>
<th>id</th>
<td>SELECT识别符。这是SELECT的查询序列号</td>
</tr>
<tr>
<th>select_type</th>
<td>
SIMPLE:简单SELECT(不使用UNION或子查询)<br/>

> 例：explain select * from campaign_info where id = 1;

<br/><br/>
UNION:UNION中的第二个或后面的SELECT语句<br/>

> 例：explain select shop_id from campaign_goods_info where id = 100 union select shop_id from campaign_shop where id = 100;

<br/><br/>
DEPENDENT UNION:当UNION作为子查询时，其中第二个UNION的SELECT_TYPE就是DEPENDENT UNION<br/>

> 例：explain 
select * from campaign_goods_info where aid in (select id from campaign_info where id = 10 union select aid from campaign_goods_info_add where id = 1999)

<br/><br/>
UNION RESULT:UNION 的结果<br/>
> 例：explain select shop_id from campaign_goods_info where id = 100 union select shop_id from campaign_shop where id = 100;
<br/><br/>
PRIMARY:最外面的SELECT<br/>

> 例：explain select * from campaign_goods_info where aid = (select id
> from campaign_info where id = 5000);

<br/><br/>
SUBQUERY:子查询中的第一个SELECT<br/>

> 例：explain select * from campaign_goods_info where aid = (select id from campaign_info where id = 5000);

<br/><br/>
DEPENDENT SUBQUERY:子查询中的第一个 SELECT 查询,依赖于外部查询的结果集<br>
> 例：explain 
select * from campaign_goods_info where aid in (select id from campaign_info where id = 10 union select aid from campaign_goods_info_add where id = 1999)

<br/><br/>
DERIVED:用来表示包含在from子句的子查询中的select，mysql会递归并将结果放到一个临时表中。服务器内部称其为“派生表”，因为该表是从子查询中派生出来的。<br/>

> 例：explain select max(id) from (select * from campaign_info) linshibiao

<br/><br/>
</td>
</tr>
<tr>
<th>table</th>
<td>输出的行所引用的表</td>
</tr>
<tr>
<th>type</th>
<td>联接类型。下面给出各种联接类型,按照从最佳类型到最坏类型进行排序:<br/><br/>
system:表仅有一行(=系统表)。这是const联接类型的一个特例。<br/><br/>
const:表最多有一个匹配行,它将在查询开始时被读取。因为仅有一行,在这行的列值可被优化器剩余部分认为是常数。const表很快,因为它们只读取一次!<br/>

> 例：explain select id from campaign_info where id = 1;

<br/><br/>
eq_ref:唯一性索引扫描，对于每个索引键，表中只有一条记录与之匹配。常见于主键或唯一索引扫描。<br/>

> 例：explain select * from order_info a,order_ext b where a.order_id = b.order_id;

<br/><br/>
ref:非唯一性索引扫描，该表中所有符合检索值的记录都会被取出来和从上一个表中取出来的记录作联合。<br/>

> 例：explain select * from campaign_goods_info where twitter_id in(select  twitter_id from campaign_goods_info_add where aid = 2367)

<br/><br/>
ref_or_null:该联接类型如同ref,但是添加了MySQL可以专门搜索包含NULL值的行。<br/>

> 例：explain select * from order_info where user_id = 111 or user_id is null;

<br/><br/>
index_merge:该联接类型表示使用了索引合并优化方法。<br/>

> 例：explain select * from campaign_goods_info where twitter_id = 455354333 or goods_id = 2423423555;<br/>
> 注释：MySQL 5.0 版本之前，每条个表在查询时 只能使用一个索引,
MySQL 5.0 和之后的版本推出了一个新特性---索引合并优化（Index merge optimization），它让MySQL可以在查询中对一个表使用多个索引,对它们同时扫描，并且合并结果。

<br/><br/>
unique_subquery:子查询使用了unique或者primary key。<br/><br/>
index_subquery:子查询使用了普通索引<br/><br/>
range:只检索给定范围的行,使用一个索引来选择行。<br/>

> 例：explain select * from campaign_goods_info where twitter_id in(3887784417,3813921293,3813921293);

<br/><br/>
index:连接类型跟 all 一样，不同的是它只扫描索引树。它通常会比 all快点，因为索引文件通常比数据文件小。<br/>

> 例：explain select count(*) from campaign_goods_info;
<br/><br/>
ALL:完整的扫描全表，最慢的联接类型，尽可能的避免。<br/>
> 例：explain select * from campaign_goods_info where shop_id = 4;

<br/><br/></td>
</tr>
<tr>
<th>possible_keys</th>
<td>指出MySQL能使用哪个索引在该表中找到行</td>
</tr>
<tr>
<th>key</th>
<td>显示MySQL实际决定使用的键(索引)。如果没有选择索引,键是NULL。</td>
</tr>
<tr>
<tr>
<th>key_len</th>
<td>表示使用的索引长度,是以字节为单位。   <a href=http://www.tuicool.com/articles/yMvMZz target=_blank>拓展链接</a></td>
</tr>
<tr>
<th>ref</th>
<td>显示使用哪个列或常数与key一起从表中选择行。
</td>
</tr>
<tr>
<th>rows</th>
<td>这个数表示mysql要遍历多少数据才能找到，表示MySQL根据表统计信息及索引选用情况，估算的找到所需的记录所需要读取的行数。
</td>
</tr>
<tr>
<th>filtered</th>
<td>	显示了通过条件过滤出的行数的百分比估计值。</td>
</tr>
<tr>
<th>Extra</th>
<td>该列包含MySQL解决查询的详细信息<br/><br/>
range checked for each record (index map: #):MySQL没有发现好的可以使用的索引,但发现如果来自前面的表的列值已知,可能部分索引可以使用。<br/><br/>
Using filesort:MySQL需要额外的一次传递,以找出如何按排序顺序检索行。<br/><br/>
Using index:从只使用索引树中的信息而不需要进一步搜索读取实际的行来检索表中的列信息。<br/><br/>
Using temporary:为了解决查询,MySQL需要创建一个临时表来容纳结果。<br/><br/>
Using where:WHERE 子句用于限制哪一个行匹配下一个表或发送到客户。<br/><br/>
Using sort_union(...), Using union(...), Using intersect(...):这些函数说明如何为index_merge联接类型合并索引扫描。<br/><br/>
Using index for group-by:类似于访问表的Using index方式,Using index for group-by表示MySQL发现了一个索引,可以用来查 询GROUP BY或DISTINCT查询的所有列,而不要额外搜索硬盘访问实际的表。<br/><br/></td>
</tr>
</table>
```
# 并发控制（锁）
## 一、MySQL中的锁（表锁、行锁） ##
**背景：**
当数据库中有多个操作需要修改同一数据时，不可避免的会产生数据的脏读。这时就需要数据库具有良好的并发控制能力，这一切在 MySQL 中都是由服务器和存储引擎来实现的。解决并发问题最有效的方案是引入了锁的机制，锁在功能上分为共享锁 (shared lock) 和排它锁 (exclusive lock) 即通常说的读锁和写锁。

锁是计算机协调多个进程或纯线程并发访问某一资源的机制，它是数据库系统区别于文件系统的一个关键特性。在数据库中，除传统的计算资源（CPU、RAM、I/O）的争用以外，数据也是一种供许多用户共享的资源
![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/orRMZL948Q1EtJa.pwRoSJvN.isM3CPqm0sMLeTBkE8!/b/dK0AAAAAAAAA&bo=rwOAAgAAAAADBww!&rf=viewer_4 "201204022114523079")
![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/R3zTCB5VeYo0sjJ*5zKJpCVLWHJ28*pyHtCnFEyfswY!/b/dAIBAAAAAAAA&bo=gAKcAgAAAAADBz4!&rf=viewer_4 "201204022114569684")
**概述**
相对其他数据库而言，MySQL的锁机制比较简单，其最显著的特点是不同的存储引擎支持不同的锁机制。
MySQL大致可归纳为以下3种锁：

 1. 表级锁：开销小，加锁快；不会出现死锁；锁定粒度大，发生锁冲突的概率最高，并发度最低。
 2. 行级锁：开销大，加锁慢；会出现死锁；锁定粒度最小，发生锁冲突的概率最低，并发度也最高。
 3. 页面锁：开销和加锁时间界于表锁和行锁之间；会出现死锁；锁定粒度界于表锁和行锁之间，并发度一般
 
**注意：**
有两种类型的表级锁：读锁和写锁。
读锁是共享锁，支持并发读，写操作被锁。
写锁是独占锁，上锁期间其他线程不能读表或写表。
如果要支持并发读写，建议采用 InnoDB 表，因为它是采用行级锁，可以获得更多的更新性能。

<h3>MySQL表级锁的锁模式（MyISAM)</h3>
 ＭySQL的表锁有两种模式：表共享读锁（Table Read Lock）和表独占写锁（Table Write Lock）。
 ＭySQL中的表锁兼容性如下表：

|当前锁模式/是否兼容/请求锁模式|读锁|写锁|
|:-:|:-:|:-:|
|读锁 | 是   | 否 |        
|写锁 | 否   |   否 |
 对ＭyISAM表的读操作，不会阻塞其他用户对同一表的读请求，但会阻塞对同一表的写请求；对ＭyISAM表的写操作，则会阻塞其他用户对同一表的读和写请求；ＭyISAM表的读和写操作之间，以及写和写操作之间是串行的！（***当一线程获得对一个表的写锁后，只有持有锁的线程可以对表进行更新操作。其他线程的读、写操作都会等待，直到锁被释放为止。***）
 
**并发锁，表锁并发插入**
    在一定条件下，MyISAM也支持查询和操作的并发进行。
    MyISAM存储引擎有一个系统变量concurrent_insert，专门用以控制其并发插入的行为，其值分别可以为0、1或2。
当concurrent_insert设置为0时，不允许并发插入。
当concurrent_insert设置为1时，如果MyISAM允许在一个读表的同时，另一个进程从表尾插入记录。这也是MySQL的**默认设置**。
当concurrent_insert设置为2时，无论MyISAM表中有没有空洞，都允许在表尾并发插入记录。
可以利用MyISAM存储引擎的并发插入特性，来解决应用中对同一表查询和插入锁争用。例如，将concurrent_insert系统变量为2，总是允许并发插入；同时，通过定期在系统空闲时段执行OPTIONMIZE TABLE语句来整理空间碎片，收到因删除记录而产生的中间空洞。

**MyISAM的锁调度**
MyISAM存储引擎的读和写锁是互斥的，读操作是串行的。那么，一个进程请求某个MyISAM表的读锁，同时另一个进程也请求同一表的写锁，MySQL如何处理呢？答案是写进程先获得锁。
不仅如此，即使读进程先请求先到锁等待队列，写请求后到，写锁也会插到读请求之前！这是因为MySQL认为写请求一般比读请求重要。这也正是MyISAM表不太适合于有大量更新操作和查询操作应用的原因，因为，大量的更新操作会造成查询操作很难获得读锁，从而可能永远阻塞。这种情况有时可能会变得非常糟糕！
我们可以通过一些设置来调节MyISAM的调度行为：

 1. 通过指定启动参数low-priority-updates，使MyISAM引擎默认给予读请求以优先的权利。
 2. 通过执行命令SET LOW_PRIORITY_UPDATES=1，使该连接发出的更新请求优先级降低。
 3. 通过指定INSERT、UPDATE、DELETE语句的LOW_PRIORITY属性，降低该语句的优先级。
 
虽然上面3种方法都是要么更新优先，要么查询优先的方法，但还是可以用其来解决查询相对重要的应用（如用户登录系统）中，读锁等待严重的问题。
另外，MySQL也提供了一种折中的办法来调节读写冲突，即给系统参数**max_write_lock_count**设置一个合适的值，当一个表的读锁达到这个值后，MySQL变暂时将写请求的优先级降低，给读进程一定获得锁的机会。
    上面已经讨论了写优先调度机制和解决办法。这里还要强调一点：一些需要长时间运行的查询操作，也会使写进程“饿死”！因此，**应用中应尽量避免出现长时间运行的查询操作，不要总想用一条SELECT语句来解决问题**。因为这种看似巧妙的SQL语句，往往比较复杂，执行时间较长，在可能的情况下可以通过使用中间表等措施对SQL语句做一定的“分解”，使每一步查询都能在较短时间完成，从而减少锁冲突。**如果复杂查询不可避免，应尽量安排在数据库空闲时段执行**，比如一些定期统计可以安排在夜间执行。


----------

<h3>InnoDB锁问题</h3>
    InnoDB与MyISAM的最大不同有两点：一是支持事务（TRANSACTION）；二是采用了行级锁。
<h5>1、事务（Transaction）及其ACID属性</h5>

事务是由一组SQL语句组成的逻辑处理单元，事务具有4属性，通常称为事务的ACID属性。
原子性（Actomicity）：事务是一个原子操作单元，其对数据的修改，要么全都执行，要么全都不执行。
一致性（Consistent）：在事务开始和完成时，数据都必须保持一致状态。这意味着所有相关的数据规则都必须应用于事务的修改，以操持完整性；事务结束时，所有的内部数据结构（如B树索引或双向链表）也都必须是正确的。
隔离性（Isolation）：数据库系统提供一定的隔离机制，保证事务在不受外部并发操作影响的“独立”环境执行。这意味着事务处理过程中的中间状态对外部是不可见的，反之亦然。
持久性（Durable）：事务完成之后，它对于数据的修改是永久性的，即使出现系统故障也能够保持。

<h5>2、并发事务带来的问题</h5>

相对于串行处理来说，并发事务处理能大大增加数据库资源的利用率，提高数据库系统的事务吞吐量，从而可以支持可以支持更多的用户。但并发事务处理也会带来一些问题，主要包括以下几种情况。
**更新丢失（Lost Update）**：当两个或多个事务选择同一行，然后基于最初选定的值更新该行时，由于每个事务都不知道其他事务的存在，就会发生丢失更新问题——最后的更新覆盖了其他事务所做的更新。例如，两个编辑人员制作了同一文档的电子副本。每个编辑人员独立地更改其副本，然后保存更改后的副本，这样就覆盖了原始文档。最后保存其更改保存其更改副本的编辑人员覆盖另一个编辑人员所做的修改。如果在一个编辑人员完成并提交事务之前，另一个编辑人员不能访问同一文件，则可避免此问题
**脏读（Dirty Reads）**：一个事务正在对一条记录做修改，在这个事务并提交前，这条记录的数据就处于不一致状态；这时，另一个事务也来读取同一条记录，如果不加控制，第二个事务读取了这些“脏”的数据，并据此做进一步的处理，就会产生未提交的数据依赖关系。这种现象被形象地叫做“脏读”。
**不可重复读（Non-Repeatable Reads）**：一个事务在读取某些数据已经发生了改变、或某些记录已经被删除了！这种现象叫做“不可重复读”。
**幻读（Phantom Reads）**：一个事务按相同的查询条件重新读取以前检索过的数据，却发现其他事务插入了满足其查询条件的新数据，这种现象就称为“幻读”。

**注：**不可重复读和脏读的却别是：脏读是读到未提交的数据；而不可重复读读到的确实是已提交的数据，但是其违反了数据库事务一致性的要求。
<h5>3.事务隔离级别</h5>

在并发事务处理带来的问题中，“更新丢失”通常应该是完全避免的。但防止更新丢失，并不能单靠数据库事务控制器来解决，需要应用程序对要更新的数据加必要的锁来解决，因此，防止更新丢失应该是应用的责任。
“脏读”、“不可重复读”和“幻读”，其实都是数据库读一致性问题，必须由数据库提供一定的事务隔离机制来解决。数据库实现事务隔离的方式，基本可以分为以下两种。
一种是在读取数据前，对其加锁，阻止其他事务对数据进行修改。
另一种是不用加任何锁，通过一定机制生成一个数据请求时间点的一致性数据快照（Snapshot），并用这个快照来提供一定级别（语句级或事务级）的一致性读取。从用户的角度，好像是数据库可以提供同一数据的多个版本，因此，这种技术叫做数据**多版本并发控制**（ＭultiVersion Concurrency Control，简称MVCC或MCC），也经常称为多版本数据库。
    数据库的事务隔离级别越严格，并发副作用越小，但付出的代价也就越大，因为事务隔离实质上就是使事务在一定程度上“串行化”进行，这显然与“并发”是矛盾的，同时，不同的应用对读一致性和事务隔离程度的要求也是不同的，比如许多应用对“不可重复读”和“幻读”并不敏感，可能更关心数据并发访问的能力。
    为了解决“隔离”与“并发”的矛盾，ISO/ANSI SQL92定义了４个事务隔离级别，每个级别的隔离程度不同，允许出现的副作用也不同，应用可以根据自己业务逻辑要求，通过选择不同的隔离级别来平衡＂隔离＂与＂并发＂的矛盾
**事务４种隔离级别比较**:
```
|隔离级别/读数据一致性及允许的并发副作用|	读数据一致性|	脏读	|不可重复读	|幻读|
|:-:|:-:|:-:|:-:|:-:|
|未提交读（Read uncommitted）|最低级别，只能保证不读取物理上损坏的数据|	是	|是|	是|
|已提交读（Read committed）|	语句级|	否	|是	|是|
|可重复读（Repeatable read）|	事务级	|否	|否	|是|
|可序列化（Serializable）|	最高级别，事务级|	否|	否	|否|
```
**获取InonoD行锁争用情况**
可以通过检查InnoDB_row_lock状态变量来分析系统上的行锁的争夺情况：
```
		mysql> show status like 'innodb_row_lock%';
		+-------------------------------+-------+
		| Variable_name | Value |
		+-------------------------------+-------+
		| Innodb_row_lock_current_waits | 0 |
		| Innodb_row_lock_time | 725374 |
		| Innodb_row_lock_time_avg | 3181 |
		| Innodb_row_lock_time_max | 11045 |
		| Innodb_row_lock_waits | 228 |
		+-------------------------------+-------+
		5 rows in set (0.00 sec) 

  ``` 
  如果发现争用比较严重，如Innodb_row_lock_waits和Innodb_row_lock_time_avg的值比较高，还可以通过设置InnoDB Monitors来进一步观察发生锁冲突的表、数据行等，并分析锁争用的原因。

<h4>InnoDB的行锁模式及加锁方法</h4>
InnoDB实现了以下两种类型的行锁。
**共享锁**（s）：允许一个事务去读一行，阻止其他事务获得相同数据集的排他锁。   --读锁
**排他锁**（Ｘ）：允许获取排他锁的事务更新数据，阻止其他事务取得相同的数据集共享读锁和排他写锁。  --写锁
另外，为了允许行锁和表锁共存，实现多粒度锁机制，InnoDB还有两种内部使用的意向锁（Intention Locks），这两种意向锁都是表锁。
**意向共享锁**（IS）：事务打算给数据行共享锁，事务在给一个数据行加共享锁前必须先取得该表的IS锁。
**意向排他锁**（IX）：事务打算给数据行加排他锁，事务在给一个数据行加排他锁前必须先取得该表的IX锁。

**InnoDB行锁模式兼容性列表**
```
|当前锁模式/是否兼容/请求锁模式|	X	|IX	|S	|IS|
|:-:|:-:|:-:|:-:|:-:|
|X	|冲突|	冲突	|冲突|	冲突|
|IX|	冲突	|兼容|	冲突|	兼容|
|S	|冲突	|冲突|	兼容|	兼容|
|IS|	冲突	|兼容|	兼容|	兼容|
```
 如果一个事务请求的锁模式与当前的锁兼容，InnoDB就请求的锁授予该事务；反之，如果两者两者不兼容，该事务就要等待锁释放。
    意向锁是InnoDB自动加的，不需用户干预。对于UPDATE、DELETE和INSERT语句，InnoDB会自动给涉及及数据集加排他锁（Ｘ）；对于普通SELECT语句，InnoDB会自动给涉及数据集加排他锁（Ｘ）；对于普通SELECT语句，InnoDB不会任何锁；事务可以通过以下语句显示给记录集加共享锁或排锁。
共享锁（Ｓ）：SELECT * FROM table_name WHERE ... LOCK IN SHARE MODE
排他锁（X）：SELECT * FROM table_name WHERE ... FOR UPDATE
    用SELECT .. IN SHARE MODE获得共享锁，主要用在需要数据依存关系时确认某行记录是否存在，并确保没有人对这个记录进行UPDATE或者DELETE操作。但是如果当前事务也需要对该记录进行更新操作，则很有可能造成死锁，对于锁定行记录后需要进行更新操作的应用，应该使用SELECT ... FOR UPDATE方式获取排他锁。
    
 
**InnoDB行锁实现方式**
    InnoDB行锁是通过索引上的索引项来实现的，这一点ＭySQL与Oracle不同，后者是通过在数据中对相应数据行加锁来实现的。InnoDB这种行锁实现特点意味者：**只有通过索引条件检索数据，InnoDB才会使用行级锁，否则，InnoDB将使用表锁**！
    在实际应用中，要特别注意InnoDB行锁的这一特性，不然的话，可能导致大量的锁冲突，从而影响并发性能。

<h4>什么时候使用表锁</h4>
   对于InnoDB表，在绝大部分情况下都应该使用行级锁，因为事务和行锁往往是我们之所以选择InnoDB表的理由。但在个另特殊事务中，也可以考虑使用表级锁。
第一种情况是：事务需要更新大部分或全部数据，表又比较大，如果使用默认的行锁，不仅这个事务执行效率低，而且可能造成其他事务长时间锁等待和锁冲突，这种情况下可以考虑使用表锁来提高该事务的执行速度。
第二种情况是：事务涉及多个表，比较复杂，很可能引起死锁，造成大量事务回滚。这种情况也可以考虑一次性锁定事务涉及的表，从而避免死锁、减少数据库因事务回滚带来的开销。
    当然，应用中这两种事务不能太多，否则，就应该考虑使用ＭyISAＭ表。
    在InnoDB下 ，使用表锁要注意以下两点。
    （１）使用LOCK TALBES虽然可以给InnoDB加表级锁，但必须说明的是，表锁不是由InnoDB存储引擎层管理的，而是由其上一层ＭySQL Server负责的，仅当autocommit=0、innodb_table_lock=1（默认设置）时，InnoDB层才能知道MySQL加的表锁，ＭySQL Server才能感知InnoDB加的行锁，这种情况下，InnoDB才能自动识别涉及表级锁的死锁；否则，InnoDB将无法自动检测并处理这种死锁。
    （２）在用LOCAK TABLES对InnoDB锁时要注意，要将AUTOCOMMIT设为0，否则ＭySQL不会给表加锁；事务结束前，不要用UNLOCAK TABLES释放表锁，因为UNLOCK TABLES会隐含地提交事务；COMMIT或ROLLBACK产不能释放用LOCAK TABLES加的表级锁，必须用UNLOCK TABLES释放表锁，正确的方式见如下语句。
    例如，如果需要写表t1并从表t读，可以按如下做：
*SET AUTOCOMMIT=0;
LOCAK TABLES t1 WRITE, t2 READ, ...;
[do something with tables t1 and here];
COMMIT;
UNLOCK TABLES;*

<h4>关于死锁</h4>

   ＭyISAM表锁是deadlock free的，这是因为ＭyISAM总是一次性获得所需的全部锁，要么全部满足，要么等待，因此不会出现死锁。但是在InnoDB中，除单个SQL组成的事务外，锁是逐步获得的，这就决定了InnoDB发生死锁是可能的。
    发生死锁后，InnoDB一般都能自动检测到，并使一个事务释放锁并退回，另一个事务获得锁，继续完成事务。但在涉及外部锁，或涉及锁的情况下，InnoDB并不能完全自动检测到死锁，这需要通过设置锁等待超时参数innodb_lock_wait_timeout来解决。需要说明的是，这个参数并不是只用来解决死锁问题，在并发访问比较高的情况下，如果大量事务因无法立即获取所需的锁而挂起，会占用大量计算机资源，造成严重性能问题，甚至拖垮数据库。我们通过设置合适的锁等待超时阈值，可以避免这种情况发生。
    通常来说，死锁都是应用设计的问题，通过调整业务流程、数据库对象设计、事务大小、以及访问数据库的SQL语句，绝大部分都可以避免。下面就通过实例来介绍几种死锁的常用方法。
    （１）在应用中，如果不同的程序会并发存取多个表，应尽量约定以相同的顺序为访问表，这样可以大大降低产生死锁的机会。如果两个session访问两个表的顺序不同，发生死锁的机会就非常高！但如果以相同的顺序来访问，死锁就可能避免。
    （２）在程序以批量方式处理数据的时候，如果事先对数据排序，保证每个线程按固定的顺序来处理记录，也可以大大降低死锁的可能。
    （３）在事务中，如果要更新记录，应该直接申请足够级别的锁，即排他锁，而不应该先申请共享锁，更新时再申请排他锁，甚至死锁。
    （４）在REPEATEABLE-READ隔离级别下，如果两个线程同时对相同条件记录用SELECT...ROR UPDATE加排他锁，在没有符合该记录情况下，两个线程都会加锁成功。程序发现记录尚不存在，就试图插入一条新记录，如果两个线程都这么做，就会出现死锁。这种情况下，将隔离级别改成READ COMMITTED，就可以避免问题。
    （５）当隔离级别为READ COMMITED时，如果两个线程都先执行SELECT...FOR UPDATE，判断是否存在符合条件的记录，如果没有，就插入记录。此时，只有一个线程能插入成功，另一个线程会出现锁等待，当第１个线程提交后，第２个线程会因主键重出错，但虽然这个线程出错了，却会获得一个排他锁！这时如果有第３个线程又来申请排他锁，也会出现死锁。对于这种情况，可以直接做插入操作，然后再捕获主键重异常，或者在遇到主键重错误时，总是执行ROLLBACK释放获得的排他锁。
 
   尽管通过上面的设计和优化等措施，可以大减少死锁，但死锁很难完全避免。因此，在程序设计中总是捕获并处理死锁异常是一个很好的编程习惯。
    如果出现死锁，可以用**SHOW ENGINE INNODB STATUS**命令来确定最后一个死锁产生的原因和改进措施。

**注意：** InnoDB 引擎解决死锁的方案是将持有最少排它锁的事务进行回滚。


<h4>实际场景中如何避免锁的资源竞争</h4>

 1. 让 SELECT 速度尽量快，尽量减少大的复杂的Query，将复杂的Query分拆成几个小的Query分步进行；
 2. 尽可能地建立足够高效的索引，让数据检索更迅速；
 3. 使用EXPLAIN SELECT来确定对于你的查询,MySQL认为哪个索引是最适当的，**优化你的SQL**
 4. 当在同一个表上同时有插入和删除操作时， INSERT DELAYED 可能会很有用；
 5. 当 SELECT 和 DELETE 一起使用出现问题时， DELETE 的 LIMIT 参数可能会很有用；
 6. 合理利用读写优先级（比如：用 LOW_PRIORITY 属性来降低 INSERT ， UPDATE ， DELETE 的优先级；用 HIGH_PRIORITY 来提高 SELECT 语句的优先级）
 7.  ......
 
 #事务处理及优缺点
 
 Mysql事务处理
---------

#### 1、概述：
事务是一组原子性sql查询语句，被当作一个工作单元。若mysql对改事务单元内的所有sql语句都正常的执行完，则事务操作视为成功，所有的sql语句才对数据生效，若sql中任意不能执行或出错则事务操作失败，所有对数据的操作则无效（通过回滚恢复数据）。

数据库引入事务的主要目的：事务会把数据库从一种一致状态转换为另一种一致状态。在数据库提交工作时，可以确保其要么所有修改都已经保存了，要么所有修改都不保存。

#### 2、事务（Transaction）的 ACID 特性：
**原子性 (atomicity)**: 事务中的所有操作要么全部提交成功，要么全部失败回滚。
**一致性 (consistency)**: 数据库总是从一个一致性状态转换到另一个一致性状态。
**隔离性 (isolation)**: 一个事务所做的修改在提交之前对其它事务是不可见的。
**持久性 (durability)**: 一旦事务提交，其所做的修改便会永久保存在数据库中。

#### 3、MYSQL事务处理的两种方式：
 1. 用begin,rollback,commit来实现
 开始：START TRANSACTION或BEGIN语句可以开始一项新的事务；推荐用START TRANSACTION 是SQL-99标准启动一个事务
 提交：COMMIT可以提交当前事务，是变更成为永久变更
 回滚：ROLLBACK可以回滚当前事务，取消其变更
 
	注，其他事务控制语句：
	SAVEPOINT identifier:  SAVEPOINT允许你在事务中创建一个保存点，一个事务中可以有多个SAVEPOINT。
	RELEASE SAVEPOINT identifier: 删除一个事务的保存点，当没有一个保存点执行这句语句时，会抛出一个异常。
	ROLLBACK TO [SAVEPOINT] identifier : 这个语句与SAVEPOINT命令一起使用，可以把事务回滚到标记点，而不回滚在此标记点之前的任何工作。

 2. 直接用set来改变mysql的自动提交模式，**用于当前连接**
 MYSQL默认是自动提交的，也就是你提交一个QUERY，它就直接执行！我们可以通过
set autocommit=0 禁止自动提交
set autocommit=1 开启自动提交
来实现事务的处理。当你用 set autocommit=0 的时候，你以后所有的SQL都将做为事务处理，直到你用commit确认或rollback结束。

	**注意：**当你结束这个事务的同时也开启了个新的事务！按第一种方法只将当前的作为一个事务！**推荐使用第一种方法！**

#### 4、MYSQL事务的实现：
 1. Redo Log
	 在InnoDB存储引擎中，事务日志是通过重做（redo）日志文件和InnoDB存储引擎的日志缓冲（InnoDB Log Buffer）来实现的。当开始一个事务的时候，会记录该事务的LSN (Log Sequence Number，日志序列号); 当事务执行时，会往InnoDB存储引擎的日志
 的日志缓存里面插入事务日志；当事务提交时，必须将InnoDB存储引擎的日志缓冲写入磁盘（通过innodb_flush_log_at_trx_commit来控制，默认的实现，即innodb_flush_log_at_trx_commit=1）。也就是写数据前，需要先写日志。这种方式称为“预写日志方式”（Write-Ahead Logging, WAL）。

 InnoDB通过此方式来保证事务的完整性。也就意味着磁盘上存储的数据页和内存缓冲池上面的页是不同步的，对于内存缓冲池中页的修改，先是先写入重做日志文件（redo log），然后再写入磁盘（data file），因此是一种异步的方式。可以通过 show engine innodb status\G 来观察当前磁盘和日志之间的“差距”。
 
 2. Undo 
	重做日志记录了事务的行为，可以很好地通过其进行“重做”。但是事务有时还需要撤销，这是就需要undo。undo与redo正好相反，对于数据库进行修改时，数据库不但会产生redo，而且还会产生一定量的undo，即使你执行的事务或语句由于某种原因失败了，或者如果你用一条ROLLBACk语句请求回滚，就可以利用这些undo信息将数据回滚到修改之前的样子。与redo不同的是，redo存放在重做日志文件中，undo存放在数据库内部的一个特殊段（segment）中，这称为undo段（undo segment），undo段位于共享表空间内。

#### 5、事务的隔离级别
ANSI/ISO SQL标准定义了4中事务隔离级别：未提交读（read uncommitted），提交读（read committed），重复读（repeatable read），串行读（serializable）。

对于不同的事务，采用不同的隔离级别分别有不同的结果。不同的隔离级别有不同的现象。主要有下面3种现在：
1、**脏读（dirty read）**：一个事务可以读取另一个尚未提交事务的修改数据。
2、**非重复读（nonrepeatable read）**：在同一个事务中，同一个查询在T1时间读取某一行，在T2时间重新读取这一行时候，这一行的数据已经发生修改，可能被更新了（update），也可能被删除了（delete）。
3、**幻像读（phantom read）**：在同一事务中，同一查询多次进行时候，由于其他插入操作（insert）的事务提交，导致每次返回不同的结果集。

不同的隔离级别有不同的现象，并有不同的锁定/并发机制，隔离级别越高，数据库的并发性就越差，4种事务隔离级别分别表现的现象如下表：
|隔离级别|	脏读|	非重复读|	幻像读|
|:-:|:-:|:-:|:-:|
|read uncommitted|允许|允许|允许|
|read committed||允许|允许|
|repeatable read|||允许|
|serializable||| |

#### 6、设置事务的隔离级别
用户可以用SET TRANSACTION语句改变单个会话或者所有新进连接的隔离级别。它的语法如下：

	SET [SESSION | GLOBAL] TRANSACTION ISOLATION LEVEL {READ UNCOMMITTED | READ COMMITTED | REPEATABLE READ | SERIALIZABLE}

**注意：**默认的行为（不带session和global）是为下一个（未开始）事务设置隔离级别。如果你使用GLOBAL关键字，语句在全局对从那点开始创建的所有新连接（除了不存在的连接）设置默认事务级别。你需要SUPER权限来做这个。使用SESSION 关键字为将来在当前连接上执行的事务设置默认事务级别。 任何客户端都能自由改变会话隔离级别（甚至在事务的中间），或者为下一个事务设置隔离级别。

可以用下列语句查询全局和会话事务隔离级别：

	mysql> SELECT @@global.tx_isolation; 
	+-----------------------+
	| @@global.tx_isolation |
	+-----------------------+
	| REPEATABLE-READ       |
	+-----------------------+
	1 row in set (0.00 sec)
	mysql>
	mysql> SELECT @@session.tx_isolation; 
	+------------------------+
	| @@session.tx_isolation |
	+------------------------+
	| REPEATABLE-READ        |
	+------------------------+
	1 row in set (0.00 sec)
	mysql> SELECT @@tx_isolation;
	+-----------------+
	| @@tx_isolation  |
	+-----------------+
	| REPEATABLE-READ |
	+-----------------+
	1 row in set (0.00 sec)
	
#### 7、数据库中的默认事务隔离级别
在Oracle中默认的事务隔离级别是提交读（read committed）。
对于MySQL的Innodb的默认事务隔离级别是重复读（repeatable read）。可以通过下面的命令查看：		
```
	mysql -h10.8.3.29 -P3337 -umlstmpdb -p****
	mysql> SELECT @@GLOBAL.tx_isolation,@@tx_isolation;
	+———————–+—————–+
	| @@GLOBAL.tx_isolation | @@tx_isolation  |
	+———————–+—————–+
	| REPEATABLE-READ | REPEATABLE-READ |
	+———————–+—————–+
	1 row in set (0.00 sec) 
	
可以利用如下语句查询并临时修改隔离级别:
	
	mysql> show variables like 'tx_isolation';
	+---------------+-----------------+
	| Variable_name | Value           |
	+---------------+-----------------+
	| tx_isolation  | REPEATABLE-READ |
	+---------------+-----------------+
	1 row in set (0.00 sec)
	mysql> set tx_isolation = 'READ-COMMITTED';
	Query OK, 0 rows affected (0.00 sec)
	mysql> show variables like 'tx_isolation';
	+---------------+----------------+
	| Variable_name | Value          |
	+---------------+----------------+
	| tx_isolation  | READ-COMMITTED |
	+---------------+----------------+
	1 row in set (0.00 sec)
	mysql>
```
####8、数据库中事务的种类

 1. 扁平事务；（应用场景中用的比较多的）
 2. 带有保存点的扁平事务；
 3. 链事务；
 4. 嵌套事务；
 5. 分布式事务。
 参考：http://www.jb51.net/article/64005.htm   
   
#### 9、不能回滚的语句
有些语句不能被回滚。通常，这些语句包括数据定义语言（DDL）语句，比如创建或取消数据库的语句，和创建、取消或更改表或存储的子程序的语句。

在设计事务时，不应包含这类语句。如果您在事务的前部中发布了一个不能被回滚的语句，则后部的其它语句会发生错误，在这些情况下，通过发布ROLLBACK语句不能 回滚事务的全部效果。


----------


**延伸：**
1、**DDL**（Data Definition Language）数据定义语言，于定义和管理 SQL 数据库中的所有对象的语言
CREATE、ALTER、DROP、TRUNCATE、COMMENT、RENAME
2、**DML**（Data Manipulation Language）数据操纵语言，SQL中处理数据等操作统称为数据操纵语言
SELECT、INSERT、UPDATE、DELETE、MERGE、CALL、EXPLAIN、PLAN、LOCK TABLE
3、**DCL**（Data Control Language）数据控制语言  授权，角色控制等
GRANT 授权
REVOKE 取消授权
4、**TCL**（Transaction Control Language）事务控制语言
COMMIT 提交
SAVEPOINT 设置保存点
ROLLBACK  回滚
SET TRANSACTION

#### 10、优缺点总结
并发控制（锁）中已做了相关说明，这里再补充一下
 1. 优点
	 a、事务处理是一组原子性的操作，内部有一个失败，则回滚；保证数据的一致性
	 b、行级锁定，利于数据的快速更新
	 c、事务的隔离性，使得事务可以并发的工作，提高并发
 2. 缺点
	 a、相对比较消耗内存；事务处理时间越长，影响越大；
	 b、死锁等现象影响性能
	 c、单个连接不支持夸库事务处理
	 d、任何可能让事务提交发生延迟的操作代价都很大，因为它影响的不仅仅是自己本身，它还会让所有参与者都在等待


注：在应用程序中，最好的做法是把事务的START TRANSACTION 、COMMIT、ROLLBACK操作交给程序端来完成，而不是在存储过程内完成。
	 	       
 # mysql分布式事务
 ## 分布式事务 ##

### 概述
在MySQL 各个版本中，只有从MySQL 5.0 开始以后的各个版本才开始对分布式事务提供支持，而且目前仅有Innodb 提供分布式事务支持

Innodb存储引擎支持XA事务，通过XA事务可以支持分布式事务的实现。分布式事务指的是允许多个独立的事务资源（transac tional resources）参与一个全局的事务中。事务资源通常是关系型数据库系统，也可以是其它类型的资源。全局事务要求在其中所有参与的事务要么全部提交，要么全部回滚，这对于事务原有的ACID要求又有了提高。另外，在使用分布式事务时候，InnoDB存储引擎的事务隔离级别必须设置成serialiable。

Mysql 的XA事务分为内部XA和外部XA。 外部XA可以参与到外部的分布式事务中，需要应用层介入作为协调者；内部XA事务用于同一实例下跨多引擎事务，由Binlog作为协调者，比如在一个存储引擎提交时，需要将提交信息写入二进制日志，这就是一个分布式内部XA事务，只不过二进制日志的参与者是MySQL本身。 Mysql 在XA事务中扮演的是一个参与者的角色，而不是协调者。

XA事务允许不同数据库之间的分布式事务，如：一台服务器是mysql数据库，一台是Oracle的，又有可能还有一台是sqlserver的，只要参与全局事务中的每个节点都支持XA事务。分布式事务可能在银行系统的转帐中比较常见，如一个用户需要从上海转10000元到北京的一个用户账号上面：

	# bank ofshanghai：
	Updateuser_account set money=money – 10000 where user=’xiaozhang’; 
	
	# bank ofBeijing：
	Updateuser_account set money= money + 10000 where user=’xiaoli’; 

像这种情况一定需要分布式的事务，要不都提交，要么都回滚。在任何一个节点出问题都会造成严重的结果：1 xiaozhang的帐号被扣款，但是xiaoli没有收到钱；2 xiaozhang的帐号没有被扣款，但是xiaoli收到钱了。

分布式事务是由一个或者多个资源管理器（Resource Managers），一个事务管理器（Transaction Manager）以及一个应用程序（Application Program）组成。

**资源管理器**：提供访问事务资源的方法，通常一个数据库就是一个资源管理器。
**事务管理器**：协调参与全局事务中的各个事务。需要和参与全局事务中的资源管理器进行通信。
**应用程序**：定义事务的边界，指定全局事务中的操作。

在mysql中的分布式事务中，资源管理器就是mysql数据库，事务管理器为连接到mysql服务器的客户端。如下图所示：
![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/VEPk93AE.zEqS*DTPcbGdR5BsJx4kY58xHcCw2tFSSs!/b/dK0AAAAAAAAA&bo=6gJUAQAAAAAFB5k!&rf=viewer_4 "131026073574501")

分布式事务使用两段式提交（two-phase commit）的方式。在第一个阶段，所有参与全局事务的节点都开始准备，告诉事务管理器它们准备好提交了。第二个阶段，事务管理器告诉资源管理器执行rollback或者commit，如果任何一个节点显示不能commit，那么所有的节点就得全部rollback。

**注：**参数innodb_support_xa可以查看是否启用了XA事务支持（主库默认为ON）：
```
	mysql> show variables like 'innodb_support_xa';
	+-------------------+-------+
	| Variable_name     | Value |
	+-------------------+-------+
	| innodb_support_xa | OFF   |
	+-------------------+-------+
	1 row in set (0.00 sec)

设置innodb_support_xa：

	mysql> set innodb_support_xa=1;
	Query OK, 0 rows affected (0.00 sec)
	mysql> show variables like 'innodb_support_xa';
	+-------------------+-------+
	| Variable_name     | Value |
	+-------------------+-------+
	| innodb_support_xa | ON    |
	+-------------------+-------+
	1 row in set (0.01 sec)
```
建议：主库开启innodb_support_xa=1，从库不开（因为从库一般不会记binlog），数据一致性还是很重要的。

课外拓展：
MySQL XA 事务基本语法：http://blog.csdn.net/luckyjiuyi/article/details/46955337
分布式事务案例：http://www.cnblogs.com/yjf512/p/5166391.html
php + mysql 分布式事务： http://blog.csdn.net/ltp901127/article/details/49663309

#InnoDB
### 1、MySQL体系结构

![enter image description here](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/By8aB1IaygrnY0Ostv9BPCh9Pt963WiJdCW4tmxRiWQ!/b/dKMAAAAAAAAA&bo=lwOJAgAAAAADBz0!&rf=viewer_4 "mysql_framework")

 1. Connectors(连接器)：指不同语言（PHP/Perl/Python/Ruby）中与SQL的交互
 2. Management Services & Utilites(服务管理与控制工具)：包括数据备份与恢复、安全、复制、集群、权限管理、配置等
 3. Connection Pool(连接池)：认证、线程重用、管理缓冲用户连接
 4. SQL Interface(SQL接口)：DML，DDL，存储过程，视图，触发器等等。
 5. Parser(解析器)：验证和解析SQL语句
 6. Optimizer(查询优化器)：查询之前进行优化，“选取-投影-联接”策略
 7. Caches & Buffers(查询缓存)：小缓存组成
 8. Pluggable Storage Engines(可插拔的存储引擎)：一个抽象接口来定制一种文件访问机制

### 2、二分查找（折半查找法）
指：将记录按有序化（递增或递减）排序，在查找过程中采用跳跃式方式查找，即先以有序数列的重点位置为比较对象，如果要找的元素小于该中点元素，则将待查序列缩小为左半部分，否则为有半部分；通过一次比较，将查找区间缩小一半，直到找到或者到最后一个数为止

如：2、3、5、6、、7、8
![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/Y6.vR1b8g2Y8mOcBoMlURC1H0CFyfwLY8IV4bf9b.fo!/b/dKcAAAAAAAAA&bo=.wBiAAAAAAAFB70!&rf=viewer_4 "2fenfa")
（1）二分查找平均次数 = (3+2+3+1+3+2)/6 = 2.3
（2）顺序查找平均次数 = (1+2+3+4+5+6)/6 = 3
而且随着数据量的怎么大，差距越来越大
（3）平衡二叉查找树，平均查找次数 = (1+2+2+3+3+3)/6 = 2.3（平衡二叉查找树 是二分查找的一种算法实现）
![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/mNh1zhqtpAPPaHlLdbK6ZEADSUTJAlJ9QltGlBCQNZI!/b/dKoAAAAAAAAA&bo=kwE3AQAAAAAFAIc!&rf=viewer_4 "bst")


----------


### 3、数据结构

 1. 二叉树
		指：二叉树的每个结点至多只有二棵子树(度<=2)，二叉树的子树有左右之分，次序不能颠倒
		特点：
			（1）度 <= 2
			（2）左右之分
 2. 二叉搜索树（二叉查找树）Binary Search Tree
		 指：每个节点都不比它左子树的任意元素小，而且不比它的右子树的任意元素大的二叉树
		 特点：
				 （1）二叉树 + 节点数据大小关系
 3. 平衡二叉树 Balanced Binary Tree（AVL树 AVerage Length Tree）
		 指：它是一棵空树或它的左右两个子树的深度差的绝对值不超过1，并且左右两个子树都是一棵平衡二叉树
		 特点：
				 （1）左右子树深度差的绝对值 <= 1
 4. 平衡二叉查找树
		 指：它是一棵空树或它的左右两个子树的深度差的绝对值不超过1，并且左右两个子树都是一棵平衡二叉查找树
 5. 完全二叉树
		 指：二叉树的深度为h，除第 h 层外，其它各层 (1～h-1) 的结点数都达到最大个数，第 h 层所有的结点都连续集中在最左边
		 特点：
				 （1）只允许最后一层有空缺结点且空缺在右边，即叶子结点只能在层次最大的两层上出现
 6. 满二叉树
		 指：除最后一层无任何子节点外，每一层上的所有结点都有两个子结点（非叶子节点都有两个子节点）
		 特点：
				 （1）总节点数：2^h - 1，一定是奇数
				 （2）总叶子数：2^(h - 1)，一定是偶数
				 （3）第k层节点数：2^(k - 1)，一定是偶数
				 （4）满二叉树一定是完全二叉树，完全二叉树不一定是满二叉树
 
### 3、B+树相关

 1. B树：平衡二叉查找树
		 每个节点只储存一个关键字，等于即命中，小于查左子树，大于查右子树，递归往下知道找到关键字或者找到叶子节点
![enter image description here](http://a3.qpic.cn/psb?/V11ViYzL3kHi5M/klyG1SYOc*gYBj1RC3fYZJm5jJggKkSG9hsNxIzy7QU!/b/dKoAAAAAAAAA&bo=9wD8AAAAAAAFACg!&rf=viewer_4 "B")
 2. B-树：多路搜索树（并不是二叉的）
		 每个节点上存储[M/2, M]个关键字（且M > 2，根节点的儿子树[2, M]），非叶子节点存储指向关键字范围的子节点，所有关键字在整个数中出现且只出现一次，非叶子节点可以命中
		 特点：
				 （1）定义任意非叶子结点最多只有M个儿子；且M>2；
				 （2）根结点的儿子数为[2, M]；
				 （3）除根结点以外的非叶子结点的儿子数为[M/2, M]；
				 （4）每个结点存放至少M/2-1（取上整）和至多M-1个关键字；（至少2个关键字）
				 （5）非叶子结点的关键字个数=指向儿子的指针个数-1；
				 （6）非叶子结点的关键字：K[1], K[2], …, K[M-1]；且K[i] < K[i+1]；
				 （7）非叶子结点的指针：P[1], P[2], …, P[M]；其中P[1]指向关键字小于K[1]的子树，P[M]指向关键字大于K[M-1]的子树，其它P[i]指向关键字属于(K[i-1], K[i])的子树；
				（8）所有叶子结点位于同一层；
如：M = 3，下图：
![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/DrSsjHoXaybUlPs9PZJBj*pw4o5iuHLq.1MbEZwmYYw!/b/dAIBAAAAAAAA&bo=oAPfAQAAAAAFAF4!&rf=viewer_4 "B- tree")
 3. B+树
		 特点：
				 （1）其定义基本与B-树同，除了：
				 （2）非叶子结点的子树指针与关键字个数相同；
				 （3）非叶子结点的子树指针P[i]，指向关键字值属于[K[i], K[i+1])的子树（B-树是开区间）；
				 （4）为所有叶子结点增加一个链指针；
				 （5）所有关键字都在叶子结点出现；
如：M = 3，下图：
 ![enter image description here](http://a2.qpic.cn/psb?/V11ViYzL3kHi5M/M6JP.OAP69t4Q6RLateUynGAMUM4he9tA0rB0Dy07M0!/b/dKkAAAAAAAAA&bo=oQPQAQAAAAADAFY!&rf=viewer_4 "B+ tree")
 4. B*树
			是B+树的变体，在B+树的非根和非叶子结点再增加指向兄弟的指针；
![enter image description here](http://a1.qpic.cn/psb?/V11ViYzL3kHi5M/3Hgyp7trGol.PfGwqXMUSOxM1cOE1OX8toQLW*uw4Rc!/b/dKsAAAAAAAAA&bo=oQPHAQAAAAAFAEc!&rf=viewer_4 "B* tree")




    
    
