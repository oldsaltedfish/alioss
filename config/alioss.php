<?php
/**
 * Created by PhpStorm.
 * User: wyh
 * Date: 2019/6/6
 * Time: 13:25
 * prefix、dir、filename支持变量、上传文件时会自动替换
 * {year} 年、{month} 月、{day} 日、{hour} 小时、{min} 分、{sec} 秒、{micro} microtime()微秒小数点后数字
 * {fileMd5}文件名md5字串、{fileName} 原文件名，如果是web端直传，必须在调用policy接口时传文件名这两个字段才有效
 * {count}循环自增计数器，范围00000~99999，缓存时效1小时
 */
return [

    'default' =>[
        'prefix' => env('ALI_OSS_PREFIX', 'dev'),// 路径前缀，可用于区分不同环境
        'dir' => env('ALI_OSS_DEFAULT_DIR', '{year}{month}{day}'),//保存文件目录路径
        'object' => env('ALI_OSS_DEFAULT_OBJECT', '{sec}{count}{suffix}'),//自动生成oss对象名，如果在上传文件过程中未传filename，Content-Disposition中attachment为object
        'access_key_id' => env('ALI_OSS_ACCESS_KEY_ID'),
        'access_key_secret' => env('ALI_OSS_ACCESS_KEY_SECRET'),
        'bucket' => env('ALI_OSS_BUCKET'),
        'endpoint' => env('ALI_OSS_ENDPOINT'),
        'is_cname' => env('ALI_OSS_IS_CNAME', false),
        'security_token' => env('ALI_OSS_SECURITY_TOKEN', NULL),
        'request_proxy' => env('ALI_OSS_REQUEST_PROXY', NULL),
        'policy_expire' => env('ALI_OSS_POLICY_EXPIRE', 600),// policy 过期时间
        'cache_prefix' => env('ALI_OSS_CACHE_PREFIX', 'ali_oss_'),// policy 过期时间
        'callback_url' => env('ALI_OSS_CALLBACK_URL'),// policy 过期时间
        'max_size' => env('ALI_OSS_MAX_SIZE', 20971520),// policy 最大值

    ]
];