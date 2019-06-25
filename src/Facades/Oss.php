<?php
/**
 * Created by PhpStorm.
 * User: wyh
 * Date: 2019/5/28
 * Time: 9:56
 */

namespace Wuliaowyh\AliOss\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static void policy()
 * @method static void url(string $object)
 * @method static void saveObjTo(string $object, $path)
 * @method static string put(string $content)
 * @method static string upload($path)
 * @method static string get(string $object)
 * @method static void del(string $object)
 * @see \Wuliaowyh\AliOss\Oss
 */
class Oss extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Wuliaowyh\AliOss\Oss::class;
    }
}