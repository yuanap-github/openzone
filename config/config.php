<?php

return [
  /**
   阿里云短信配置
    */
  'AccessKeyId' => '阿里云短信配置AccessKeyId',
  'AccessKeySecret' => '阿里云短信配置AccessKeySecret',
  'RegionId' => '阿里云短信配置RegionId',
  'SignName' => '阿里云短信配置SignName',
  'TemplateCode' => '阿里云短信配置TemplateCode',
   /**
   正常和错误Api返回状态码
    */
   'ErroorCode' => [
    '10001'=>'手机号格式错误!',
    '10002'=>'验证码错误!',
    '10003'=>'身份错误',
    '10004'=>'意外错误',
    '10005'=>'资源不存在',
    '10006'=>'点赞失败',
    '10007'=>'该账号已经被封'
  ],
  /**
 * 阿里云OSS对象存储配置
 */
  'ossaccessKeyId' => '阿里云OSS对象存储配置ossaccessKeyId',
  'ossaccessKeySecret' => '阿里云OSS对象存储配置endpoint',
  'endpoint' => '阿里云OSS对象存储配置endpoint',
  'bucket' => '阿里云OSS对象存储配置bucket',
   /**
   上传临时路径(头像headportraittmp，发布workstmp)
    */
   'headportraittmp' => '上传目录',
   'workstmp' => '上传目录',
   /**
   每次取得条数
    */
  'page' => 10,

]
?>
