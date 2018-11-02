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

class FullSubtraction
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
        Validator::make($this->rule,[
            'full'          =>  'required|numeric',
            'subtract'      =>  'required|numeric',
        ])->validate();
        if($this->rule['full'] < $this->rule['subtract']){
            throw new PromotionException(trans('promotion.validations.full_must_great_subtract'));
        }
    }

    public function calculate(){
        if($this->promotion->getGoodsAmount() >= $this->rule['full']){
            $this->promotion->setPromotion( round($this->rule['subtract'],2));
            $this->promotion->setPromotionAmount($this->promotion->getGoodsAmount()-$this->promotion->getPromotion());
        }else{
            $this->promotion->setPromotion(0);
            $this->promotion->setPromotionAmount($this->promotion->getGoodsAmount());
        }
        if($this->promotion->getPromotion() > 0){
            $rate                                       =   $this->promotion->getPromotionAmount()/$this->promotion->getGoodsAmount();
            foreach ($this->promotion->getProducts() as $key=>$value){
                if($value->is_check){
                    $value->final_price                     =   $rate*$value->price;
                }

                $this->promotion->putProduct($key,$value);
            }
        }
        return $this->promotion;
    }
}