<?php
/*
 * 生成随机字符串
 * @param int $length 生成随机字符串的长度
 * @param string $char 组成随机字符串的字符串
 * @return string $string 生成的随机字符串
 */
 function str_rand($length = 32, $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    if(!is_int($length) || $length < 0) {
        return false;
    }

    $string = '';
    for($i = $length; $i > 0; $i--) {
        $string .= $char[mt_rand(0, strlen($char) - 1)];
    }

    return $string;
}

function str_limit($text,$len=15){
     if(!$text)
         return '';
     if(mb_strlen($text)>$len)
         return mb_substr($text,0,$len).'...';
     return $text;
}

function floor2($number, $s = 2)
{
    $n = pow(10, $s);
    return sprintf("%.".$s."f", floor($number * $n) / $n);
}
