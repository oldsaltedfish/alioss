<?php
/**
 * Created by PhpStorm.
 * User: wyh
 * Date: 2019/5/28
 * Time: 9:56
 */

namespace Wuliaowyh\AliOss;

use Illuminate\Support\Facades\Cache;
use OSS\Core\OssException;
use OSS\OssClient;

class Oss extends OssClient
{
    protected $config;
    public function __construct(string $configName = 'default')
    {

        $config = config('alioss.'.$configName);
        if(empty($config)){
            throw new OssException('can not found ali oss config '.$configName);
        }
        $config['prefix'] = trim($config['prefix'], '\/');
        $config['dir'] = trim($config['dir'], '/');
        $config['object'] = trim($config['object'], '/');
        $this->config = $config;
        parent::__construct(
            $config['access_key_id'],
            $config['access_key_secret'],
            $config['endpoint'],
            $config['is_cname'],
            $config['security_token'],
            $config['request_proxy']
        );
    }

    protected function getDir($filename = '', $fileMd5=''){
        $dir = $this->config['dir'];
        if($this->config['prefix']){
            $dir = $this->config['prefix'].'/'.$dir;
        }
        if($dir) {
            $dir = $this->replaceVar($dir, $filename, $fileMd5);
        }
        return $dir;
    }

    protected function getObjectStr($filename = '', $fileMd5 = '')
    {
        $dir = $this->getDir($filename, $fileMd5);
        $object = $this->config['object'];
        if($dir){
            $object = $dir.'/'.$object;
        }
        return $object;
    }

    protected function getCachePrefix(){
        return config('cache_prefix').$this->config['access_key_id'];
    }

    protected function getCount(){
        $key = $this->getCachePrefix();
        $count = Cache::increment($key);
        if($count > 99999){
            $count = 1;
            Cache::put($key, $count, $this->config['cache_prefix']);
        }
        return $count;
    }

    protected function replaceVar(string $subject, $fileName = '', $fileMd5 = '')
    {
        $suffix = empty($fileName)? '' : '.'.substr($fileName, stripos($fileName, '.') + 1);
        $micro = preg_match('/\.([\d]+\\s)/', microtime(), $matches);
        $search = [
            '{year}',
            '{mon}',
            '{month}',
            '{day}',
            '{hour}',
            '{min}',
            '{sec}',
            '{micro}',
            '{count}',
            '{fileMd5}',
            '{fileName}',
            '{suffix}'
        ];
        $replace = [
            date("Y"),
            date("m"),
            date("m"),
            date("d"),
            date("H"),
            date("i"),
            date("s"),
            $micro,
            $this->getCount(),
            $fileMd5,
            $fileName,
            $suffix
        ];
        return ltrim(str_replace($search, $replace, $subject), '/');
    }

    protected function gmtIso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

    /**
     * @param array $option
     * @return array
     * @throws \Exception
     */
    public function policy()
    {
        $dir = $this->getDir();
        if(!empty($this->config['callback_url'])){
            $callback_param = [
                'callbackUrl'=> $this->config['callback_url'],
                'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
                'callbackBodyType'=>"application/x-www-form-urlencoded"
            ];
            $callback_string = json_encode($callback_param);
            $base64_callback_body = base64_encode($callback_string);
        }

        //设置过期时间
        $endTime =  $this->config['policy_expire'] + time();
        $expiration = $this->gmtIso8601($endTime);

        //最大文件大小.用户可以自己设置
        $condition = [
            0=>'content-length-range', 1=>0,
            2=> $this->config['max_size'],
        ];
        $conditions[] = $condition;


        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = array(0=>'starts-with', 1=>'$key', 2=>$dir);
        $conditions[] = $start;

        $policy = json_encode(['expiration'=>$expiration,'conditions'=>$conditions]);

        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->config['access_key_secret'], true));
        $host = 'https://'.$this->config['bucket'].'.'.$this->config['endpoint'];

        $response = [];
        $response['accessid'] = $this->config['access_key_id'];
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $endTime;
        if(!empty($base64_callback_body)){
            $response['callback'] = $base64_callback_body;
        }
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        return $response;
    }
}