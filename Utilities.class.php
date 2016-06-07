<?php
namespace Framework\Libs\Util;

//config for image host
use Framework\Config\MemcacheCluster;
use Framework\Libs\Mail\Helper;
use Framework\Libs\Monitor\MonitorManager;

$GLOBALS['PICTURE_DOMAINS'] = array (
    'a' => 'http://imgtest.meiliworks.com',
    //tx
    'b' => 'http://d04.res.meilishuo.net',
    //tx
    'c' => 'http://imgtest-dl.meiliworks.com',
    //dl
    'd' => 'http://d06.res.meilishuo.net',
    //dl
    'e' => 'http://d05.res.meilishuo.net',
    //ws
    'f' => 'http://d01.res.meilishuo.net',
    //ws
    'g' => 'http://d02.res.meilishuo.net',
    //tx
    'h' => 'http://d03.res.meilishuo.net',
    //tx
);

$GLOBALS['PICTURE_DOMAINS_ALLOCATION'] = 'aaddbbgggggggggggghhhhhhhhhhhhbbbbbbbbbbccddddddddggdddddddddddddddddeeeeeeeeeeeeeeeeeeeeeeeeeedddff';

class Utilities {
    private static $default_split_arr = array(
        array(
            'split' => ',',
            'type'  => 'arr',
        ),
        array(
            'split' => ';',
            'type'  => 'arr',
        ),
        array(
            'split' => ':',
            'type'  => 'key-val',
        ),
        array(
            'split' => '$',
            'type'  => 'key-val',
        ),
        array(
            'split' => '|',
            'type'  => 'arr',
        ),
    );

    public static function convertPicture($key) {

        if (strncasecmp($key, 'http://', strlen('http://')) == 0) {
            return $key;
        }

        $key = ltrim($key, '/');
        $hostPart = self::getPictureHost($key);
        if (empty($key)) {
            return $hostPart . '/css/images/0.gif';
        }

        return $hostPart . '/' . $key;
    }

    private static function getPictureHost($key) {
        if (empty($key)) {
            return $GLOBALS['PICTURE_DOMAINS']['a'];
        }
        if (substr($key, 0, 3) === 'css' && defined('CSS_JS_BASE_URL')) {
            return rtrim(CSS_JS_BASE_URL, '/');
        }
        $remain = crc32($key) % 100;
        $remain = abs($remain);
        $hashKey = $GLOBALS['PICTURE_DOMAINS_ALLOCATION'][$remain];

        return $GLOBALS['PICTURE_DOMAINS'][$hashKey];
    }

    /**
     * 通用获取图片缩放地址
     * @param string $uri
     * @return string $url
     */
    public static function getZoomPicUrl($picUrl, $zType = 'z1') {
        $amount = '';
        $radius = '';
        $threshold = '';
        if ($zType == 'z1') {
            $token = '156218meilishuonewyearhappy'; //缩放服务的token值
            $width = 156;
            $height = 218;
            $type = 's2';
        } elseif ($zType == 'z2') {
            $width = 230;
            $height = 328;
            $token = '15050230328meilishuonewyearhappy';
            $amount = 150;
            $radius = 5;
            $threshold = "0";
            $type = 's6';
        } elseif ($zType == 'z3') {
            $width = 226;
            $height = 800;
            $token = '20060226800meilishuonewyearhappy';
            $amount = 200;
            $radius = 6;
            $threshold = "0";
            $type = 's1';
        } elseif ($zType == 'z4') {
            $width = 226;
            $height = 800;
            $token = '15050226800meilishuonewyearhappy';
            $amount = 150;
            $radius = 5;
            $threshold = "0";
            $type = 's1';
        } elseif ($zType == 'z5') {
            $token = '180255meilishuonewyearhappy'; //缩放服务的token值
            $width = 180;
            $height = 255;
            $type = 's2';
        } elseif ($zType == 'z6') {
            $token = '6868meilishuonewyearhappy'; //缩放服务的token值
            $width = 68;
            $height = 68;
            $type = 's3';
        } elseif ($zType == 'z7') {
            $token = '15050226226meilishuonewyearhappy'; //缩放服务的token值
            $width = 226;
            $height = 226;
            $amount = 150;
            $radius = 5;
            $threshold = "0";
            $type = 's3';
        } elseif ($zType == 'z8') {
            $token = '450680meilishuonewyearhappy'; //缩放服务的token值
            $width = 450;
            $height = 680;
            $type = 's7';
        } elseif ($zType == 'z9') {
            $token = '290290meilishuonewyearhappy'; //缩放服务的token值
            $width = 290;
            $height = 290;
            $type = 's0';
        } elseif ($zType == 'z10') {
            $token = '208208meilishuonewyearhappy'; //缩放服务的token值
            $width = 208;
            $height = 208;
            $type = 's0';
        }

        //zoom类型，s3是表示指定目标宽高，如果有参差，则截断
        if (empty($picUrl)) {
            return '';
        }
        $urlArgs = parse_url($picUrl);
        $uri = ltrim($urlArgs['path'], '/');
        if (empty($uri)) {
            return '';
        }
        $md5sum = md5($uri . $token);
        $eightM = substr($md5sum, 0, 8);
        $target = $picUrl . "_{$eightM}_{$type}";
        $amount !== '' && $target .= "_q0_{$amount}_{$radius}_{$threshold}";
        $target .= "_{$width}_{$height}.jpg";
        return $target;
    }
    public static function getPictureUrl($key, $type = "_o", $ignorecase = true) {
        if (empty($key) || empty($type)) {
            return '';
        }
        if ($ignorecase) {
            $type = strtolower($type);
        }
        $key = str_replace('/_o/', '/' . $type . '/', $key);

        $key = trim($key);
        if (strncasecmp($key, 'http://', strlen('http://')) == 0) {
            return $key;
        }

        $key = ltrim($key, '/');
        $hostPart = self::getPictureHost($key);
        if (empty($key)) {
            return $hostPart . '/css/images/noimage.jpg';
        }
        return $hostPart . '/' . $key;
    }

    public static function nginx_userid_decode($str) {
        $str_unpacked = unpack('h*', base64_decode(str_replace(' ', '+', $str)));
        $str_split = str_split(current($str_unpacked), 8);
        $str_map = array_map('strrev', $str_split);
        $str_dedoded = strtoupper(implode('', $str_map));

        return $str_dedoded;
    }

    /**
     * 在/home/work/webdata/logs/目录下记录一个日志
     *
     * @param String $file           日志文件
     * @param String $content        需要记录的日志信息
     * @param String $format         日志文件名称-时间后缀
     * @param bool   $one_file单独一个文件 (不带时间后缀)
     *
     * @example
     * Utilities::addLog('goods_add',"添加商品成功：" . json_encode($goods_info));
     */
    public static function addLog($file, $content, $format = "YmdH", $one_file = false) {
        //记日志
        $logger = new \Framework\Libs\Log\Log(new \Framework\Libs\Log\BasicLogWriter());
        $logger->log($file, $content, $format, $one_file);

        // 日志收集，用于线上稳定性监控
        MonitorManager::collect($file, $content);
    }


    public static function addCollectedLog($content, $module_name = 'goods') {
        self::addLog($module_name . '/log', $content, "YmdH", true);
    }

    /**
     * @param $data
     * @param $module_dir
     */
    public static function addMqLog($head, $data) {
        $log_data = '';
        if (!empty($data) && is_array($data)) {
            foreach ($data as $key => $val) {
                switch ($key) {
                    default:
                        if (is_array($val)) {
                            $val = json_encode($val);
                        }
                        $log_data .= ' ['.$key.':'.$val.'] ';
                }
            }
        }

        if (!empty($log_data)) {
            Utilities::addCollectedLog('['.$head.'] '.$log_data, 'mq');
        }

        // 报警
        if($head != 'message_consume_success') {
            MonitorManager::collect('mq_error', array(
                'message' => $head,
                'data' => $data
            ));
        }
    }

    /**
     * 获取本机PHP机器的IP
     * @return mixed
     */
    public static function getServerPhpIP() {
        static $server_ip;
        if (empty($server_ip)) {
            // 获取服务器本机IP
            exec('/sbin/ifconfig | grep "inet addr" | grep -v "127.0.0.1" | cut -d":" -f2 | awk \'{print $1}\'', $server_ip);
        }

        return $server_ip[0];
    }

    /**
     * 从config目录下获取某配置文件中的某个配置
     *
     * @param String  $file   config目录下的文件名字，如remote
     * @param String  $name   具体配置文件里的某一个key
     * @param Boolean $getAll 是否获取全部配置
     *
     * @return String|Array
     *
     * @example 获取库存系统对应的redis配置
     * Utilities::getConfig('redis','inventory',true);
     */
    public static function getConfig($file, $name, $getAll = false) {
        $config = \Framework\ConfigFilter::instance()->getConfig($file);
        $result = NULL;
        if (isset($config[$name])) {
            $result = $config[$name];
        }
        if ($getAll) {
            return $result;
        } elseif (is_array($result)) {
            return $result[array_rand($result)];
        } else {
            return $result;
        }
    }

    /**
     * 请求远程接口
     *
     * @param String $env     接口所在域名，如：virus、doota
     * @param String $api     接口具体地址，如：brdgoods/goods_list
     * @param Array  $params  请求接口所需参数
     * @param Array  $headers 可以自行制定headers，如：array('Host:virus.meilishuo.com')
     *
     * @return string
     */
    public static function postByRemoteConfig($env, $api, $params = array (), $headers = array (), $format = true) {
        $result = self::multiPostByRemoteConfig(array (
            array (
                'env'      => $env,
                'api'      => $api,
                'param'    => $params,
                'callback' => 'callback',
                'header'   => $headers,
                'format'   => $format,
            )
        ));

        return $result['callback'];
    }

    /**
     * 批量发Post请求
     *
     * @param array $params
     *
     * @return array|bool
     */
    public static function multiPostByRemoteConfig($params = array ()) {
        if (!empty($params)) {
            $client = new \Framework\Libs\Serviceclient\MultiClient();
            $callbacks = array ();
            foreach ($params as $item) {
                $client->call($item['env'], $item['api'], $item['param'], $item['callback'], array (
                    'method' => 'POST',
                    'header' => $item['header']
                ));
                $callbacks[$item['callback']] = !isset($item['format']) ? true : $item['format'];
            }
            $client->callData();
            $result = array ();
            foreach ($callbacks as $cb => $format) {
                $result[$cb] = $format ? $client->formatClientData($cb) : $client->$cb;
            }

            return $result;
        }

        return false;
    }

    /**
     * 请求远程接口
     *
     * @param String $env    接口所在域名，如：virus、doota
     * @param String $api    接口具体地址，如：brdgoods/goods_list
     * @param Array  $params 请求接口所需参数
     * @param bool   $format 是否默认进行http_code=200的判断
     *
     * @return Array
     *
     * @example 根据shop_id获取店铺信息
     * Utilities::apiRequest('virus','cargo/shop_info',array('shop_id' => 100843));
     */
    public static function apiRequest($env, $api, $params = array (), $format = true, $opt = null) {
        $client = new \Framework\Libs\Serviceclient\MultiClient();
        $client->call($env, $api, $params, 'result_data', $opt);
        $client->callData();
        if ($format) {
            return $client->formatClientData('result_data');
        } else {
            return $client->result_data;
        }
    }

    public static function getRequestPath() {
        $path = '';
        if (isset($_SERVER['REQUEST_URI'])) {
            // extract the path from REQUEST_URI
            $request_path = strtok($_SERVER['REQUEST_URI'], '?');
            $base_path_len = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));

            // unescape and strip $base_path prefix, leaving $path without a leading slash
            $path = substr(urldecode($request_path), $base_path_len + 1);

            // $request_path is "/" on root page and $path is FALSE in this case
            if ($path === false) {
                $path = '';
            }

            // if the path equals the script filename, either because 'index.php' was
            // explicitly provided in the URL, or because the server added it to
            // $_SERVER['REQUEST_URI'] even when it wasn't provided in the URL (some
            // versions of Microsoft IIS do this), the front page should be served
            if ($path == basename($_SERVER['PHP_SELF'])) {
                $path = '';
            }
        }

        return $path;
    }

    public static function isHaitaoShop($shop_id) {
        if (empty($shop_id) || is_numeric($shop_id)) {
            return false;
        }

        return $shop_id == 150347 ? true : false;
    }

    /**
     * 获取当前集群的名称
     */
    public static function getClusterName() {
        $clusterInfo = \Framework\MlserviceConfig::load('Cluster');

        return $clusterInfo['name'];
    }

    public static function getClusterInfo($key = null) {
        $clusterInfo = \Framework\MlserviceConfig::load('Cluster');

        return empty($key) ? $clusterInfo: $clusterInfo[$key];
    }

    /**
     * 获取线上所有的集群名称
     * @return array
     */
    public static function getOnlineClusters() {
        return array (
            'dfz',
            'yz',
            'syq',
            'qxg'
        );
    }

    /**
     * 判断当前集群是否为线上集群
     * @return bool
     */
    public static function isOnlineCluster() {
        $clusters = array_flip(self::getOnlineClusters());

        return isset($clusters[self::getClusterName()]);
    }


    /**
     * @purpose 删除远程集群数据(goods用)
     *
     * @param   string /array  $postFields    缓存的key
     *
     * @return  bool    $del_flag   删除是否成功
     */
    public static function delRemoteData($postFields) {
        if (empty($postFields)) {
            return false;
        }

        try {
            $params = array ();
            $cluster_config = array (
                'goodsdfz',
                'goodsyz',
                'goodsqxg',
            );
            foreach ($cluster_config as $env) {
                $params[] = array (
                    'env'      => $env,
                    'api'      => 'cache/clean_cache',
                    'param'    => $postFields,
                    'callback' => $env,
                    'header'   => array (
                        //'Host' => $_SERVER['SERVER_NAME']
                    )
                );
            }
            $result = self::multiPostByRemoteConfig($params);
            foreach ($result as $r => $t) {
                if (!$t) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            self::addCollectedLog('delRemoteData:' . $e->getMessage());

            return false;
        }

        return true;
    }

    public static function getMultiData($post_data, $api = 'api/get_data', $header = array(), $cluster_config = array('goodsdfz', 'goodsyz', 'goodsqxg')) {
        if (empty($post_data)) {
            return false;
        }

        try {
            $params = array();
            foreach ($cluster_config as $env) {
                $params[] = array(
                    'env' => $env,
                    'api' => $api,
                    'param' => $post_data,
                    'callback' => $env,
                    'header' => $header,
                );
            }

            return self::multiPostByRemoteConfig($params);
        } catch (\Exception $e) {
            self::addCollectedLog('getMultiData:' . $e->getMessage());

            return false;
        }
    }

    /**
     * 获取线上脚本及ip
     *
     * @return array
     */
    public static function getScriptIp() {
        return array (
            '10.5.1.47',
            '10.5.12.74',
        );
    }

    /**
     * 将字符创转换成数组
     * @param $str
     * @param $splits
     * @param int $level
     * @return array|bool
     */
    public static function transferStrBySplit($str, $splits, $level = 0) {
        $next_level     = $level + 1;
        $split_key      = $splits[$level];
        $split          = self::_getSplitInfo($split_key);
        $target_info    = array();
        try{
            if ($split['type'] == 'arr') {
                $res = explode($split_key, $str);
                if ($next_level < count($splits)) {
                    foreach($res as $index => $item) {
                        $trans_res = self::transferStrBySplit($item, $splits, $next_level);
                        if ($trans_res === false) {
                            continue;
                        }
                        if (isset($trans_res['_key'])) {
                            if (!empty($trans_res['_key'])) {
                                $target_info[$trans_res['_key']] = $trans_res['_value'];
                            }
                        } else {
                            $target_info[$index] = $trans_res;
                        }
                    }
                } else {
                    $target_info = $res;
                }
            } else {
                $res = explode($split_key, $str, 2);
                $target_info = array(
                    '_key'   => $res[0],
                    '_value' => $res[1],
                );

                if ($next_level < count($splits)) {
                    $trans_res = self::transferStrBySplit($res[1], $splits, $next_level);
                    if ($trans_res === false) {
                        return false;
                    }
                    $target_info['_value'] = $trans_res;
                }
            }
        } catch(\Exception $e) {
            return false;
        }
        return $target_info;
    }

    private static function _getSplitInfo($split_key) {
        $split_key_info = explode('-', $split_key);
        if (count($split_key_info) == 2) {
            return array(
                'split' => $split_key_info[0],
                'type' => $split_key_info[1]
            );
        }
        foreach(self::$default_split_arr as $split_item) {
            if ($split_item['split'] == $split_key) {
                return $split_item;
            }
        }
        return null;
    }

    /**
     * 获取程序的调用栈，方便问题定位
     * @return string
     */
    public static function getStackTrace(){
        $data = array();
        $trace = debug_backtrace();
        foreach($trace as $index => $item) {
            $data[] = '#' . $index . ' ' . $item['file'] . '(' . $item['line'] . '): ' . $item['class'] . $item['type'] . $item['function']
                . '(' . substr(json_encode($item['args'],true),0,80) . ')';
        }
        return implode("\n",$data);
    }

    /**
     * 调用libs/mail/Helper发送邮件
     *
     * @param $receiver    string      收件人列表，非空,eg:"abc@meilishuo.com,cba@meilishuo.com"
     * @param $subject     string      邮件标题 ，非空
     * @param $body        string      邮件信体, 支持html格式, 非空
     * @param $cc          array       抄送人列表, 默认为空
     * @param $attachment  array       附件列表, 默认为空
     * @return  bool 发送状态|true表示成功；false表示失败
     */
    public static function sendMail($receiver, $subject, $body, $cc = array(), $attachment = array()){
        $body = date("Y-m-d H:i:s", time()).$body;
        return Helper::sendMail($receiver,$subject,$body,$cc,$attachment);
    }
}
