# alioss

laravel阿里云oss扩展，在阿里云官方oss sdk上集成了policy接口，方便前端直传

## 获取policy

``` php
$policy = Oss::policy($phone, $templateCode, $params);
// policy 结构如下
Array
(
    'accessid' => 'ALI_OSS_ACCESS_KEY_ID',
    'url' => 'https://ALI_OSS_BUCKET.ALI_OSS_ENDPOINT',
    'policy' => 'eyJleHBpcmF0aW9uIjoiMjAxOS0wNi0wNlQxMDoxMjozNVoiLCJjb25kaXRpb25zIjpbWyJjb250ZW50LWxlbmd0aC1yYW5nZSIsMCwyMDk3MTUyMF0sWyJzdGFydHMtd2l0aCIsIiRrZXkiLCJkZXZcLzIwMTkwNlwvMDYiXV19',
    'signature' => 'HIijmFun1561CU+i1Ltl/X6tIC8=',
    'expire' => 1559815955,
    'dir' => 'dev/20190606',
);

```
## 获取url

```php
// $object为oss对象名，如 image/a.jpg
$url = Oss::url($object);



```
 调用url方法后$url为：https://ALI_OSS_BUCKET.ALI_OSS_ENDPOINT/image/a.jpg

## 保存oss对象到本地服务器

```php
// $object为oss对象名，如 image/a.jpg; $path为本地服务器路径
$url = Oss::saveObjTo($object, $path);



```

## 其他接口与阿里官方接口一致，可以直接使