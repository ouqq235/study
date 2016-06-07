<?php

namespace Framework\Libs\Util\Common;

/**
 * @purpose 处理 str 类库
 *
 * @author : haitaoguo
 *        
 *         Date: 2015-07-16
 */
class ProcessStr {
    
    /**
     * @截取小数点后几位
     *
     * @param str $num            
     * @param int $count_after_dot
     *            @result str $result 结果数据
    public static function format_number($num, $count_after_dot = 2) {
        $count_after_dot = (int) $count_after_dot;
        $pow = pow(10, $count_after_dot);
        $tmp = $num * $pow;
        $tmp = floor($tmp) / $pow;
        $format = sprintf('%%.%df', (int) $count_after_dot);
        $result = sprintf($format, (float) $tmp);
        return $result;
    }
    */
    
   public static function format_number($price, $percision) {
        $percision = isset($percision) ? $percision : 2;
        $pow = pow(10, $percision);
        $formatPrice = (floor(bcmul($price, $pow))) / $pow;
        if ($formatPrice <= 0) {
            return $price;
        }
        else {
            $formatPrice=sprintf("%.2f", $formatPrice);
            return $formatPrice;
        }
    }

    /*
     * unicode 转义
     */
    public static function unicode_encode($name) {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2)
        {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0)
            {    // 两个字节的文字
                $str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
            }
            else
            {
                $str .= $c2;
            }
        }
        return $str;
    }

    /*
     * unicode 反转义
     */
    public static function unicode_decode($name)
    {
        // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (!empty($matches))
        {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j++)
            {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0)
                {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code).chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                }
                else
                {
                    $name .= $str;
                }
            }
        }
        return $name;
    }
}
