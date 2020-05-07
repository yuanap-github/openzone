<?php

namespace App\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use Illuminate\Support\Facades\Cache;
/**
 * 阿里云短信类
 */
class Sms extends Controller
{
/**
*单次发送验证码
*/
  static public function sendsms($phone){

    AlibabaCloud::accessKeyClient(config('config.AccessKeyId'), config('config.AccessKeySecret'))->regionId('cn-hangzhou')->asDefaultClient();
    $code_number = RndNumber();
    try {
      $result = AlibabaCloud::rpc()
      ->product('Dysmsapi') // ->scheme('https') // https | http
      ->version('2017-05-25')
      ->action('SendSms')
      ->method('POST')
      ->host('dysmsapi.aliyuncs.com')
      ->options([
          'query' => [
          'RegionId' => config('config.RegionId'),
          'PhoneNumbers' => $phone,
          'SignName' => config('config.SignName'),
          'TemplateCode' => config('config.TemplateCode'),
          'TemplateParam' => "{\"code\":\"{$code_number}\"}",
                     ],
        ])->request();
        $result->code_number = $code_number;
      return $result->toArray();
    } catch (ClientException $e) {
      return ['Message'=>'NO', 'Errorinfo' => $e->getErrorMessage() . PHP_EOL];
    } catch (ServerException $e) {
      return ['Message'=>'NO', 'Errorinfo' => $e->getErrorMessage() . PHP_EOL];
    }
  }
}