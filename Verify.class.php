<?php
namespace Framework\Libs\Util\Common;
/**
 * @purpose 认证、校验相关 方法类库
 *
 * @author: yongfeimiao
 *
 * Date: 2015-06-19
 */
use \Framework\Libs\Util\Constant\ErrorCodes;
class Verify {
    /**
     * @purpose 根据自己配置的验证数据，验证请求参数信息
     *
     * @param array $fields
     * @param array $request
     *
     * 调用示例：
     * private function _init() {
            $result = \Framework\Libs\Util\Common\Verify::verify_request_params($this->_fields, $this->request->REQUEST);

            if ($result['code'] == 0) {
                 $this->_data = $result['data'];
            }
            else {
                 $this->setView($result['code'], $result['msg']);

                 return false;
            }

            return true;
        }
     *
     *例子：private $_fields = array(
        'goods_status' => array(
            'check_type' => 'cannotempty',
            'field_type' => 'in_array',
            'field_value' => array(1,3,5),
        ),
        'goods_ids'=> array(
            'check_type' => 'cannotempty',
            'field_type' => 'ints',
        ),
        'shop_id'=> array(
        ),
    );
     *
     * @result array $result
     */
    public static function verify_request_params($fields, $request) {
        $result = array(
            'code' => 0,
            'msg'  => '',
            'data' => array(),
        );

        if (empty($fields) || !is_array($fields)) {
            $code = ErrorCodes::ERROR_DATA_TYPE;
            $result = array(
                'code' => $code,
                'msg'  => ErrorCodes::$codes[$code],
                'data' => array(),
            );

            return $result;
        }

        if (empty($request) || !is_array($request)) {
            $code = ErrorCodes::ERROR_REQUEST_PARAMS;
            $result = array(
                'code' => $code,
                'msg'  => ErrorCodes::$codes[$code],
                'data' => array(),
            );

            return $result;
        }

        $item_default = array(
            'check_type' => '',
            'field_type' => '',
        );
        foreach ($fields as $field => $item) {
            $param = isset($request[$field]) ? (is_array($request[$field]) ? $request[$field] : trim($request[$field])) : '';

            $item = array_merge($item_default, $item);

            //校验数据
            switch ($item['check_type']) {
                case 'cannotempty':
                    if ($param == '') {
                        $code = ErrorCodes::ERROR_CAN_NOT_BE_EMPTY;
                        $msg  = ErrorCodes::$codes[$code];
                        $result = array(
                            'code' => $code,
                            'msg'  => self::proc_replace($field , $msg),
                            'data' => array(),
                        );

                        return $result;
                    }
                    break;
                default:
                    break;
            }

            //转换格式
            switch ($item['field_type']) {
                case 'int'://非负整数
                    if ($param != '') {
                        if (is_numeric($param) && is_int($param * 1) && $param >= 0) {
                            break;
                        }
                    }
                    $code = ErrorCodes::ERROR_MUST_BE_INT;
                    $msg  = ErrorCodes::$codes[$code];
                    $result = array(
                        'code' => $code,
                        'msg'  => self::proc_replace($field , $msg),
                        'data' => array(),
                    );

                    return $result;
                case 'ints'://一堆非负整数，已英文逗号分隔
                    $param = !empty($param) ? $param : '';
                    //$param_arr = explode(',', $param);
                    if(is_array($param)){
                        $param_arr=$param;
                    }else{
                        $param_arr = !empty($param) ? explode(',', $param) : '';
                    }
                    if (!empty($param_arr)) {
                        foreach ($param_arr as $i => $item) {
                            $param_arr[$i] = trim($item);
                            $item = $param_arr[$i];
                            if (!is_numeric($item) || $item < 0) {
                                $code = ErrorCodes::ERROR_MUST_BE_INT;
                                $msg  = ErrorCodes::$codes[$code];
                                $result = array(
                                    'code' => $code,
                                    'msg'  => self::proc_replace('every '.$field , $msg),
                                    'data' => array(),
                                );

                                return $result;
                            }
                        }
                        $param = $param_arr;
                    }
                    break;
                case 'in_array'://数据有范围,范围就是$item['field_value']
                    if (!in_array($param, $item['field_value'])) {
                        $code = ErrorCodes::ERROR_VALUE;
                        $msg  = ErrorCodes::$codes[$code];
                        $result = array(
                            'code' => $code,
                            'msg'  => self::proc_replace($field , $msg),
                            'data' => array(),
                        );

                        return $result;
                    }
                    break;
                case 'datetime'://2015-07-12 12:23:12
                    $flag = false;
                    if ($param != '') {
                        preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $param, $matches);
                        if (isset($matches[0]) && ($param == $matches[0])) {
                            $flag = true;
                        }
                    }
                    if (!$flag) {
                        $code = ErrorCodes::ERROR_VALUE;
                        $msg  = ErrorCodes::$codes[$code];
                        $result = array(
                            'code' => $code,
                            'msg'  => self::proc_replace($field , $msg),
                            'data' => array(),
                        );

                        return $result;
                    }
                    break;
                default:
                    break;
            }

            $result['data'][$field] = $param;
        }

        return $result;
    }

    public static function proc_replace($placeholder, $msg) {
        $placeholder = (array)$placeholder;
        foreach ($placeholder as $key => $item) {
            $message = str_replace('$' . $key, $item, $msg);
        }

        return $message;
    }

    /**
     * @purpose 处理请求参数信息
     *
     * @param array $fields
     * @param array $request
     *
     * @result array $result
     */
    public static function proc_request_params_value($fields, $request) {
        $result = array(
            'code' => 0,
            'msg'  => '',
            'data' => array(),
        );

        if (empty($fields) || !is_array($fields)) {
            $code = ErrorCodes::ERROR_DATA_TYPE;
            $result = array(
                'code' => $code,
                'msg'  => ErrorCodes::$codes[$code],
                'data' => array(),
            );

            return $result;
        }

        if (empty($request) || !is_array($request)) {
            $code = ErrorCodes::ERROR_REQUEST_PARAMS;
            $result = array(
                'code' => $code,
                'msg'  => ErrorCodes::$codes[$code],
                'data' => array(),
            );

            return $result;
        }

        foreach ($request as $field => $item) {
            if (in_array($field, $fields)) {
                $result['data'][$field] = isset($item) ? (trim($item)) : '';
            }
        }

        if (empty($result['data'])) {
            $code = ErrorCodes::ERROR_REQUEST_PARAMS;
            $result = array(
                'code' => $code,
                'msg'  => ErrorCodes::$codes[$code],
                'data' => array(),
            );

            return $result;
        }

        return $result;
    }
}
