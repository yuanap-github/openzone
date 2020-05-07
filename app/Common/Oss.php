<?php
namespace App\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use OSS\OssClient;
use OSS\Core\OssException;
use Illuminate\Support\Facades\Cache;
/**
 * 阿里云OSS对象存储类
 */
class Oss extends Controller {

  static public function uplodeoss ($filePath, $filename) {
   // 阿里云主账号AccessKey拥有所有API的访问权限，风险很高。强烈建议您创建并使用RAM账号进行API访问或日常运维，请登录 https://ram.console.aliyun.com 创建RAM账号。
  $accessKeyId = config('config.ossaccessKeyId');
  $accessKeySecret = config('config.ossaccessKeySecret');
  // Endpoint以杭州为例，其它Region请按实际情况填写。
  $endpoint = config('config.endpoint');
  // 存储空间名称
  $bucket= config('config.bucket');
  // 文件名称
  $object = $filename;
  // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
  $filePath = $filePath;

  try{
      $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
      $result= $ossClient->uploadFile($bucket, $object, $filePath);
  } catch(OssException $e) {
      // printf(__FUNCTION__ . ": FAILED\n");
      // printf($e->getMessage() . "\n");
      return false;
  }
    return $result['info']['http_code'] == 200 ? str_replace("http", "https", $result['info']['url']):false;
    }

}
