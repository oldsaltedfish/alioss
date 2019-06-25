<?php
/**
 * Created by PhpStorm.
 * User: wyh
 * Date: 2019/5/28
 * Time: 15:14
 */

class TestCase extends Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([\Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class]);
        $app['config']->set('alioss.default',[
            'prefix' => env('ALI_OSS_PREFIX', 'dev'),// 路径前缀，可用于区分不同环境
            'dir' => env('ALI_OSS_DEFAULT_DIR', '{year}{month}/{day}'),//保存文件目录路径
            'filename' => env('ALI_OSS_DEFAULT_FILE_NAME', '{hour}{min}{sec}{count}{suffix}'),
            'access_key_id' => env('ALI_OSS_ACCESS_KEY_ID'),
            'access_key_secret' => env('ALI_OSS_ACCESS_KEY_SECRET'),
            'bucket' => env('ALI_OSS_BUCKET'),
            'endpoint' => env('ALI_OSS_ENDPOINT'),
            'is_cname' => env('ALI_OSS_IS_CNAME', false),
            'security_token' => env('ALI_OSS_SECURITY_TOKEN', NULL),
            'request_proxy' => env('ALI_OSS_REQUEST_PROXY', NULL),
            'policy_expire' => env('ALI_OSS_POLICY_EXPIRE', 600),// policy 过期时间
            'cache_prefix' => env('ALI_OSS_CACHE_PREFIX', 'ali_oss_'),// policy 过期时间
            'max_size' => env('ALI_OSS_MAX_SIZE', 20971520),// policy 最大值
        ]);
        parent::getEnvironmentSetUp($app);
    }

    protected function getPackageProviders($app)
    {
        return ['Wuliaowyh\AliOss\OssServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Oss' => 'Wuliaowyh\AliOss\Facades\Oss'
        ];
    }

    /**
     *  @test
     *  @expectException        Exception
     */
    public function policy()
    {
        $policy = Oss::policy('image.png');
        $this->assertIsArray($policy);
        $this->assertArrayHasKey('policy', $policy);
        $this->assertIsString($policy['policy']);
    }
    /**
     *  @test
     */
    public function test(){
        $obj = 'test';
        $excepted = 'https://'.config('alioss.default.bucket').'.'.config('alioss.default.endpoint').'/'.$obj;
        $this->assertEquals($excepted, Oss::url($obj));
    }

    /**
     *  @test
     */
    public function testSaveObjTo(){
        $path = 'test_file';
        Oss::saveObjTo('201812/15450116053nyZD.png', $path);
        if(file_exists($path)){}
        $this->assertIsBool((file_exists($path)));
        unlink($path);
    }

    public function testPut()
    {
        $obj = 'test_content';
        $ossObj = Oss::put($obj);

        $this->assertTrue($obj === Oss::get($ossObj));
        Oss::del($ossObj);
        try{
            Oss::get($ossObj);
        }catch (Exception $e){
            $this->assertIsInt(strpos($e->getMessage(), 'NoSuchKey'));
        }
    }

    public function testUpload()
    {
        $ossObj = Oss::upload('testfile');
        Oss::saveObjTo($ossObj, 'dowlandTestfile');
        $this->assertTrue(file_exists('dowlandTestfile'));
        Oss::del($ossObj);
        try{
            Oss::get($ossObj);
        }catch (Exception $e){
            $this->assertIsInt(strpos($e->getMessage(), 'NoSuchKey'));
        }
        unlink('dowlandTestfile');
    }
}