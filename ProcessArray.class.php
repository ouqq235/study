<?php
namespace Framework\Libs\Util\Common;
/**
 * @purpose 处理 array 类库
 *
 * @author: yongfeimiao
 *
 * Date: 2015-07-03
 */
class ProcessArray {
    /**
     * @purpose 把数组，转化成key=>value的数组
     *
     * @param   array   $data
     * @param   string  $key etc. 'goods_id'
     * @param   string  $value  etc. 'goods_status'
     * @param   int     $ignore_type 当有重复key值 1:忽略以后的值;2:保留最后的值;3:都保留[数组维度 + 1]
     *
     * @result  array   $ret    结果数据
     */
    public static function parseArrayToKeyValue($data, $key, $value='', $ignore_type=1) {
        if (!is_array($data) || empty($data)) {
            return $data;
        }

        $ret = array();
        $i = 1;
        foreach ($data as $item) {
            if (isset($item[$key])) {
                if (isset($ret[$item[$key]])) {
                    switch ($ignore_type) {
                        case 1:
                            break;
                        case 2:
                            if (empty($value)) {
                                $ret[$item[$key]] = $item;
                            }
                            else {
                                if (isset($item[$value])) {
                                    $ret[$item[$key]] = $item[$value];
                                }
                            }
                            break;
                        case 3:
                            if (empty($value)) {
                                if ($i == 1) {
                                    $tmp = $ret[$item[$key]];
                                    unset($ret[$item[$key]]);
                                    $ret[$item[$key]] = array(
                                        $tmp,
                                        $item,
                                    );
                                }
                                else {
                                    $ret[$item[$key]][] = $item;
                                }
                            }
                            else {
                                if (isset($item[$value])) {
                                    $tmp = $ret[$item[$key]];
                                    $ret[$item[$key]] = array(
                                        $tmp,
                                        $item[$value],
                                    );
                                }
                                else {
                                    $ret[$item[$key]][] = $item[$value];
                                }
                            }
                            $i++;
                            break;
                        case 4:
                            if (empty($value)) {
                                $ret[$item[$key]][] = $item;
                            }
                            else {
                                if (isset($item[$value])) {
                                    $ret[$item[$key]][] = $item[$value];
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
                else {
                    if (empty($value)) {
                        $ret[$item[$key]] = $item;
                    }
                    else {
                        if (isset($item[$value])) {
                            $ret[$item[$key]] = $item[$value];
                        }
                    }

                    if (isset($ret[$item[$key]]) && $ignore_type == 4) {
                        $tmp = $ret[$item[$key]];
                        unset($ret[$item[$key]]);
                        $ret[$item[$key]] = array(
                            $tmp
                        );
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * @purpose 获得二维数组指定key的值
     *
     * @param   array   $arr    待处理的二维数组
     * @param   string  $key    待取出的key
     * @param   bool    $unique 默认为true:表示结果数组数据不重复; false:表示不做处理
     *
     * @return  array   $ret    获得的一维数组
     */
    public static function getValueByKey($arr, $key, $unique=true) {
        if (!is_array($arr) || empty($arr)) {
            return $arr;
        }

        foreach ($arr as $item) {
            var_dump();
            $ret[] = $item[$key];
        }

        return $unique ? array_values(array_unique($ret)) : $ret;
    }

    /**
     * @purpose 对二维数组按照指定key的一维数组进行排序(类似MySQL的order by)
     *
     * @param   array   $arr            待处理的二维数组
     * @param   string  $sort_by_key    待取出的key
     * @param   string  $sort_order     排序顺序
     * @param   string  $sort_type      排序类型
     *
     * @return  array   $arr            按要求排序之后的二维数组
     */
    public static function sortArray($arr, $sort_by_key, $sort_order='ASC', $sort_type='REGULAR') {
        if (!is_array($arr) || empty($arr)) {
            return $arr;
        }

        $sort_order = strtoupper($sort_order) == 'ASC' ? SORT_ASC : SORT_DESC;
        $sort_type  = strtoupper($sort_type);
        switch ($sort_type) {
            case 'REGULAR':
                $sort_type = SORT_REGULAR;
                break;
            case 'NUMERIC':
                $sort_type = SORT_NUMERIC;
                break;
            case 'STRING':
                $sort_type = SORT_STRING;
                break;
            default:
                $sort_type = SORT_REGULAR;
                break;
        }

        $sort_by_arr = array();
        foreach ($arr as $k => $v) {
            $sort_by_arr[$k] = $v[$sort_by_key];
        }
        array_multisort($sort_by_arr, $sort_order, $sort_type, $arr);

        return $arr;
    }

    /**
     * @purpose 把一维数组的数据强制转化为每一个value是int型的一维数组[适用场景:goods_id,twitter_id等的数组]
     * @param array $arr
     * @param int $int_type
     *
     * @return array
     */
    public static function conventToIntArr($arr, $int_type=1) {
        if (!is_array($arr) || empty($arr)) {
            return $arr;
        }

        $ret = array();
        foreach ($arr as $val) {
            if (is_numeric($val) && is_int($val * 1)) {
                switch ($int_type) {
                    case 1://正整数
                        if ($val > 0) {
                            $ret[] = intval($val);
                        }
                        break;
                    case 2://非正整数=负整数+0
                        if ($val <= 0) {
                            $ret[] = intval($val);
                        }
                        break;
                    case 3://负整数
                        if ($val < 0) {
                            $ret[] = intval($val);
                        }
                        break;
                    case 4://非负整数=正整数+0
                        if ($val >= 0) {
                            $ret[] = intval($val);
                        }
                        break;
                    default:
                        $ret[] = intval($val);
                }
            }
        }

        return $ret;
    }

    /**
     * 数组递归合并(覆盖方式, 非array_merge_recursive的追加方式)
     *
     * @param array $arr1   数组一
     * @param array $arr2   数组二
     * @param array ...     数组...
     * @return array
     */
    public static function arrayCoverRecursive(array $arr1,array $arr2){
        $rs = $arr1;
        foreach(func_get_args() as $arr) {
            if(!is_array($arr)) {
                return array();
            }
            foreach($arr as $key=>$val) {
                $rs[$key] = isset($rs[$key]) ? $rs[$key] : array();
                $rs[$key] = is_array($val) ? self::arrayCoverRecursive($rs[$key], $val) : $val;
            }
        }
        return $rs;
    }

    /**
     * 返回array中制定的key的值
     * @param $arr
     * @param $arr_keys
     *
     * @return array
     */
    public static function arrayKeyFilter($arr, $arr_keys) {
        $res = array();
        foreach($arr_keys as $arr_key) {
            if (array_key_exists($arr_key, $arr)) {
                $res[$arr_key] = $arr[$arr_key];
            }
        }
        return $res;
    }
}
