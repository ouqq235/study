## 背景

 * （1）Linux Shell本身实用又好用的特性
 * （2）业务中有很多地方用得着，或者用上之后 效果更好

## 定位
 * （1）重点分享通用、实用技巧
 * （2）目标不是 零基础的人员，市面上书很多，我们不是简单的cp
 * （3）可作为工具书用

## 要求
 * （1）所有写在里面的东西，必须得亲测没有问题
 ##简述
Shell 是一种具备特殊功能的程序，他提供了用户与内核进行交互操作的一种接口。她接收用户输入的命令，并把它送入内核去执行。内核是Linux系统的心脏，从开机自检时就驻留在计算机的内存中，直到计算机关闭为止，而用户的应用程序存储在计算机的硬盘上，仅当需要时才被调入内存。Shell是一种应用程序，当用户登录Linux系统时，Shell就会被调入内存执行。Shell独立于内核，它是连接内核和应用程序的桥梁，并由输入设备读取命令，再将其转为计算机可以理解的机械码，Linux内核才能执行该命令。Shell在Linux系统中的位置，如下图：
![enter image description here](http://bizfe.meilishuo.com/md-imgs/941fdce2c97499a267e5da34e195dbbf.png "91747CFA-6C54-410A-AEEA-392E7509C391")

简单点说：在登录到注销期间，输入的每个命令都会经常解译及执行，而这个负责的机制就是Shell.

##Shell的种类
	  [work(0)@yz-lab-675 12:04:57 ~]# ls -l /bin/*sh
	  -rwxr-xr-x. 1 root root 906792 Sep  5  2014 /bin/bash
	  -rwxr-xr-x. 1 root root 106216 Oct 17  2012 /bin/dash
	  lrwxrwxrwx. 1 root root      4 Jun 14  2015 /bin/sh -> bash

**更改使用某种类型的shell:**
**chsh -s 输入新的shell** 		比如：/bin/dash
		       
	[work(0)@yz-lab-675 16:38:36 ~]# chsh -s /bin/dash
	Changing shell for work.
	Password: 
需要用root账号更改:
	
	[root(0)@yz-lab-675 16:44:11 ~]# chsh -s /bin/dash
	Changing shell for root.
	Shell changed. 
生效需要注销重新登录:

	[root(0)@yz-lab-675 16:44:31 ~]# logout  (或exit)
	2014-019deMacBook-Pro:fedev MLS$ relay rdlab root
	# 
	# ls 

**当前的Shell，及当前操作系统的环境变量**

	[work(0)@yz-lab-675 12:12:06 ~]# env
	HOSTNAME=yz-lab-675.meilishuo.com
	TERM=xterm-256color
	SHELL=/bin/bash
	HISTSIZE=1000
	SSH_CLIENT=10.8.1.13 38059 22
	SSH_TTY=/dev/pts/0
	JRE_HOME=/home/service/source/jdk1.7.0_55/jre
	USER=work
	LS_COLORS=rs=0:di=38;5;27:ln=38;5;51:mh=44;38;5;15:pi=40;38;5;11:so=38;5;13:do=38;5;5:bd=48;5;232;38;5;11:cd=48;5;232;38;5;3:or=48;5;232;38;5;9:mi=05;48;5;232;38;5;15:su=48;5;196;38;5;15:sg=48;5;11;38;5;16:ca=48;5;196;38;5;226:tw=48;5;10;38;5;16:ow=48;5;10;38;5;21:st=48;5;21;38;5;15:ex=38;5;34:*.tar=38;5;9:*.tgz=38;5;9:*.arj=38;5;9:*.taz=38;5;9:*.lzh=38;5;9:*.lzma=38;5;9:*.tlz=38;5;9:*.txz=38;5;9:*.zip=38;5;9:*.z=38;5;9:*.Z=38;5;9:*.dz=38;5;9:*.gz=38;5;9:*.lz=38;5;9:*.xz=38;5;9:*.bz2=38;5;9:*.tbz=38;5;9:*.tbz2=38;5;9:*.bz=38;5;9:*.tz=38;5;9:*.deb=38;5;9:*.rpm=38;5;9:*.jar=38;5;9:*.rar=38;5;9:*.ace=38;5;9:*.zoo=38;5;9:*.cpio=38;5;9:*.7z=38;5;9:*.rz=38;5;9:*.jpg=38;5;13:*.jpeg=38;5;13:*.gif=38;5;13:*.bmp=38;5;13:*.pbm=38;5;13:*.pgm=38;5;13:*.ppm=38;5;13:*.tga=38;5;13:*.xbm=38;5;13:*.xpm=38;5;13:*.tif=38;5;13:*.tiff=38;5;13:*.png=38;5;13:*.svg=38;5;13:*.svgz=38;5;13:*.mng=38;5;13:*.pcx=38;5;13:*.mov=38;5;13:*.mpg=38;5;13:*.mpeg=38;5;13:*.m2v=38;5;13:*.mkv=38;5;13:*.ogm=38;5;13:*.mp4=38;5;13:*.m4v=38;5;13:*.mp4v=38;5;13:*.vob=38;5;13:*.qt=38;5;13:*.nuv=38;5;13:*.wmv=38;5;13:*.asf=38;5;13:*.rm=38;5;13:*.rmvb=38;5;13:*.flc=38;5;13:*.avi=38;5;13:*.fli=38;5;13:*.flv=38;5;13:*.gl=38;5;13:*.dl=38;5;13:*.xcf=38;5;13:*.xwd=38;5;13:*.yuv=38;5;13:*.cgm=38;5;13:*.emf=38;5;13:*.axv=38;5;13:*.anx=38;5;13:*.ogv=38;5;13:*.ogx=38;5;13:*.aac=38;5;45:*.au=38;5;45:*.flac=38;5;45:*.mid=38;5;45:*.midi=38;5;45:*.mka=38;5;45:*.mp3=38;5;45:*.mpc=38;5;45:*.ogg=38;5;45:*.ra=38;5;45:*.wav=38;5;45:*.axa=38;5;45:*.oga=38;5;45:*.spx=38;5;45:*.xspf=38;5;45:
	SSH_AUTH_SOCK=/tmp/ssh-kdDby14317/agent.14317
	MAVEN_HOME=/home/service/source/apache-maven-3.3.3
	MAIL=/var/spool/mail/work
	PATH=/home/service/source/apache-maven3.3.3/bin:/home/service/source/go/bin:/home/service/source/jdk1.7.0_55/bin:/usr/local/bin:/bin:/usr/bin:/usr/local/sbin:/usr/sbin:/sbin:/home/work/bin
	PWD=/home/work
	JAVA_HOME=/home/service/source/jdk1.7.0_55
	LANG=en_US.UTF-8
	HISTCONTROL=ignoredups
	SHLVL=1
	HOME=/home/work
	GOROOT=/home/service/source/go
	LOGNAME=work
	CVS_RSH=ssh
	CLASSPATH=.:/home/service/source/jdk1.7.0_55/lib/dt.jar:/home/service/source/jdk1.7.0_55/lib/tools.jar
	SSH_CONNECTION=10.8.1.13 38059 10.8.250.194 22
	LESSOPEN=|/usr/bin/lesspipe.sh %s
	RELAY_USER=ningli
	G_BROKEN_FILENAMES=1
	_=/bin/env 

##Shell脚本
文件的第一行必须是“#!/bin/bash”，“#！”符号成为“Sha-bang”符号，是Shell脚本的其实符号，“#！”符号是指定一个文件类型的特殊标记，它告诉Linux系统这个文件的执行需要指定一个解析器。“#！”符号之后是一个路径名，这个路径名指明了解释器在系统中的位置，对于一般的Shell脚本而言，解释器是bash，也可以是sh，即：#/bin/bash 
或 #!/bin/sh

	[work(0)@yz-lab-675 12:21:52 ~]# cat first.sh
	#!/bin/bash  #-----符号#!用来告诉系统它后面的参数是用来执行该文件的程序
	hostname
	date
	who
	[work(0)@yz-lab-675 12:21:56 ~]# sh first.sh 
	yz-lab-675.meilishuo.com
	Fri Mar 18 12:22:00 CST 2016
	work     pts/0        2016-03-18 10:18 (10.8.1.13)
	##使用中的小技巧
$# 是传给脚本的参数个数
$0 是脚本本身的名字
$1 是传递给该shell脚本的第一个参数
$2 是传递给该shell脚本的第二个参数
$@ 是传给脚本的所有参数的列表
$* 是以一个单字符串显示所有向脚本传递的参数，与位置变量不同，参数可超过9个
$$ 是脚本运行的当前进程ID号
$? 是显示最后命令的退出状态，0表示没有错误，其他表示有错误


----------

 - “双击”tab建可以自动补全命令
 - man命令查看某个命令具体是做什么的，如果没有man命令请root权限登录后安装再查看
 
		  [root(0)@yz-lab-675 18:30:04 ~]# yum install man
 - 分号（;）可以用来隔开同一行内的多条命令，Shell会依次执行用分号隔开的多条命令
 
		 [work(0)@yz-lab-675 14:25:46 ~]# ls -l /etc/sh*;date;who
 - 正则表达式中“^$”来代表空行
 - “^”符合表示匹配行首，但是，“^”符号放到“[]”符号中就不再表示匹配行首了，而是表示取反符号
 - awk 支持“？”和“+”两个扩展元字符，而grep和sed并不支持
 - awk默认设置的域分隔符都是空格键，Tab键被看做是连续的空格键来处理，我们可以使用awk的-F选项改变分隔符，比如将分隔符改为Tab键，例如： awk  -F  "\t"  '{print $2}'  test.txt
 - :%d 格式化复制粘贴
 - shift + v + 上下键 选中代码； 按 = 号格式化数据
 - 手动显示行号：在vim命令行模式下输入  ：set nu; 取消显示行号：在vim命令行模式下输入：  set nonu
 - linux下查找目录下的所有文件中是否含有某个字符串： find .|xargs grep -ri "IBM"
 - grep -r : -r选项表示递归搜索，不仅搜索当前目录，而且搜索子目录。
# grep 查找字符串
grep [OPTION]... PATTERN [FILE]...

## 1、-w（--word-regexp）、-l（--file-with-matches）、-n（--line-number）、-v（--invert-match）
```bash
$ grep -r "yongfeimiao" .
./test1/1.txt:yongfeimiao
./a.txt:yongfeimiao
./a.txt:yongfeimiao1
./a.txt:yongfeimiao3

$ grep -rw "yongfeimiao" .
./test1/1.txt:yongfeimiao
./a.txt:yongfeimiao

$ grep -rl "yongfeimiao" .
./test1/1.txt
./a.txt

$ grep -rn "yongfeimiao" .
./test1/1.txt:1:yongfeimiao
./a.txt:1:yongfeimiao
./a.txt:2:yongfeimiao1
./a.txt:3:yongfeimiao3

$ grep -c "yongfeimiao" a.txt
3

$ grep -wv "yongfeimiao" a.txt
yongfeimiao1
yongfeimiao3
```

## 2、-B（--before-content=NUM）、-A（--after-content=NUM）、-C（--content=NUM）
```bash
$ grep '2015-11-17 15:45:59' /home/work/webdata/logs/db_log
[2015-11-17 15:45:59]	Array

#当前行前面NUM行，NUM数不含当前行
$ grep '2015-11-17 15:45:59' -B 3 /home/work/webdata/logs/db_log
)


[2015-11-17 15:45:59]	Array

#当前行后面NUM行，NUM数不含当前行
$ grep '2015-11-17 15:45:59' -A 3 /home/work/webdata/logs/db_log
[2015-11-17 15:45:59]	Array
(
    [message] => Passed variable is not an array or object, using empty array instead
    [url] => http://goods.mlservice.yongfeimiao.rdlab.meilishuo.com/goods/goods_sale_num_add?goods_id=77432291&sale_num=10

#当前行前后各NUM行，NUM数不含当前行
$ grep '2015-11-17 15:45:59' -C 3 /home/work/webdata/logs/db_log
)


[2015-11-17 15:45:59]	Array
(
    [message] => Passed variable is not an array or object, using empty array instead
    [url] => http://goods.mlservice.yongfeimiao.rdlab.meilishuo.com/goods/goods_sale_num_add?goods_id=77432291&sale_num=10

$ grep '2015-11-17 15:45:59' -3 /home/work/webdata/logs/db_log
)


[2015-11-17 15:45:59]	Array
(
    [message] => Passed variable is not an array or object, using empty array instead
    [url] => http://goods.mlservice.yongfeimiao.rdlab.meilishuo.com/goods/goods_sale_num_add?goods_id=77432291&sale_num=10
```

## 3、-P（--perl-regexp）、-o（--only-matching）
```bash
$ tail -2 phplibmqproxy.2015111814
[2015-11-18 14:50:52]	[publish_message]	INFO	{"endpoint":"http:\/\/127.0.0.1:9090\/produce?format=json","topic":"goods_other_topic","partition_key":"yongfeimiao.rdlab117362","message":{"_log_id":"yongfeimiao.rdlab117362","_topic":"goods_other_topic","_msg_key":"update_sph_goods","_partition_key":"yongfeimiao.rdlab117362","_consumer":"Mlservice\\Package\\Consumer\\Update_sph_goods\\SyncUpdateSphGoods","_retry_times":0,"_flag":"kafka","_data":"{\"type\":\"updateGoodsInfoSph\",\"goods_id\":[\"323\"],\"shop_id\":[],\"current_data\":\"a\"}"},"partition":5,"offset":0,"exception":"","timecost":"8.58","current_retry_time":0}
[2015-11-18 14:50:58]	[publish_message]	INFO	{"endpoint":"http:\/\/127.0.0.1:9090\/produce?format=json","topic":"goods_other_topic","partition_key":"yongfeimiao.rdlab117364","message":{"_log_id":"yongfeimiao.rdlab117364","_topic":"goods_other_topic","_msg_key":"update_sph_goods","_partition_key":"yongfeimiao.rdlab117364","_consumer":"Mlservice\\Package\\Consumer\\Update_sph_goods\\SyncUpdateSphGoods","_retry_times":0,"_flag":"kafka","_data":"{\"type\":\"updateGoodsInfoSph\",\"goods_id\":[\"323\"],\"shop_id\":[],\"current_data\":\"a\"}"},"partition":3,"offset":0,"exception":"","timecost":"5.73","current_retry_time":0}

$ tail -2 phplibmqproxy.2015111814 | grep -Po '"timecost":"[\d|\.]+'
"timecost":"8.58
"timecost":"5.73
```
# sort 文本文件行排序

sort [OPTION]... [FILE]...

## 1、常用用法

### 1.1、sort file.txt
key点：不带任何参数，sort默认按照`字母`顺序`正`排序
```bash
$ cat file.txt
abc
def
ghi
abc
100
1
10
2
20
$ sort file.txt
1
10
100
2
20
abc
abc
def
ghi
```

### 1.2、sort -n file.txt
key点：-n（--numeric-sort），sort按照`数值`顺序`正`排序
```bash
$ cat file.txt
abc
def
ghi
abc
100
1
10
2
20
$ sort -n file.txt
abc
abc
def
ghi
1
2
10
20
100
```

### 1.3、sort -u file.txt
key点：-u（--unique），sort移除所有重复的行
```bash
$ cat file.txt
abc
def
ghi
abc
100
1
10
2
20
$ sort -n file.txt
1
10
100
2
20
abc
def
ghi
```

### 1.4、sort -r file.txt
key点：-r（--reverse），sort按照`字母`顺序`倒`排序
```bash
$ cat file.txt
abc
def
ghi
abc
100
1
10
2
20
$ sort -r file.txt
ghi
def
abc
abc
20
2
100
10
1
```

## 2、用法进阶

### 2.1、sort -t ' ' -k 3n,3 -k 4.3,4.3nr file.txt
```bash
$ cat file.txt
sina zy 72 175
baidu yf 52 174
baidu xl 52 176
mls ln 98 190
mls ht 72 180
$ sort -t ' ' -k 3n,3 -k 4r,4 file.txt
baidu xl 52 176
baidu yf 52 174
mls ht 72 180
sina zy 72 175
mls ln 98 190
$ sort -t ' ' -k 3n,3 -k 4.3,4.3nr file.txt
baidu xl 52 176
baidu yf 52 174
sina zy 72 175
mls ht 72 180
mls ln 98 190
```
key点：
* （1）-t（--field-separator=SEP）指定列分隔符
* （2）-k [ FStart [ .CStart ] ] [ Modifier ] [ , [ FEnd [ .CEnd ] ][ Modifier ] ] 从哪一列开始比较（或者哪一列的哪个字符），到哪一列结束（或者哪一列的哪个字符）（默认到最后一列最后一个字符）

```comment
FStart   从哪一列(域Field)开始（默认从第一列）
CStart   从当前列(域Field)的哪个字符(character)开始（默认从第一个）
Modifier 修饰，如n（numeric-sort）、r（reverse）等

FEnd     到哪一列(域Field)结束（默认到最后一列）
CEnd     到当前列(域Field)的哪个字符(character)结束（默认到最后一个）
Modifier 修饰，如n（numeric-sort）、r（reverse）等
```

### 2.2、sort a.txt b.txt
```bash
$ cat a.txt
a
b
$ cat b.txt
a
d
$ sort a.txt b.txt
a
a
b
d
```


### 2.3、sort 和awk的联合用法 
**根据姓名对文件块进行排序**
```bash
[work(0)@yz-lab-675 16:39:59 ~]# cat a.txt 
J Luo #每个文件块由姓名、学校和地址组成
BeiJing University
BeiJing,China

Y Zhang
Vitory University
Melbourne, Australia

D Hou
BeiJing University
BeiJing,China

B Liu
Shanghai Jiaotong University
Shanghai,China
[work(0)@yz-lab-675 16:40:04 ~]# cat a.txt | awk -v RS="" '{gsub("\n","@");print}'| sort | awk -v ORS="\n\n" '{gsub("@","\n");print}' #将每个文件块合并到一行，对每行的记录排序，将排序后的行分块打印
B Liu
Shanghai Jiaotong University
Shanghai,China

D Hou
BeiJing University
BeiJing,China

J Luo
BeiJing University
BeiJing,China

Y Zhang
Vitory University
Melbourne, Australia
```
# uniq [相邻行]文本去重
uniq [OPTION]... [INPUT [OUTPUT]]

## 1、-c（--count）、-d（--repeated）、-D（--all-repeated）、-u（--unique）、-f（--skip-field=N）
```bash
$ cat uniq.txt
a
b
a
a
c
d
f

$ uniq -c uniq.txt
      1 a
      1 b
      2 a
      1 c
      1 d
      1 f

#只显示重复的，并只显示一次
$ uniq -d uniq.txt
a

#只显示重复的，但是都显示
$ uniq -D uniq.txt
a
a

#只显示非重复的
$ uniq -u uniq.txt
a
b
c
d
f

$ cat uniq2.txt
a 1
a 2
b 1
c 1
d 2
#相邻行去重
$ uniq uniq2.txt
a 1
a 2
b 1
c 1
d 2
#忽略第一列，即从第二列开始去重
$ uniq -f 1 uniq2.txt
a 1
a 2
b 1
d 2
```

## 2、和sort联合使用，才能实现当前所有数据行去重
```bash
$ cat uniq.txt
a
b
a
a
c
d
f
#相邻行去重
$ uniq -c uniq.txt
      1 a
      1 b
      2 a
      1 c
      1 d
      1 f
#所有数据行去重
$ sort uniq.txt | uniq -c
      3 a
      1 b
      1 c
      1 d
      1 f
```
# awk 文本处理

### 1、核心思想：
 * 程序语言；包含变量、算术、字符串操作符、循环、条件等
 * 数据驱动；描述你要处理的数据以及在在你找到他之后要做什么
 * 结构化的数据；所有操作的关键，没有结构化的数据，就不能找到pattern，就无从action

### 2、语法：
```bash
pattern {action}
pattern {action}
...
```

### 3、运行方式：
* （1）awk [options] [--] program-text    file ...
* （2）awk [options] -f program-file [--] file ...

### 4、常用的选项：
* -F '[' ：指定用于输入数据的列分隔符[
* -v var=value ：在awk程序执行之前（BEGIN区块）指定一个值value给变量var
* -f program-file ：指定一个awk程序文件，代替直接指定awk指令
* -- ：根据POSIX参数解析约定，此选项表示命令行选项的结束。

### 5、条件语句
if (conditon1) body1 [else if (condition2) body2] [else body3]
```bash
$ cat file.txt
miaoyongfei-57
oulinan-98
zhaoxianlie-60

$ awk 'BEGIN {FS="-"} {if ($2 < 58) {print "weight < 58KG : "$1} else if ($2 > 90) {print "weight > 90KG : "$1} else {print "weight >= 58kG && weight <= 90KG : "$1}}' file.txt
weight < 58KG : miaoyongfei
weight > 90KG : oulinan
weight >= 58kG && weight <= 90KG : zhaoxianlie
```

### 6、变量和操作符
```bash
$ cat file.txt
miaoyongfei-57
oulinan-98
zhaoxianlie-60

$ awk 'BEGIN {n=0} {n++} END {print n}' file.txt
3
```
|运算|符号|表达式|
|-|
|加法|`+`|n=n+3|
|减法|`-`|n=n-3|
|乘法|`*`|n=n*3|
|除法|`/`|n=n/3|
|取余|`%`|n=n%3|
|幂乘|`^`或者`**`|n=n^3 或者 n=n**3|
|自加一|`++`|n++|
|自减一|`--`|n--|
|赋值||n+=3,n-=3,n*=3,n/=3等|

### 7、特殊变量（内置变量）
| 变量名称 | 语义说明 | 额外说明 | case |
|-|
|    $0    | 当前行数据                  | awk会自动将此变量的值设置为当前行数据 | awk '{print $0}' file.txt |
|  $1 ~ $n | 当前记录的第n个字段，字段之间FS分隔 | awk会自动将对应的字段赋值给对应变量$n | awk '{print $1,$2}' file.txt|
|-|
|    FS    | （Field Separator）列分隔符 | 不一定是单个字符，可以是正则表达式 | awk 'BEGIN {FS="\t+"} {print $1}' file.txt |
|    RS    | （Row Separator）输入的数据的记录(行)分隔符 | 默认：换行符| awk 'BEGIN {FS=" ";RS="\t"} {print $0}' file.txt |
|-|
|    OFS   | （Output Field Separator）输出的列分隔符 | 默认：换行符| awk 'BEGIN {FS="\t+";OFS=" [ "} {print $1,$2}' file.txt |
|    ORS   | （Output Row Separator）输出的行分隔符| 默认：一个空格 | awk 'BEGIN {RS="\t+";ORS="\r\n"} {print $0}' file.txt |
|-|
|    NF    | （Number of Field）列的数量 | awk会自动将此变量的值设置为当前记录中列的个数 | awk 'NF == 3 {print $0}' file.txt |
|    NR    | （Number of Row）行的数量   | awk会自动将此变量的值设置为当前记录的行数，从1开始 | awk '{if (NR > 2) {print NR".\t"$0}}' file.txt |
|-|
|   FNR    | （Front Number of Row）当前的行号 | awk会自动将此变量的值设置为当前记录的行号，从1开始 | awk '{print FNR"\t"$0}' file.txt |
|-|
| FILENAME | 当前文件的名字 | awk会自动将当前文件的名字赋值给FILENANE | awk '{print FILENAME}' file.txt |
|FIELDWIDTHS| 输入字段宽度的空白分隔字符串 | 默认是空格 | $ cat file.txt<br>20151126<br>$ awk 'BEGIN {FIELDWIDTHS ="4 2 2"} {print $1,$2,$3}' file.txt<br>2015 11 26 |
|   OFMT   | （Output ForMaT） | 默认：%.6g | $ awk 'BEGIN {OFMT="%.3f"} {print 2/3,123.111111111}' file.txt<br>0.667 123.111 |

### 8、循环（while、for）
（1）while语法
```bash
while (condition)
    body
```
case
```bash
$ cat file.txt
beijing shanghai shengzhen
hangzhou tianjin nanjing
quzhou jinhua zunyi

$ awk '{
    i=2;
    while (i < 2) {
        print $i
        i++
    }
}' file.txt

```
（2）do while语法
```bash
do {
    body
} while (condition)
```
case
```bash
$ cat file.txt
beijing shanghai shengzhen
hangzhou tianjin nanjing
quzhou jinhua zunyi

$ awk '{
    i=2;
    do {
        print $i
        i++
    } while (i < 2)
}' file.txt
shanghai
tianjin
jinhua
```
（3）for语法
```bash
for (init; condition; increment) 
    body
```
case
```bash
$ cat file.txt
beijing shanghai shengzhen
hangzhou tianjin nanjing
quzhou jinhua zunyi

$ awk '{
    for (i = 1; i < 2; i++) {
        print $1
    }
}' file.txt
beijing
hangzhou
quzhou
```
##语法：alias [别名]=[指令名称]
##参数：若不加任何参数，则列出所有的别名设置
```bash
[root(0)@yz-lab-563 15:05:29 /home/linanou]# alias
alias check='sh /home/service/client/monitor/health_chk.sh'
alias cp='cp -i'
alias fsh='sh /home/linanou/fsh'
alias l.='ls -d .* --color=auto'
alias ll='ls -l --color=auto'
alias ls='ls --color=auto'
alias mv='mv -i'
alias rm='rm -i'
alias text.sh='sh /home/linanou/text.sh'
alias which='alias | /usr/bin/which --tty-only --read-alias --show-dot --show-tilde'
```
####注：alias的效力仅限于此次登录，若想之久话可以设置到~/.bashrc
##例子
```bash
vim ~/.bashrc

alias fsh='sh /home/linanou/fsh'
alias php='/home/service/php/bin/php'
```
#case选择
##格式
```bash
case 变量名 in
	模式1)
		命令序列1
	;;
	模式2)
		命令序列2
	;;
		*)
		默认执行的命令序列
esac
```
#Bash shell的四种运算方式
# Bash shell的四种运算方式

## 1、使用 expr 外部程式

| 运算 | 符号 | 表达式 | 额外说明 |
|-|
| 加法 | `+` | r=\`expr 4 + 5\`   |  |
| 减法 | `-` | r=\`expr 4 - 5\`   |  |
| 乘法 | `\*`| r=\`expr 4 \\* 5\` | 不是`*` |
| 除法 | `/` | r=\`expr 4 / 5\`   |  |
| 取余 | `%` | r=\`expr 4 % 5\`   |  |
| 自加1| `+` | r=\`expr $r + 1\`  | 里面是只能是$r |
| 自减1| `-` | r=\`expr $r - 1\`  | 里面是只能是$r,别忘了先赋值 r=1;r=\`expr $r - 1\` |
| 乘幂 | `**`|                    | 没有此语法   |



注意事项：
* （1）<font color='red'>'='两边不能有空格</font>
* （2）<font color='red'>'4' '+' '5' 这三者之间要有空白</font>
* （3）<font color='red'>表达式包含在\`\`中（键盘上与波浪线在一起的符号，数字1左边）</font>

## 2、使用 $(( ))

| 运算 | 符号 | 表达式 | 额外说明 |
|-|
| 加法 | `+` | r=$(( 4 + 5 ))  |  |
| 减法 | `-` | r=$(( 4 - 5 ))  |  |
| 乘法 | `*` | r=$(( 4 * 5 ))  |  |
| 除法 | `/` | r=$(( 4 / 5 ))  |  |
| 取余 | `%` | r=$(( 4 % 5 ))  |  |
| 自加1| `+` | r=$(( r + 1 )) | 里面是$r或者r |
| 自减1| `-` | r=$(( r - 1 )) | 里面是$r或者r |
| 乘幂 | `**`| r=$(( 4 ** 5 )) | 支持 |

注意事项：
* （1）<font color='red'>'='两边不能有空格</font>
* （2）<font color='red'>$(( 4 + 5 )) 之间的空格都可以没有，这么写只是为了统一及一目了然</font>

## 3、使用 $[ ]

| 运算 | 符号 | 表达式 | 额外说明 |
|-|
| 加法 | `+` | r=$[ 4 + 5 ]  |  |
| 减法 | `-` | r=$[ 4 - 5 ]  |  |
| 乘法 | `*` | r=$[ 4 * 5 ]  |  |
| 除法 | `/` | r=$[ 4 / 5 ]  |  |
| 取余 | `%` | r=$[ 4 % 5 ]  |  |
| 自加1| `+` | r=$[ r + 1 ] | 里面是$r或者r |
| 自减1| `-` | r=$[ r - 1 ] | 里面是$r或者r |
| 乘幂 | `**`| r=$[ 4 ** 5 ] | 支持 |

注意事项：
* （1）<font color='red'>'='两边不能有空格</font>
* （2）<font color='red'>$[ 4 + 5 ] 之间的空格都可以没有，这么写只是为了统一及一目了然</font>

## 4、使用let

| 运算 | 符号 | 表达式 | 额外说明 |
|-|
| 加法 | `+` | let r=4+5 |  |
| 减法 | `-` | let r=4-5 |  |
| 乘法 | `*` | let r=4*5 |  |
| 除法 | `/` | let r=4/5 |  |
| 取余 | `%` | let r=4%5 |  |
| 自加1| `+` | let r=r+1 | 里面是$r或者r |
| 自减1| `-` | let r=r-1 | 里面是$r或者r |
| 乘幂 | `**`| let r=4**5| 支持 |

注意事项：
* （1）<font color='red'>'='两边不能有空格</font>
* （2）<font color='red'>r=4+5 之间的不能有空格</font>

## 5、建议
* 虽然Bash shell 有四种算术运算方法，但并不是每一种都是跨平台的，建议在expr支持的情况下，尽量使用expr。
#for
## 1、数字段形式
```bash
$ for i in {1..5}
do
    echo $i
done

1
2
3
4
5
```

## 2、详细列出（字符且项数不多）
```bash
for i in 1 2 3 4 5
do
    echo $i
done

1
2
3
4
5
```

## 3、对命令输出进行循环
```bash
$cat iplist.txt
	yq-virusnx-01
	syq-virusnx-02
	syq-virusnx-03
	syq-virusnx-04
	yz-virusnx-01
	yz-virusnx-02
$for ip in `cat iplist.txt`
	do 
		echo "——————"${ip}"————start———————————————"
		ssh rd@${ip} "awk '{if (\$8==\"[499]\") {print \$0}}' /home/service/nginx/logs/goods.mlservice.access.2015121015.log"
		echo "——————"${ip}"—————end————————————————"
	done

——————yz-virusnx-01————start———————————————
——————yz-virusnx-01—————end————————————————
——————yz-virusnx-02————start———————————————
[10.8.7.33] [-] [10/Dec/2015:15:24:35 +0800] [POST /goods/goods_info HTTP/1.0] [499] [0] [virus.meilishuo.com/freight/get_freight] [-] [10.8.7.33] [1.000] [10.8.12.94:9999] [-] [goods_id=277963991&fields%5B0%5D=old_repertory] [uid:0;ip:0.0.0.0;v:0;master:0]
——————yz-virusnx-02—————end————————————————


$ for filename in `find . -type f -name "*.txt"`
do
    echo $filename;
done

./f.txt
./a.txt
./file.txt
./b.txt
```

## 4、for(())语法循环（很像C语法，但注意这里是前后双括号）
```bash
$ for((i=1;i<10;i++))
do
    if((i%3==0))
    then
        echo $i
    else
        echo 'null'
    fi
done

null
null
3
null
null
6
null
null
9
```

## 5、seq形式
```bash
$ for i in `seq 1 2 10`
do 
    if((i%3==0))
    then
        echo $i
    fi
done

3
9
```
#while
# 语法

## 1、while []，此处注意'while [ $min -le $max ]'之间的每处空格
```bash
$ min=1;max=10;\
while [ $min -le $max ]
    do
        echo $min;
        min=`expr $min + 1`;
    done

1
2
3
4
5
6
7
8
9
10
```

## 2、$(())
```bash
$ i=1;\
while(($i<10))
    do
        echo $i;
        i=$((i+1));
    done

1
2
3
4
5
6
7
8
9
10
```

## 3、while与read配合读取文件
key点：控制进程数量
```bash
$ cat a.txt
3
$ cat a.txt | while read line
    do 
        while [ 1 -eq 1 ]
            do
                psNum=`ps -ef | grep "test.php" | grep -v "grep" | wc -l`;
                if [ $psNum -lt $line ]
                then
                    echo $psNum
                    echo '+1'
                    nohup /home/service/php/bin/php test.php &
                elif [ $psNum -gt $line ]
                then
                    echo $psNum
                    echo '-1'
                    ps -ef | grep "test.php" | grep -v "grep" | tail -1 | awk '{print $2}' | xargs kill -9
                else
                    echo $line
                    break
                fi
            done
    done

0
+1
1
+1
2
+1
3
$ ps -ef | grep "test.php" | grep -v "grep"
work     17300     1  0 14:25 pts/0    00:00:00 /home/service/php/bin/php test.php
work     17315     1  0 14:29 pts/0    00:00:00 /home/service/php/bin/php test.php
work     17321     1  0 14:29 pts/0    00:00:00 /home/service/php/bin/php test.php /home/service/php/bin/php test.php
$ echo 1 > a.txt
$ cat a.txt
1
$ cat a.txt | while read line
    do 
        while [ 1 -eq 1 ]
            do
                psNum=`ps -ef | grep "test.php" | grep -v "grep" | wc -l`;
                if [ $psNum -lt $line ]
                then
                    echo $psNum
                    echo '+1'
                    nohup /home/service/php/bin/php test.php &
                elif [ $psNum -gt $line ]
                then
                    echo $psNum
                    echo '-1'
                    ps -ef | grep "test.php" | grep -v "grep" | tail -1 | awk '{print $2}' | xargs kill -9
                else
                    echo $line
                    break
                fi
            done
    done

3
-1
2
-1
1
$ ps -ef | grep "test.php" | grep -v "grep"
work     17300     1  0 14:25 pts/0    00:00:00 /home/service/php/bin/php test.php
```
#遍历执行部分shell工具-fsh
```bash
#!/bin/bash
user=$(whoami)

blue="\033[34m"
green="\033[32m"
red="\033[31m"
color_end="\033[0m"

function work(){
	if [ $1"x" == 'x' ]
	then
        help
	fi
	
	index=0
	content=''
	for i in $@
	do
	    if [[ $index == 0 ]]
	    then
            cluster_name=$i
            #echo "cluster_name:"$cluster_name
	    else
            content=$content" "$i
	    fi
	    index=`expr $index + 1`
	done
	
	#echo "content:"$content
	
	for ip in `cat ~/iplist/$cluster_name`
	do
        echo -e "$red-----------------------------$ip-----------------------------------$color_end"
        echo -e "$green"
        ssh $user@$ip "$content"
        echo -e "$color_end"
	done
}

function list(){
	if [ $1"x" != 'x' ]
	then
        iplist_filename=~/iplist/$1
        if [ ! -f "$iplist_filename" ]
        then
	        echo -e "$red当前集群没有配置，如果有必要请先增加配置$color_end"
	        exit
        else
	        for ip in `cat $iplist_filename`
	        do
	            echo -e "$green$ip$color_end"
	        done
        fi
	else
	        for cl in `ls ~/iplist`
	        do
                echo -e "$blue$cl$color_end"
	        done
	fi
}

function help(){
	cat <<EOF
        usage:
        (1) fsh --help                          使用说明（帮助手册），或者fsh
        (2) fsh --list                          知道当前支持的集群list
        (3) fsh --list 集群list名称              当前集群的ip list，如：fsh --list dfz-goods-php
        (4) fsh 集群list名称 "具体操作"           具体要执行的操作(注意 "")，如：fsh dfz-goods-php "ps -ef | grep 'bin/nut' | grep -v 'grep' | grep -v 'bash'"
EOF
	exit
}


while true;
do
	case $1 in
        --list)
	        list $2
	        break
	        ;;
        --help)
	        help
	        break
	        ;;
        *)
            work $@
            break
            ;;
	esac
done
```
```bash
+ '[' dfz-goods-phpx == x ']'
+ index=0
+ content=
+ for i in '$@'
+ [[ 0 == 0 ]]
+ cluster_name=dfz-goods-php
++ expr 0 + 1
+ index=1
+ for i in '$@'
+ [[ 1 == 0 ]]
+ content=' ps'
++ expr 1 + 1
+ index=2
+ for i in '$@'
+ [[ 2 == 0 ]]
+ content=' ps -ef'
++ expr 2 + 1
+ index=3
+ for i in '$@'
+ [[ 3 == 0 ]]
+ content=' ps -ef |'
++ expr 3 + 1
+ index=4
+ for i in '$@'
+ [[ 4 == 0 ]]
+ content=' ps -ef | grep'
++ expr 4 + 1
+ index=5
+ for i in '$@'
+ [[ 5 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'''
++ expr 5 + 1
+ index=6
+ for i in '$@'
+ [[ 6 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' |'
++ expr 6 + 1
+ index=7
+ for i in '$@'
+ [[ 7 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' | grep'
++ expr 7 + 1
+ index=8
+ for i in '$@'
+ [[ 8 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' | grep -v'
++ expr 8 + 1
+ index=9
+ for i in '$@'
+ [[ 9 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' | grep -v '\''grep'\'''
++ expr 9 + 1
+ index=10
+ for i in '$@'
+ [[ 10 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' | grep -v '\''grep'\'' |'
++ expr 10 + 1
+ index=11
+ for i in '$@'
+ [[ 11 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' | grep -v '\''grep'\'' | grep'
++ expr 11 + 1
+ index=12
+ for i in '$@'
+ [[ 12 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' | grep -v '\''grep'\'' | grep -v'
++ expr 12 + 1
+ index=13
+ for i in '$@'
+ [[ 13 == 0 ]]
+ content=' ps -ef | grep '\''bin/nut'\'' | grep -v '\''grep'\'' | grep -v '\''bash'\'''
++ expr 13 + 1
+ index=14
++ cat /home/rd/iplist/dfz-goods-php
+ for ip in '`cat ~/iplist/$cluster_name`'
+ echo -e '\033[31m-----------------------------dfz-goods-php-01.meilishuo.com-----------------------------------\033[0m'
-----------------------------dfz-goods-php-01.meilishuo.com-----------------------------------
+ echo -e '\033[32m'

+ ssh rd@dfz-goods-php-01.meilishuo.com ' ps -ef | grep '\''bin/nut'\'' | grep -v '\''grep'\'' | grep -v '\''bash'\'''
work     43997 43956  0 Dec16 ?        00:08:31 /home/service/nutcracker/bin/nutcracker -s 4204 -c /home/work/conf/nut/nutcracker4.yml -p /home/work/nutcracker/nutcracker4.pid -o /home/work/nutcracker/logs/nutcracker4.log -v 4
work     44058 44014  0 Dec16 ?        00:08:26 /home/service/nutcracker/bin/nutcracker -s 4203 -c /home/work/conf/nut/nutcracker3.yml -p /home/work/nutcracker/nutcracker3.pid -o /home/work/nutcracker/logs/nutcracker3.log -v 4
work     44093 43954  0 Dec16 ?        00:08:27 /home/service/nutcracker/bin/nutcracker -s 4202 -c /home/work/conf/nut/nutcracker2.yml -p /home/work/nutcracker/nutcracker2.pid -o /home/work/nutcracker/logs/nutcracker2.log -v 4
work     44431 44330  0 Dec16 ?        00:08:53 /home/service/nutcracker/bin/nutcracker -s 4201 -c /home/work/conf/nut/nutcracker1.yml -p /home/work/nutcracker/nutcracker1.pid -o /home/work/nutcracker/logs/nutcracker1.log -v 4
+ echo -e '\033[0m'

+ for ip in '`cat ~/iplist/$cluster_name`'
+ echo -e '\033[31m-----------------------------dfz-goods-php-02.meilishuo.com-----------------------------------\033[0m'
-----------------------------dfz-goods-php-02.meilishuo.com-----------------------------------
+ echo -e '\033[32m'
```








