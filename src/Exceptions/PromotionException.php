<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/14
 * Time: 10:53
 */

namespace Jcove\Promotion\Exceptions;


class PromotionException extends \Exception
{

    /**
     * PromotionException constructor.
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}