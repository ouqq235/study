#介绍
        Sphinx是一个基于SQL的全文检索引擎，支持多种数据源，可以结合MySQL,PostgreSQL做全文搜索，它可以提供比数据库本身更专业的搜索功能，使得应用程序更容易实现专业化的全文检索。Sphinx特别为一些脚本语言设计搜索API接口，如PHP,Python,Perl,Ruby等，同时为MySQL也设计了一个存储引擎插件。
        
        Sphinx单一索引最大可包含1亿条记录，在1千万条记录情况下的查询速度为0.x秒（毫秒级）。Sphinx创建索引的速度为：创建100万条记录的索引只需 3～4分钟，创建1000万条记录的索引可以在50分钟内完成，而只包含最新10万条记录的增量索引，重建一次只需几十秒。
        
#Sphinx的主要特性包括：
    支持分布式索引
    高速索引 (在新款CPU上,近10 MB/秒);
    高速搜索 (2-4G的文本量中平均查询速度不到0.1秒);
    高可用性 (单CPU上最大可支持100 GB的文本,100M文档);
    提供良好的相关性排名
    支持分布式搜索;
    提供文档摘要生成;
    提供从MySQL内部的插件式存储引擎上搜索
    支持布尔,短语, 和近义词查询;
    支持每个文档多个全文检索域(默认最大32个);
    支持每个文档多属性;
    支持断词;
    支持单字节编码与UTF-8编码。
root 命令下执行 sh install.sh 安装
安装文件svn地址 http://svn.meilishuo.com/repos/shop-backend/trunk/sphinx/script/install.sh
```bash
#######首版安装  root权限安装
###创建索引与日志目录
echo "创建索引与日志目录......"
mkdir -p /home/work/cor/data
mkdir -p /home/work/cor/log
echo "创建索引与日志目录完......"
cd /tmp
echo "下载......"
wget http://www.coreseek.cn/uploads/csft/4.0/coreseek-4.0.1-beta.tar.gz
echo "下载完......"
echo "开始安装......"
tar -zxvf coreseek-4.0.1-beta.tar.gz
cd  coreseek-4.0.1-beta
cd mmseg-3.2.14
./bootstrap
./configure --prefix=/home/service/mmseg
make && make install
cd ..
cd csft-4.0.1
yum install mysql-devel
sh buildconf.sh
./configure --prefix=/home/service/coreseek  --without-unixodbc --with-mmseg --with-mmseg-includes=/home/service/mmseg/include/mmseg/ --with-mmseg-libs=/home/service/mmseg/lib/ --with-mysql  --enable-id64
make && make install
rm -rf /tmp/coreseek-4.0.1-beta
chown -R work:work /home/service/
chown -R work:work /tmp/
chown -R work:work /home/work/cor/
chmod -R 755 /home/service/
chmod -R 755 /tmp/
chmod -R 755 /home/work/cor/
echo "安装完...."
```
#sphinx支持多种索引源 这里拿MYSQL举例
```
## 数据源src1
source src1
{
	## 说明数据源的类型。数据源的类型可以是：mysql，pgsql，mssql，xmlpipe，odbc，python
	## 有人会奇怪，python是一种语言怎么可以成为数据源呢？
	## python作为一种语言，可以操作任意其他的数据来源来获取数据，更多数据请看：（http://www.coreseek.cn/products-install/python/）
	type			= mysql
	
	## 下面是sql数据库特有的端口，用户名，密码，数据库名等。
	sql_host		= localhost
	sql_user		= test
	sql_pass		=
	sql_db			= test
	sql_port		= 3306

	## 如果是使用unix sock连接可以使用这个。
	# sql_sock		= /tmp/mysql.sock

	## indexer和mysql之间的交互，需要考虑到效率和安全性。
	## 比如考虑到效率，他们两者之间的交互需要使用压缩协议；考虑到安全，他们两者之间的传输需要使用ssl
	## 那么这个参数就代表这个意思，0/32/2048/32768  无/使用压缩协议/握手后切换到ssl/Mysql 4.1版本身份认证。
	# mysql_connect_flags	= 32

	## 当mysql_connect_flags设置为2048（ssl）的时候，下面几个就代表ssl连接所需要使用的几个参数。
	# mysql_ssl_cert		= /etc/ssl/client-cert.pem
	# mysql_ssl_key		= /etc/ssl/client-key.pem
	# mysql_ssl_ca		= /etc/ssl/cacert.pem

	## mssql特有，是否使用windows登陆
	# mssql_winauth		= 1

	## mssql特有，是使用unicode还是单字节数据。
	# mssql_unicode		= 1 # request Unicode data from server

	## odbc的dsn串
	# odbc_dsn		= DBQ=C:\data;DefaultDir=C:\data;Driver={Microsoft Text Driver (*.txt; *.csv)};
	
	## sql某一列的缓冲大小，一般是针对字符串来说的。
	## 为什么要有这么一种缓冲呢？
	## 有的字符串，虽然长度很长，但是实际上并没有使用那么长的字符，所以在Sphinx并不会收录所有的字符，而是给每个属性一个缓存作为长度限制。
	## 默认情况下非字符类型的属性是1KB，字符类型的属性是1MB。
	## 而如果想要配置这个buffer的话，就可以在这里进行配置了。
	# sql_column_buffers	= content=12M, comments=1M

	## indexer的sql执行前需要执行的操作。
	# sql_query_pre		= SET NAMES utf8
	# sql_query_pre		= SET SESSION query_cache_type=OFF

	## indexer的sql执行语句
	sql_query		= \
		SELECT id, group_id, UNIX_TIMESTAMP(date_added) AS date_added, title, content \
		FROM documents

	## 有的时候有多个表，我们想要查询的字段在其他表中。这个时候就需要对sql_query进行join操作。
	## 而这个join操作可能非常慢，导致建立索引的时候特别慢，那么这个时候，就可以考虑在sphinx端进行join操作了。
	## sql_joined_field是增加一个字段，这个字段是从其他表查询中查询出来的。
	## 这里封号后面的查询语句是有要求的，如果是query，则返回id和查询字段，如果是payload-query，则返回id，查询字段和权重。
	## 并且这里的后一个查询需要按照id进行升序排列。
	# sql_joined_field	= tags from query; SELECT docid, CONCAT('tag',tagid) FROM tags ORDER BY docid ASC
	# sql_joined_field	= wtags from payload-query; SELECT docid, tag, tagweight FROM tags ORDER BY docid ASC

	## 外部文件字段，意思就是一个表中，有一个字段存的是外部文件地址，但是实际的字段内容在文件中。比如这个字段叫做content_file_path。
	## 当indexer建立索引的时候，查到这个字段，就读取这个文件地址，然后加载，并进行分词和索引建立等操作。
	# sql_file_field		= content_file_path

	## 当数据源数据太大的时候，一个sql语句查询下来往往很有可能锁表等操作。
	## 那么我么就可以使用多次查询，那么这个多次查询就需要有个范围和步长，sql_query_range和sql_range_step就是做这个使用的。
	## 获取最大和最小的id，然后根据步长来获取数据。比如下面的例子，如果有4500条数据，这个表建立索引的时候就会进行5次sql查询。 
	## 而5次sql查询每次的间隔时间是使用sql_ranged_rhrottle来进行设置的。单位是毫秒。
	# sql_query_range		= SELECT MIN(id),MAX(id) FROM documents
	# sql_range_step		= 1000
	# sql_ranged_throttle	= 0

	## 下面都是些不同属性的数据了
	## 先要了解属性的概念：属性是存在索引中的，它不进行全文索引，但是可以用于过滤和排序。

	## uint无符号整型属性
	sql_attr_uint		= group_id
	
	## bool属性
	# sql_attr_bool		= is_deleted
	
	## 长整型属性
	# sql_attr_bigint		= my_bigint_id
	
	## 时间戳属性，经常被用于做排序
	sql_attr_timestamp	= date_added

	## 字符串排序属性。一般我们按照字符串排序的话，我们会将这个字符串存下来进入到索引中，然后在查询的时候比较索引中得字符大小进行排序。
	## 但是这个时候索引就会很大，于是我们就想到了一个方法，我们在建立索引的时候，先将字符串值从数据库中取出，暂存，排序。
	## 然后给排序后的数组分配一个序号，然后在建立索引的时候，就将这个序号存入到索引中去。这样在查询的时候也就能完成字符串排序的操作。
	## 这，就是这个字段的意义。
	# sql_attr_str2ordinal	= author_name

	## 浮点数属性，经常在查询地理经纬度的时候会用到。
	# sql_attr_float		= lat_radians
	# sql_attr_float		= long_radians

	## 多值属性（MVA）
	## 试想一下，有一个文章系统，每篇文章都有多个标签，这个文章就叫做多值属性。
	## 我要对某个标签进行查询过滤，那么在建立查询的时候就应该把这个标签的值放入到索引中。
	## 这个字段，sql_attr_multi就是用来做这个事情的。
	# sql_attr_multi		= uint tag from query; SELECT docid, tagid FROM tags
	# sql_attr_multi		= uint tag from ranged-query; \
	#	SELECT docid, tagid FROM tags WHERE id>=$start AND id<=$end; \
	#	SELECT MIN(docid), MAX(docid) FROM tags

	## 字符串属性。
	# sql_attr_string		= stitle

	## 文档词汇数记录属性。比如下面就是在索引建立的时候增加一个词汇数的字段
	# sql_attr_str2wordcount	= stitle

	## 字符串字段，可全文搜索，可返回原始文本信息。
	# sql_field_string	= author

	## 文档词汇数记录字段，可全文搜索，可返回原始信息
	# sql_field_str2wordcount	= title

	## 取后查询，在sql_query执行后立即操作。
	## 它和sql_query_post_index的区别就是执行时间不同
	## sql_query_post是在sql_query执行后执行，而sql_query_post_index是在索引建立完成后才执行。
	## 所以如果要记录最后索引执行时间，那么应该在sql_query_post_index中执行。
	# sql_query_post		=

	## 参考sql_query_post的说明。
	# sql_query_post_index	= REPLACE INTO counters ( id, val ) \
	#	VALUES ( 'max_indexed_id', $maxid )

	## 命令行获取信息查询。
	## 什么意思呢？
	## 我们进行索引一般只会返回主键id，而不会返回表中的所有字段。
	## 但是在调试的时候，我们一般需要返回表中的字段，那这个时候，就需要使用sql_query_info。
	## 同时这个字段只在控制台有效，在api中是无效的。
	sql_query_info		= SELECT * FROM documents WHERE id=$id

	## 比如有两个索引，一个索引比较旧，一个索引比较新，那么旧索引中就会有数据是旧的。
	## 当我要对两个索引进行搜索的时候，哪些数据要按照新的索引来进行查询呢。
	## 这个时候就使用到了这个字段了。
	## 这里的例子（http://www.coreseek.cn/docs/coreseek_4.1-sphinx_2.0.1-beta.html#conf-sql-query-killlist）给的非常清晰了。
	# sql_query_killlist	= SELECT id FROM documents WHERE edited>=@last_reindex

	## 下面几个压缩解压的配置都是为了一个目的：让索引重建的时候不要影响数据库的性能表现。
	## SQL数据源解压字段设置
	# unpack_zlib		= zlib_column
	## MySQL数据源解压字段设置
	# unpack_mysqlcompress	= compressed_column
	# unpack_mysqlcompress	= compressed_column_2
	## MySQL数据源解压缓冲区设置
	# unpack_mysqlcompress_maxsize	= 16M


	## xmlpipe的数据源就是一个xml文档
	# type			= xmlpipe

	## 读取数据源的命令
	# xmlpipe_command		= cat /home/yejianfeng/instance/coreseek/var/test.xml

	## 字段
	# xmlpipe_field		= subject
	# xmlpipe_field		= content

	## 属性
	# xmlpipe_attr_timestamp	= published
	# xmlpipe_attr_uint	= author_id

	## UTF-8修复设置
	## 只适用xmlpipe2数据源，数据源中有可能有非utf-8的字符，这个时候解析就有可能出现问题
	## 如果设置了这个字段，非utf-8序列就会全部被替换为空格。
	# xmlpipe_fixup_utf8	= 1
}

## sphinx的source是有继承这么一种属性的，意思就是除了父source之外，这个source还有这个特性
source src1throttled : src1
{
	sql_ranged_throttle	= 100
}

## 索引test1
index test1
{
	## 索引类型，包括有plain，distributed和rt。分别是普通索引/分布式索引/增量索引。默认是plain。
	# type			= plain

	## 索引数据源
	source			= src1
	## 索引文件存放路径
	path			= /home/yejianfeng/instance/coreseek/var/data/test1

	## 文档信息的存储模式，包括有none,extern,inline。默认是extern。
	## docinfo指的就是数据的所有属性（field）构成的一个集合。
	## 首先文档id是存储在一个文件中的（spa）
	## 当使用inline的时候，文档的属性和文件的id都是存放在spa中的，所以进行查询过滤的时候，不需要进行额外操作。
	## 当使用extern的时候，文档的属性是存放在另外一个文件（spd）中的，但是当启动searchd的时候，会把这个文件加载到内存中。
	## extern就意味着每次做查询过滤的时候，除了查找文档id之外，还需要去内存中根据属性进行过滤。
	## 但是即使这样，extern由于文件大小小，效率也不低。所以不是有特殊要求，一般都是使用extern
	docinfo			= extern

	## 缓冲内存锁定。
	## searchd会讲spa和spi预读取到内存中。但是如果这部分内存数据长时间没有访问，则它会被交换到磁盘上。
	## 设置了mlock就不会出现这个问题，这部分数据会一直存放在内存中的。
	mlock			= 0

	## 词形处理器
	## 词形处理是什么意思呢？比如在英语中，dogs是dog的复数，所以dog是dogs的词干，这两个实际上是同一个词。
	## 所以英语的词形处理器会讲dogs当做dog来进行处理。
	morphology		= none

	## 词形处理有的时候会有问题，比如将gps处理成gp，这个设置可以允许根据词的长度来决定是否要使用词形处理器。
	# min_stemming_len	= 1

	## 词形处理后是否还要检索原词？
	# index_exact_words	= 1

	## 停止词，停止词是不被索引的词。
	# stopwords		= /home/yejianfeng/instance/coreseek/var/data/stopwords.txt

	## 自定义词形字典
	# wordforms		= /home/yejianfeng/instance/coreseek/var/data/wordforms.txt

	## 词汇特殊处理。
	## 有的一些特殊词我们希望把它当成另外一个词来处理。比如，c++ => cplusplus来处理。
	# exceptions		= /home/yejianfeng/instance/coreseek/var/data/exceptions.txt

	## 最小索引词长度，小于这个长度的词不会被索引。
	min_word_len		= 1

	## 字符集编码类型，可以为sbcs,utf-8。对于Coreseek，还可以有zh_cn.utf-8,zh_ch.gbk,zh_ch.big5
	charset_type		= sbcs

	## 字符表和大小写转换规则。对于Coreseek，这个字段无效。
	# 'sbcs' default value is
	# charset_table		= 0..9, A..Z->a..z, _, a..z, U+A8->U+B8, U+B8, U+C0..U+DF->U+E0..U+FF, U+E0..U+FF
	#
	# 'utf-8' default value is
	# charset_table		= 0..9, A..Z->a..z, _, a..z, U+410..U+42F->U+430..U+44F, U+430..U+44F

	## 忽略字符表。在忽略字符表中的前后词会被连起来当做一个单独关键词处理。
	# ignore_chars		= U+00AD

	## 是否启用通配符，默认为0，不启用
	# enable_star		= 1

	## min_prefix_len,min_infix_len,prefix_fields,infix_fields都是在enable_star开启的时候才有效果。
	## 最小前缀索引长度
	## 为什么要有这个配置项呢？
	## 首先这个是当启用通配符配置启用的前提下说的，前缀索引使得一个关键词产生了多个索引项，导致索引文件体积和搜索时间增加巨大。
	## 那么我们就有必要限制下前缀索引的前缀长度，比如example，当前缀索引长度设置为5的时候，它只会分解为exampl，example了。
	# min_prefix_len		= 0
	## 最小索引中缀长度。理解同上。
	# min_infix_len		= 0

	## 前缀索引和中缀索引字段列表。并不是所有的字段都需要进行前缀和中缀索引。
	# prefix_fields		= filename
	# infix_fields		= url, domain

	## 词汇展开
	## 是否尽可能展开关键字的精确格式或者型号形式
	# expand_keywords		= 1

	## N-Gram索引的分词技术
	## N-Gram是指不按照词典，而是按照字长来分词，这个主要是针对非英文体系的一些语言来做的（中文、韩文、日文）
	## 对coreseek来说，这两个配置项可以忽略。
	# ngram_len		= 1
	# ngram_chars		= U+3000..U+2FA1F

	## 词组边界符列表和步长
	## 哪些字符被看做分隔不同词组的边界。
	# phrase_boundary		= ., ?, !, U+2026 # horizontal ellipsis
	# phrase_boundary_step	= 100

	## 混合字符列表
	# blend_chars		= +, &, U+23
	# blend_mode		= trim_tail, skip_pure

	## html标记清理，是否从输出全文数据中去除HTML标记。
	html_strip		= 0

	## HTML标记属性索引设置。
	# html_index_attrs	= img=alt,title; a=title;

	## 需要清理的html元素
	# html_remove_elements	= style, script

	## searchd是预先打开全部索引还是每次查询再打开索引。
	# preopen			= 1

	## 字典文件是保持在磁盘上还是将他预先缓冲在内存中。
	# ondisk_dict		= 1

	## 由于在索引建立的时候，需要建立临时文件和和副本，还有旧的索引
	## 这个时候磁盘使用量会暴增，于是有个方法是临时文件重复利用
	## 这个配置会极大减少建立索引时候的磁盘压力，代价是索引建立速度变慢。
	# inplace_enable		= 1
	# inplace_hit_gap		= 0 # preallocated hitlist gap size
	# inplace_docinfo_gap	= 0 # preallocated docinfo gap size
	# inplace_reloc_factor	= 0.1 # relocation buffer size within arena
	# inplace_write_factor	= 0.1 # write buffer size within arena

	## 在经过过短的位置后增加位置值
	# overshort_step		= 1

	## 在经过 停用词 处后增加位置值
	# stopword_step		= 1

	## 位置忽略词汇列表
	# hitless_words		= all
	# hitless_words		= hitless.txt

	## 是否检测并索引句子和段落边界
	# index_sp			= 1

	## 字段内需要索引的HTML/XML区域的标签列表
	# index_zones		= title, h*, th
}

index test1stemmed : test1
{
	path			= /home/yejianfeng/instance/coreseek/var/data/test1stemmed
	morphology		= stem_en
}

index dist1
{
	type			= distributed

	local			= test1
	local			= test1stemmed

	## 分布式索引（distributed index）中的远程代理和索引声明
	agent			= localhost:9313:remote1
	agent			= localhost:9314:remote2,remote3
	# agent			= /var/run/searchd.sock:remote4

	## 分布式索引（ distributed index）中声明远程黑洞代理
	# agent_blackhole		= testbox:9312:testindex1,testindex2

	## 远程代理的连接超时时间
	agent_connect_timeout	= 1000

	## 远程查询超时时间
	agent_query_timeout	= 3000
}

index rt
{
	type			= rt

	path			= /home/yejianfeng/instance/coreseek/var/data/rt

	## RT索引内存限制
	# rt_mem_limit		= 512M

	## 全文字段定义
	rt_field		= title
	rt_field		= content

	## 无符号整数属性定义
	rt_attr_uint		= gid

	## 各种属性定义
	# rt_attr_bigint		= guid
	# rt_attr_float		= gpa
	# rt_attr_timestamp	= ts_added
	# rt_attr_string		= author
}

indexer
{
	## 建立索引的时候，索引内存限制
	mem_limit		= 32M

	## 每秒最大I/O操作次数，用于限制I/O操作
	# max_iops		= 40

	## 最大允许的I/O操作大小，以字节为单位，用于I/O节流
	# max_iosize		= 1048576

	## 对于XMLLpipe2数据源允许的最大的字段大小，以字节为单位
	# max_xmlpipe2_field	= 4M

	## 写缓冲区的大小，单位是字节
	# write_buffer		= 1M

	## 文件字段可用的最大缓冲区大小，字节为单位
	# max_file_field_buffer	= 32M
}

## 搜索服务配置
searchd
{
	# listen			= 127.0.0.1
	# listen			= 192.168.0.1:9312
	# listen			= 9312
	# listen			= /var/run/searchd.sock

	## 监听端口
	listen			= 9312
	listen			= 9306:mysql41

	## 监听日志
	log			= /home/yejianfeng/instance/coreseek/var/log/searchd.log

	## 查询日志
	query_log		= /home/yejianfeng/instance/coreseek/var/log/query.log

	## 客户端读超时时间 
	read_timeout		= 5

	## 客户端持久连接超时时间，即客户端读一次以后，持久连接，然后再读一次。中间这个持久连接的时间。
	client_timeout		= 300

	## 并行执行搜索的数目
	max_children		= 30

	## 进程id文件
	pid_file		= /home/yejianfeng/instance/coreseek/var/log/searchd.pid

	## 守护进程在内存中为每个索引所保持并返回给客户端的匹配数目的最大值
	max_matches		= 1000

	## 无缝轮转。防止 searchd 轮换在需要预取大量数据的索引时停止响应
	## 当进行索引轮换的时候，可能需要消耗大量的时间在轮换索引上。
	## 但是启动了无缝轮转，就以消耗内存为代价减少轮转的时间
	seamless_rotate		= 1

	## 索引预开启，是否强制重新打开所有索引文件
	preopen_indexes		= 1

	## 索引轮换成功之后，是否删除以.old为扩展名的索引拷贝
	unlink_old		= 1

	## 属性刷新周期
	## 就是使用UpdateAttributes()更新的文档属性每隔多少时间写回到磁盘中。
	# attr_flush_period	= 900

	## 索引字典存储方式
	# ondisk_dict_default	= 1

	## 用于多值属性MVA更新的存储空间的内存共享池大小
	mva_updates_pool	= 1M

	## 网络通讯时允许的最大的包的大小
	max_packet_size		= 8M

	## 崩溃日志文件
	# crash_log_path		= /home/yejianfeng/instance/coreseek/var/log/crash

	## 每次查询允许设置的过滤器的最大个数
	max_filters		= 256

	## 单个过滤器允许的值的最大个数
	max_filter_values	= 4096

	## TCP监听待处理队列长度
	# listen_backlog		= 5

	## 每个关键字的读缓冲区的大小
	# read_buffer		= 256K

	## 无匹配时读操作的大小
	# read_unhinted		= 32K

	## 每次批量查询的查询数限制
	max_batch_queries	= 32

	## 每个查询的公共子树文档缓存大小
	# subtree_docs_cache	= 4M

	## 每个查询的公共子树命中缓存大小
	# subtree_hits_cache	= 8M

	## 多处理模式（MPM）。 可选项；可用值为none、fork、prefork，以及threads。 默认在Unix类系统为form，Windows系统为threads。
	workers			= threads # for RT to work

	## 并发查询线程数
	# dist_threads		= 4

	## 二进制日志路径
	# binlog_path		= # disable logging
	# binlog_path		= /home/yejianfeng/instance/coreseek/var/data # binlog.001 etc will be created there

	## 二进制日志刷新
	# binlog_flush		= 2

	## 二进制日志大小限制
	# binlog_max_log_size	= 256M

	## 线程堆栈
	# thread_stack			= 128K

	## 关键字展开限制
	# expansion_limit		= 1000

	## RT索引刷新周期 
	# rt_flush_period		= 900

	## 查询日志格式
	## 可选项，可用值为plain、sphinxql，默认为plain。 
	# query_log_format		= sphinxql

	## MySQL版本设置
	# mysql_version_string	= 5.0.37

	## 插件目录
	# plugin_dir			= /usr/local/sphinx/lib

	## 服务端默认字符集
	# collation_server		= utf8_general_ci
	## 服务端libc字符集
	# collation_libc_locale	= ru_RU.UTF-8

	## 线程服务看守
	# watchdog				= 1
	## 兼容模式
	# compat_sphinxql_magics	= 1
}
```
#SphinxQL
##目录
1. SELECT（搜索查询）语法
2. SHOW META（显示查询状态信息）语法
3. SHOW WARNINGS（显示查询警告信息）语法
4. SHOW STATUS（显示服务端状态信息）语法
5. INSERT 和 REPLACE（数据插入和替换）语法
6. DELETE（数据删除）语法
7. SET（设置服务端变量）语法
8. BEGIN, COMMIT, 以及 ROLLBACK（事务处理）语法
9. CALL SNIPPETS（摘要生成）语法
10. CALL KEYWORDS（关键词生成）语法
11. SHOW TABLES（显示当前提供搜索服务的索引列表）语法
12. DESCRIBE（显示指定搜索服务索引的字段信息）语法
13. CREATE FUNCTION（添加自定义函数）语法
14. DROP FUNCTION（删除自定义函数）语法
15. SHOW VARIABLES（显示服务器端变量）语法
16. SHOW COLLATION（显示字符集校对）语法
17. UPDATE（数据更新）语法
18. 多结果集查询（批量查询）
19. COMMIT（注释）语法
20. SphinxQL 保留关键字列表
21. SphinxQL 升级备注, version 2.0.1-beta

###Select（搜索语法）
	SELECT
    select_expr [, select_expr ...]
    FROM index [, index2 ...]
    [WHERE where_condition]
    [GROUP [N] BY {col_name | expr_alias} [, {col_name | expr_alias}]]
    [WITHIN GROUP ORDER BY {col_name | expr_alias} {ASC | DESC}]
    [HAVING having_condition]
    [ORDER BY {col_name | expr_alias} {ASC | DESC} [, ...]]
    [LIMIT [offset,] row_count]
    [OPTION opt_name = opt_value [, ...]]
    [FACET facet_options[ FACET facet_options][ ...]]

####select_expr
fields可以是列名、任意表达式以及*号，<font color="#FF0000">与常规SQL不同在于，表达式必须用一个AS别名来标识</font>
如：SELECT @id, group_id*123+456 AS expr1 FROM test1
从2.0.1测试版本开始AS是可选的。
<font color="#FF0000">EXIST()函数</font>，从2.1.1版本开始可以使用，如EXIST(“attr-name”,default-value)，当指定的attr-name为空的时候，使用默认值代替。这个功能在某些地方是十分好用的，比如搜索几个索引，而每个索引还不一致：
如：SELECT *, EXIST('gid', 6) as cnd FROM i1, i2 WHERE cnd>5
SNIPPET()函数

####from
from语句应该使用索引列表中的项，不同于常规的SQL语句，逗号在from语句中表示全文索引的枚举，与Query()的调用类似。而不再是常规的JOIN操作。

####where
这个语句中的元素都会映射到全文查询和过滤器。
比较运算符(=，!，=，<，>，<=，>=)，IN，AND，NOT，BETWEENT语句都是支持的或者被映射为了一个过滤器来实现，<font color="#FF0000">OR暂时不支持</font>，但是将来会支持。<font color="#FF0000">Match(“查询”)</font>是支持的，该语句作为一个全文检索来实现。Query函数将被解释为全文检索，where子句中，最多有一个Match()语句。

####group by
支持多个值或者表达式的group by：

	SELECT *, group_id*1000+article_type AS gkey FROM example GROUP BY gkey

	SELECT id FROM products GROUP BY region, price
支持聚合函数：(AVG(), MIN(), MAX(), SUM())，聚合函数的参数可以是普通的属性或者任意的表达式。
count(*)在group by中被隐含支持，结果在@count列中。
支持COUNT(DISTINCT attr)，但是一个语句最多一个COUNT(DISTINCT attr)，且参数必须是一个属性。这两个限制在将来可能会取消。从2.0.1开始，group by支持String类型。
如：

	SELECT *, AVG(price) AS avgprice, COUNT(DISTINCT storeid) 
	FROM products 
	WHERE MATCH('ipod')
	GROUP BY vendorid
GROUPBY()函数是支持的，该函数返回GROUPBY键。当GROUPBY一个MVA值的时候，为了选择当前组的特殊的值。
如：

	SELECT *, AVG(price) AS avgprice, COUNT(DISTINCT storeid), GROUPBY()
	FROM products
	WHERE MATCH('ipod')
	GROUP BY vendorid
从2.2.1开始，可以选择<font color="#FF0000">每个group中的topN</font>数据：

	如：SELECT id FROM products GROUP 3 BY category	
	如：SELECT group_id, MAX(id) AS max_id
		FROM my_index WHERE MATCH('the')
		GROUP BY group_id ORDER BY max_id DESC

GROUP_CONCAT()函数，将group中的某个值，用逗号分隔之后，展示出来。
SELECT id, GROUP_CONCAT(price) as pricesList, GROUPBY() AS name FROM shops GROUP BY shopName;

####WITHIN GROUP ORDER BY
在一个group by中，选出该group中的最佳“行”，不是一个group为一行。
如：

	SELECT *, INTERVAL(posted,NOW()-7*86400,NOW()-86400) AS timeseg
	FROM example WHERE MATCH('my search query')
	GROUP BY siteid
	WITHIN GROUP ORDER BY @weight DESC
	ORDER BY timeseg DESC, @weight DESC

	SELECT *, INTERVAL(posted,NOW()-7*86400,NOW()-86400) AS timeseg, WEIGHT() AS w
	FROM example WHERE MATCH('my search query')
	GROUP BY siteid
	WITHIN GROUP ORDER BY w DESC
	ORDER BY timeseg DESC, w DESC

####ORDER BY
ORDER BY的参数只能是列名（不能是表达式），但是可以计算之后别名来排序：

	如：SELECT *, @weight*10+docboost AS skey FROM example ORDER BY key
	SELECT *, WEIGHT()*10+docboost AS skey FROM example ORDER BY skey
2.0.1之后，支持string类型
2.1.1之后，能够进行子查询。

	SELECT id,a_slow_expression() AS cond FROM an_index ORDER BY id ASC, cond DESC LIMIT 100;
上面语句比下面要好，因为上面语句，慢表达式将会被过滤，而下面的过滤仅仅会针对部分集合。

	SELECT * FROM (SELECT id,a_slow_expression() AS cond FROM an_index ORDER BY id ASC LIMIT 100) ORDER BY cond DESC;


####HAVING(2.2.1)
用来过滤GROUP BY的结果集。当前仅仅支持一个过滤条件

	SELECT id FROM plain GROUP BY title HAVING group_id=16;
	SELECT id FROM plain GROUP BY attribute HAVING COUNT(*)>1;

####LIMIT
LIMIT N和LIMIT M,N两种方式都是支持的。与常规的SQL不同，sphinx默认加上了一个LIMIT 0,20。
OPTION
这个是Sphinx特定的拓展，可以让你控制每个SQL语句的选项：
支持的Option以及对应的值如下：

	'ranker' - any of 'proximity_bm25', 'bm25', 'none', 'wordcount', 'proximity', 'matchany', or 'fieldmask'
	'max_matches' - integer (每个查询的最大matches值)
	'cutoff' - integer (最大的found matches阀值)
	'max_query_time' - integer (最次查询大的搜索时间, msec)
	'retry_count' - integer (distributed retries count，分布式重试次数)
	'retry_delay' - integer (distributed retry delay, msec，分布式重试时间)
	'field_weights' - a named integer list (per-field user weights for ranking，每个字段的用户权重排名)
	'index_weights' - a named integer list (per-index user weights for ranking，每个索引的用户权重排)
	'reverse_scan' - 0 or 1, lets you control the order in which full-scan query processes the rows，让你能够控制全扫描搜索中行的处理顺序。
如：

	SELECT * FROM test WHERE MATCH('@title hello @body world')
	OPTION ranker=bm25, max_matches=3000,
	field_weights=(title=10, body=3)
	
####FACET



###INSERT & REPLACE syntax（插入和替换）语法
	语句：{INSERT | REPLACE} INTO index [(column, ...)]
    VALUES (value, ...)
    [, (...)]
该语句仅仅针对<font color="#FF0000">RT索引(实时索引)</font>有效，在一个已经存在的索引里插入指定的数据。
必须有ID，多个ID时，请使用REPLACE。REPLACE和INSERT基本一致，但是当一个ID已经存在的时候，会先删除老的，然后再INSERT新的数据。
Index是需要insert的索引名称。而(value,…)列表可以指定需要插入的数据，其他的value将会被默认值填充。（0或空string）
暂时不支持表达式，value值需要指定。
多行插入也能使用一个单一的INSERT语句，通过使用逗号分隔的，圆括号闭合的多value值。


###DELETE语句（数据删除）语法
	语句：DELETE FROM index WHERE where_condition
DELETE语句仅仅支持<font color="#FF0000">RT索引（实时索引）</font>或者是以实时索引作为分布式的代理？
该语句通过ID在索引中删除一个存在的行。
index表示需要操作的索引。
where子句和select子句中的where一致。
举例：

	mysql> select * from rt;
+------+------+-------------+------+
| id   | gid  | mva1        | mva2 |
+------+------+-------------+------+
|  100 | 1000 | 100,201     | 100  |
|  101 | 1001 | 101,202     | 101  |
|  102 | 1002 | 102,203     | 102  |
|  103 | 1003 | 103,204     | 103  |
|  104 | 1004 | 104,204,205 | 104  |
|  105 | 1005 | 105,206     | 105  |
|  106 | 1006 | 106,207     | 106  |
|  107 | 1007 | 107,208     | 107  |
+------+------+-------------+------+
8 rows in set (0.00 sec)

	mysql> delete from rt where match ('dumy') and mva1>206;
Query OK, 2 rows affected (0.00 sec)

mysql> select * from rt;
+------+------+-------------+------+
| id   | gid  | mva1        | mva2 |
+------+------+-------------+------+
|  100 | 1000 | 100,201     | 100  |
|  101 | 1001 | 101,202     | 101  |
|  102 | 1002 | 102,203     | 102  |
|  103 | 1003 | 103,204     | 103  |
|  104 | 1004 | 104,204,205 | 104  |
|  105 | 1005 | 105,206     | 105  |
+------+------+-------------+------+
6 rows in set (0.00 sec)

	mysql> delete from rt where id in (100,104,105);
Query OK, 3 rows affected (0.01 sec)

mysql> select * from rt;
+------+------+---------+------+
| id   | gid  | mva1    | mva2 |
+------+------+---------+------+
|  101 | 1001 | 101,202 | 101  |
|  102 | 1002 | 102,203 | 102  |
|  103 | 1003 | 103,204 | 103  |
+------+------+---------+------+
3 rows in set (0.00 sec)

	mysql> delete from rt where mva1 in (102,204);
Query OK, 2 rows affected (0.01 sec)

mysql> select * from rt;
+------+------+---------+------+
| id   | gid  | mva1    | mva2 |
+------+------+---------+------+
|  101 | 1001 | 101,202 | 101  |
+------+------+---------+------+
1 row in set (0.00 sec)


###事物
	START TRANSACTION | BEGIN
	COMMIT
	ROLLBACK
	SET AUTOCOMMIT = {0 | 1}
BEGIN或者START TRANSACTION语句开始一个事物，COMMIT提交当前的事物，使改变永久化。ROLLBACK回滚当前的事物，取消其变化。
SET AUTOCOMMIT在当前session中控制自动提交模式。自动提交默认为1，即在任何索引中每个修改语句(insert replace等等)语句，都会被一个BEGIN和COMMIT包含。


###UPDATE语句（数据更新）语法
	语句：UPDATE index SET col1 = newval1 [, ...] WHERE where_condition [OPTION opt_name = opt_value [, …]]
UPDATE语句支持多个属性和值，无论是<font color="#FF0000">RT索引还是硬盘索引</font>，都支持！
从2.0.2版本开始，所有的属性类型(int,bigint,float,<font color="#FF0000">MVA</font>)，除了string和json属性，都能够被自动更新。
where子句和select中的where子句一致。
注意：当更新一个32bits的属性时，系统将会强制截取值的低32位作为值。
MVA类型的值需要用在圆括号中以逗号分隔。如果需要清除MVA值，仅仅需要使用()值覆盖即可。(MVA是一个标签属性，即逗号分隔，如campaign中的join_theme)
从2.2.1开始，UPDATE语句能够更新JSON数组中的Int和float值（不支持string,arrays和其他类型）

	mysql> UPDATE myindex SET enabled=0 WHERE id=123;
Query OK, 1 rows affected (0.00 sec)

mysql> UPDATE myindex
  SET bigattr=-100000000000,
    fattr=3465.23,
    mvattr1=(3,6,4),
    mvattr2=()
  WHERE MATCH('hehe') AND enabled=1;
Query OK, 148 rows affected (0.01 sec)
####OPTION子句
这是一个Sphinx特有的子句，用来控制每个update子句的选项，句式如下：

	OPTION <optionname>=<value> [ , ... ]
基本和SELECT中的OPTION类似，下面几个选项是UPDATE语句独有的：

	'ignore_nonexistent_columns' - this option, added in version 2.1.1-beta, points that the update will silently ignore any warnings about trying to update a column which is not exists in current index schema.
	'strict' - this option is used while updating JSON attributes. As of 2.2.1-beta, it's possible to update just some types in JSON. And if you try to update, for example, array type you'll get error with 'strict' option on and warning otherwise.


###多语句查询（批量查询）
	<?php
	$link = mysqli_connect ( "127.0.0.1", "root", "", "", 9306 );
	if ( mysqli_connect_errno() )
    die ( "connect failed: " . mysqli_connect_error() );
    
	$batch = "SELECT * FROM test1 ORDER BY group_id ASC;";
	$batch .= "SELECT * FROM test1 ORDER BY group_id DESC";
	
	if ( !mysqli_multi_query ( $link, $batch ) )
    die ( "query failed" );
    
    do
    {
    // fetch and print result set
    if ( $result = mysqli_store_result($link) )
    {
        while ( $row = mysqli_fetch_row($result) )
            printf ( "id=%s\n", $row[0] );
        mysqli_free_result($result);
    }

    // print divider
    if ( mysqli_more_results($link) )
        printf ( "------\n" );
        
        } while ( mysqli_next_result($link) );
Its output with the sample test1 index included with Sphinx is as follows.

$ php test_multi.php

	id=1
	id=2
	id=3
	id=4
- - - -
	id=3
	id=4
	id=1
	id=2


###SHOW META
展示上一次搜索执行的一些额外信息，比如搜索时间以及关键词统计数据等等。只有当searchd以iostats和- -cpustats开启的时候，才会在show meta中展示。

	mysql> SELECT * FROM test1 WHERE MATCH('test|one|two');
+------+--------+----------+------------+
| id   | weight | group_id | date_added |
+------+--------+----------+------------+
|    1 |   3563 |      456 | 1231721236 |
|    2 |   2563 |      123 | 1231721236 |
|    4 |   1480 |        2 | 1231721236 |
+------+--------+----------+------------+
3 rows in set (0.01 sec)

	mysql> SHOW META;
+-----------------------+-------+
| Variable_name         | Value |
+-----------------------+-------+
| total                      | 3     |
| total_found           | 3     |
| time                      | 0.005 |
| keyword[0]            | test  |
| docs[0]                 | 3     |
| hits[0]                   | 5     |
| keyword[1]           | one   |
| docs[1]                 | 1     |
| hits[1]                   | 2     |
| keyword[2]           | two   |
| docs[2]                 | 1     |
| hits[2]                   | 2     |
| cpu_time              | 0.350 |
| io_read_time        | 0.004 |
| io_read_ops         | 2     |
| io_read_kbytes     | 0.4   |
| io_write_time        | 0.000 |
| io_write_ops         | 0     |
| io_write_kbytes    | 0.0   |
| agents_cpu_time       | 0.000 |
| agent_io_read_time    | 0.000 |
| agent_io_read_ops     | 0     |
| agent_io_read_kbytes  | 0.0   |
| agent_io_write_time   | 0.000 |
| agent_io_write_ops    | 0     |
| agent_io_write_kbytes | 0.0   |
+-----------------------+-------+
12 rows in set (0.00 sec)

在2.1.1版本以后，还可以使用Like语句：

	mysql> SHOW META LIKE 'total%';
+-----------------------+-------+
| Variable_name         | Value |
+-----------------------+-------+
| total                 | 3     |
| total_found           | 3     |
+-----------------------+-------+
2 rows in set (0.00 sec)

	SHOW WARNINGS syntax
可以获取上一次query语句产生的warning语句，错误信息也会一道返回。

	mysql> SELECT * FROM test1 WHERE MATCH('@@title hello') \G
ERROR 1064 (42000): index test1: syntax error, unexpected TOK_FIELDLIMIT
near '@title hello'

	mysql> SELECT * FROM test1 WHERE MATCH('@title -hello') \G
ERROR 1064 (42000): index test1: query is non-computable (single NOT operator)

	mysql> SELECT * FROM test1 WHERE MATCH('"test doc"/3') \G
*************************** 1. row ***************************
        id: 4
    weight: 2500
  group_id: 2
date_added: 1231721236
1 row in set, 1 warning (0.00 sec)

mysql> SHOW WARNINGS \G
*************************** 1. row ***************************
  Level: warning
   Code: 1000
Message: quorum threshold too high (words=2, thresh=3); replacing quorum operator
         with AND operator
1 row in set (0.00 sec)

SHOW STATUS syntax
2.1.1之后，也可以使用like%语句了。

	mysql> SHOW STATUS;
+--------------------+-------+
| Counter            | Value |
+--------------------+-------+
| uptime             | 216   |
| connections        | 3     |
| maxed_out          | 0     |
| command_search     | 0     |
| command_excerpt    | 0     |
| command_update     | 0     |
| command_keywords   | 0     |
| command_persist    | 0     |
| command_status     | 0     |
| agent_connect      | 0     |
| agent_retry        | 0     |
| queries            | 10    |
| dist_queries       | 0     |
| query_wall         | 0.075 |
| query_cpu          | OFF   |
| dist_wall          | 0.000 |
| dist_local         | 0.000 |
| dist_wait          | 0.000 |
| query_reads        | OFF   |
| query_readkb       | OFF   |
| query_readtime     | OFF   |
| avg_query_wall     | 0.007 |
| avg_query_cpu      | OFF   |
| avg_dist_wall      | 0.000 |
| avg_dist_local     | 0.000 |
| avg_dist_wait      | 0.000 |
| avg_query_reads    | OFF   |
| avg_query_readkb   | OFF   |
| avg_query_readtime | OFF   |
+--------------------+-------+
29 rows in set (0.00 sec)



###SET语句（设置服务端变量）语法
	SET [GLOBAL] server_variable_name = value
	SET [INDEX index_name] GLOBAL @user_variable_name = (int_val1 [, int_val2, ...])
	SET NAMES value
	SET @@dummy_variable = ignored_value
貌似是设置一个全局变量
没啥大用，随后翻译。




###SHOW TABLES 语句
	语句：SHOW TABLES [ LIKE pattern ]
mysql> SHOW TABLES;
+-------+-------------+
| Index | Type        |
+-------+-------------+
| dist1 | distributed |
| rt    | rt          |
| test1 | local       |
| test2 | local       |
+-------+-------------+
4 rows in set (0.00 sec)

DESCRIBE语句

	mysql> DESC rt;
+---------+---------+
| Field   | Type    |
+---------+---------+
| id      | integer |
| title   | field   |
| content | field   |
| gid     | integer |
+---------+---------+
4 rows in set (0.00 sec)



###CREATE FUNCTION 语句
	CREATE FUNCTION udf_name
    RETURNS {INT | INTEGER | BIGINT | FLOAT | STRING}
    SONAME 'udf_lib_file'
CREATE FUNCTION statement, introduced in version 2.0.1-beta, installs a user-defined function (UDF) with the given name and type from the given library file. The library file must reside in a trusted plugin_dir directory. On success, the function is available for use in all subsequent queries that the server receives. Example:

	mysql> CREATE FUNCTION avgmva RETURNS INTEGER SONAME 'udfexample.dll';
Query OK, 0 rows affected (0.03 sec)

mysql> SELECT *, AVGMVA(tag) AS q from test1;
+------+--------+---------+-----------+
| id   | weight | tag     | q         |
+------+--------+---------+-----------+
|    1 |      1 | 1,3,5,7 | 4.000000  |
|    2 |      1 | 2,4,6   | 4.000000  |
|    3 |      1 | 15      | 15.000000 |
|    4 |      1 | 7,40    | 23.500000 |
+------+--------+---------+-----------+



###DROP FUNCTION 语句
	DROP FUNCTION udf_name
DROP FUNCTION statement, introduced in version 2.0.1-beta, deinstalls a user-defined function (UDF) with the given name. On success, the function is no longer available for use in subsequent queries. Pending concurrent queries will not be affected and the library unload, if necessary, will be postponed until those queries complete. Example:

mysql> DROP FUNCTION avgmva;
Query OK, 0 rows affected (0.00 sec)


###Sphinx保留关键字
AND, AS, BY, DIV, FACET, FALSE, FROM, ID, IN, IS, LIMIT,
MOD, NOT, NULL, OR, ORDER, SELECT, TRUE


###Select"或"关系查询解决
	select * from t_test_filter_sphinxSE 
	where query="nick;select=IF( FUserId=34,1,0) + IF(Fshopid=1234567,10,0) as match_qq; filter= match_qq, 1,10,11";

两个IF语句构成一个filter(filter是个二进制)，filter在1，10，11中，即或关系。

	select popularity_score,omg_score,periods_stisfy_rate
	,if((popularity_score>0 or img_score>1 or periods_satisfy_rate>0 or comment_num >1 or goods_on_shelf > 1388131559 or is_mlzz=0 or picture_auth_status=9),1,0) as or_filter 
	where match(‘@shop_id s100843s’) and or_filter=1,show meta;

![enter image description here](http://bizfe.meilishuo.com/md-imgs/d8b885507371a13c409628fda7111c43.jpg "}[B~T92@L76Z)COD_DQTWP2")

大概400万数据，几毫秒即出结果。关键是shop_id匹配减少了筛选项。

###表达式、函数以及操作
链接：http://sphinxsearch.com/docs/current.html#operators
####操作：
Arithmetic operators: +, -, *, /, %, DIV, MOD
Comparison operators: <, > <=, >=, =, <>
Boolean operators: AND, OR, NOT
Bitwise operators: &, |
####数字函数
ABS()
BITDOT()
CEIL()
CONTAINS()
COS()
DOUBLE()
EXP()
FIBONACCI()
FLOOR()
GEOPOLY2D()
IDIV()
LN()
LOG10()
LOG2()
MAX()
MIN()
POLY2D()
POW()
SIN()
SQRT()
UINT()


#### 日期和时间函数
DAY()
Returns the integer day of month (in 1..31 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

MONTH()
Returns the integer month (in 1..12 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

NOW()
Returns the current timestamp as an INTEGER. Introduced in version 0.9.9-rc1.

YEAR()
Returns the integer year (in 1969..2038 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

YEARMONTH()
Returns the integer year and month code (in 196912..203801 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

YEARMONTHDAY()
Returns the integer year, month, and date code (in 19691231..20380119 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

#### 类型转换函数
BIGINT()

INTEGER()

SINT()

#### 比较函数
IF()
IF() behavior is slightly different that that of its MySQL counterpart. It takes 3 arguments, check whether the 1st argument is equal to 0.0, returns the 2nd argument if it is not zero, or the 3rd one when it is. Note that unlike comparison operators, IF() does not use a threshold! Therefore, it's safe to use comparison results as its 1st argument, but arithmetic operators might produce unexpected results. For instance, the following two calls will produce different results even though they are logically equivalent:
IF ( sqrt(3)*sqrt(3)-3<>0, a, b )
IF ( sqrt(3)*sqrt(3)-3, a, b )
In the first case, the comparison operator <> will return 0.0 (false) because of a threshold, and IF() will always return 'b' as a result. In the second one, the same sqrt(3)*sqrt(3)-3 expression will be compared with zero without threshold by the IF() function itself. But its value will be slightly different from zero because of limited floating point calculations precision. Because of that, the comparison with 0.0 done by IF() will not pass, and the second variant will return 'a' as a result.

IN()
IN(expr,val1,val2,...), introduced in version 0.9.9-rc1, takes 2 or more arguments, and returns 1 if 1st argument (expr) is equal to any of the other arguments (val1..valN), or 0 otherwise. Currently, all the checked values (but not the expression itself!) are required to be constant. (Its technically possible to implement arbitrary expressions too, and that might be implemented in the future.) Constants are pre-sorted and then binary search is used, so IN() even against a big arbitrary list of constants will be very quick. Starting with 0.9.9-rc2, first argument can also be a MVA attribute. In that case, IN() will return 1 if any of the MVA values is equal to any of the other arguments. Starting with 2.0.1-beta, IN() also supports IN(expr,@uservar) syntax to check whether the value belongs to the list in the given global user variable. First argument can be JSON attribute since 2.2.1-beta.

INTERVAL()
INTERVAL(expr,point1,point2,point3,...), introduced in version 0.9.9-rc1, takes 2 or more arguments, and returns the index of the argument that is less than the first argument: it returns 0 if expr<point1, 1 if point1<=expr<point2, and so on. It is required that point1<point2<...<pointN for this function to work correctly.

#### 其他函数
ALL()
ANY()
ATAN2()
...
###表达式、函数以及操作

同时支持API和SphinxQL的形式 
链接：http://sphinxsearch.com/docs/current.html#operators

#### 操作：

Arithmetic operators: +, -, *, /, %, DIV, MOD 
Comparison operators: <, > <=, >=, =, <> 
Boolean operators: AND, OR, NOT 
Bitwise operators: &, |

#### 数字函数

ABS() 
BITDOT() 
CEIL() 
CONTAINS() 
COS() 
DOUBLE() 
EXP() 
FIBONACCI() 
FLOOR() 
GEOPOLY2D() 
IDIV() 
LN() 
LOG10() 
LOG2() 
MAX() 
MIN() 
POLY2D() 
POW() 
SIN() 
SQRT() 
UINT()

#### 日期和时间函数

DAY() 
Returns the integer day of month (in 1..31 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

MONTH() 
Returns the integer month (in 1..12 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

NOW() 
Returns the current timestamp as an INTEGER. Introduced in version 0.9.9-rc1.

YEAR() 
Returns the integer year (in 1969..2038 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

YEARMONTH() 
Returns the integer year and month code (in 196912..203801 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

YEARMONTHDAY() 
Returns the integer year, month, and date code (in 19691231..20380119 range) from a timestamp argument, according to the current timezone. Introduced in version 2.0.1-beta.

#### 类型转换函数

BIGINT()
INTEGER()
SINT()

#### 比较函数

IF() 
IF() behavior is slightly different that that of its MySQL counterpart. It takes 3 arguments, check whether the 1st argument is equal to 0.0, returns the 2nd argument if it is not zero, or the 3rd one when it is. Note that unlike comparison operators, IF() does not use a threshold! Therefore, it’s safe to use comparison results as its 1st argument, but arithmetic operators might produce unexpected results. For instance, the following two calls will produce different results even though they are logically equivalent: 
IF ( sqrt(3)*sqrt(3)-3<>0, a, b ) 
IF ( sqrt(3)*sqrt(3)-3, a, b ) 
In the first case, the comparison operator <> will return 0.0 (false) because of a threshold, and IF() will always return ‘b’ as a result. In the second one, the same sqrt(3)*sqrt(3)-3 expression will be compared with zero without threshold by the IF() function itself. But its value will be slightly different from zero because of limited floating point calculations precision. Because of that, the comparison with 0.0 done by IF() will not pass, and the second variant will return ‘a’ as a result.

IN() 
IN(expr,val1,val2,…), introduced in version 0.9.9-rc1, takes 2 or more arguments, and returns 1 if 1st argument (expr) is equal to any of the other arguments (val1..valN), or 0 otherwise. Currently, all the checked values (but not the expression itself!) are required to be constant. (Its technically possible to implement arbitrary expressions too, and that might be implemented in the future.) Constants are pre-sorted and then binary search is used, so IN() even against a big arbitrary list of constants will be very quick. Starting with 0.9.9-rc2, first argument can also be a MVA attribute. In that case, IN() will return 1 if any of the MVA values is equal to any of the other arguments. Starting with 2.0.1-beta, IN() also supports IN(expr,@uservar) syntax to check whether the value belongs to the list in the given global user variable. First argument can be JSON attribute since 2.2.1-beta.

INTERVAL() 
INTERVAL(expr,point1,point2,point3,…), introduced in version 0.9.9-rc1, takes 2 or more arguments, and returns the index of the argument that is less than the first argument: it returns 0 if expr

#### 其他函数
ALL() 
ANY() 
ATAN2() 
…

### 查询API：
#### SetLimits
函数: function SetLimits ( $offset, $limit, $max_matches=1000, $cutoff=0 )

Sets offset into server-side result set ($offset) and amount of matches to return to client starting from that offset ($limit). Can additionally control maximum server-side result set size for current query ($max_matches) and the threshold amount of matches to stop searching at ($cutoff). All parameters must be non-negative integers.

First two parameters to SetLimits() are identical in behavior to MySQL LIMIT clause. They instruct searchd to return at most $limit matches starting from match number $offset. The default offset and limit settings are 0 and 20, that is, to return first 20 matches.

max_matches setting controls how much matches searchd will keep in RAM while searching. All matching documents will be normally processed, ranked, filtered, and sorted even if max_matches is set to 1. But only best N documents are stored in memory at any given moment for performance and RAM usage reasons, and this setting controls that N. Note that there are two places where max_matches limit is enforced. Per-query limit is controlled by this API call, but there also is per-server limit controlled by max_matches setting in the config file. To prevent RAM usage abuse, server will not allow to set per-query limit higher than the per-server limit.

You can't retrieve more than max_matches matches to the client application. The default limit is set to 1000. Normally, you must not have to go over this limit. One thousand records is enough to present to the end user. And if you're thinking about pulling the results to application for further sorting or filtering, that would be much more efficient if performed on Sphinx side.

$cutoff setting is intended for advanced performance control. It tells searchd to forcibly stop search query once $cutoff matches had been found and processed.

#### SetMaxQueryTime
Prototype: function SetMaxQueryTime ( $max_query_time )

Sets maximum search query time, in milliseconds. Parameter must be a non-negative integer. Default value is 0 which means "do not limit".

Similar to $cutoff setting from SetLimits(), but limits elapsed query time instead of processed matches count. Local search queries will be stopped once that much time has elapsed. Note that if you're performing a search which queries several local indexes, this limit applies to each index separately.

#### SetSelect
Prototype: function SetSelect ( $clause )

设置选择子句，展示需要拉取的字段，以及表达式。句式模仿SQL。

计算表达式和字段一样，能用在排序(sorting)，过滤以及分组上。
0.99以后，在有group的子句中，(AVG(), MIN(), MAX(), SUM())函数能够被使用。

Example:

	$cl->SetSelect ( "*, @weight+(user_karma+ln(pageviews))*0.1 AS myweight" );
	$cl->SetSelect ( "exp_years, salary_gbp*{$gbp_usd_rate} AS salary_usd,IF(age>40,1,0) AS over40" );
	$cl->SetSelect ( "*, AVG(price) AS avgprice" );


### 结果集过滤器设置
#### SetIDRange
函数: function SetIDRange ( $min, $max )
设置一个ID的区间，参数必须是整数，默认0-0，即无限制。
设置该参数之后，只有该区间内的东西会被选出来。

#### SetFilter
函数: function SetFilter ( $attribute, $values, $exclude=false )
即attribute (exclude?in:not in) values

#### SetFilterRange
函数: function SetFilterRange ( $attribute, $min, $max, $exclude=false )
设置属性的区间过滤

#### SetFilterFloatRange
函数: function SetFilterFloatRange ( $attribute, $min, $max, $exclude=false )


#### SetFilterString

函数: function SetFilterString ( $attribute, $value, $exclude=false )
String的过滤器。

On this call, additional new filter is added to the existing list of filters. $attribute must be a string with attribute name. $value must be a string. $exclude must be a boolean value; it controls whether to accept the matching documents (default mode, when $exclude is false) or reject them.

Only those documents where $attribute column value stored in the index matches string value from $value will be matched (or rejected, if $exclude is true).
#目前我们遇到的问题
1.实时性问题

      **`#新增数据无法立马搜索怎么办？`**
              之前我们采取主+增  主合并增的 架构方案 实际应用中发现新增数据延迟1分钟左右，为了解决这个问题 我们采取了 主+大增量+实时增量的架构方式来解决延时问题，目前的这种方案基本延迟时间在十秒内。
              
      **`#实时更新字段有时不生效怎么办？`**
               由于我们采用了主+大增量+实时增量的架构方式，基本上我们每时每刻都在合并索引，而sphinx实时更新字段属性的机制是先写到内存里，在写到磁盘里，如果更新的时候正好赶上更新的这条文档所在的数据的索引正在合并，就会导致当时成功了但是合并完索引就不成功了，为了解决这个问题我们又采取了实时更新字段也放到增量索引里。这样就保证了数据的一致性。











