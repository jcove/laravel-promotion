<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/14
 * Time: 15:38
 */

namespace Jcove\Promotion\Promotions;


use Illuminate\Support\Facades\Validator;
use Jcove\Promotion\Exceptions\PromotionException;
use Jcove\Promotion\Models\Promotion;

class Nothing
{
    private $promotion;
    private $rule;

    /**
     * FullSubtraction constructor.
     * @param $promotion
     */
    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
        $this->rule();
    }


    public function rule(){
        $this->rule                     =   json_decode($this->promotion->rule,true);
    }

    /**
     * @throws PromotionException
     */
    public function validateRule(){

    }

    public function calculate(){

        $this->promotion->setPromotion(0);
        $this->promotion->setPromotionAmount($this->promotion->getGoodsAmount());
        return $this->promotion;
    }
}