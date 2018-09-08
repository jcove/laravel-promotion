<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/16
 * Time: 9:06
 */

namespace Jcove\Promotion\Facades;


use Illuminate\Support\Facades\Facade;

class Promotion extends Facade
{
    public static function getFacadeAccessor(){
        return 'promotion';
    }
}