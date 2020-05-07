<?php
 /**
  * 公共方法
 */
//生成随机数(默认1000-9999四位随机数)
  function RndNumber ($start = 1000, $end = 9999) {
    return rand($start,$end);
  }
//验证手机号
  function CheckPhone ($phone_number) {
    $check = '/^(1(([35789][0-9])|(47)))\d{8}$/';
    if (preg_match ($check, $phone_number)) {
      return true;
    } else {
      return false;
    }
  }

