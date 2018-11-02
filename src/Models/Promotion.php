<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/13
 * Time: 17:03
 */

namespace Jcove\Promotion\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Jcove\Promotion\Product;

class Promotion  extends Model
{
    const ENABLE                    =   1;
    const DISABLE                   =   0;
    /**
     * @var Collection
     */
    protected $products;
    protected $goodsAmount;
    protected $promotionAmount;
    protected $promotion;

    /**
     * @return mixed
     */
    public function getPromotionAmount()
    {
        return $this->promotionAmount;
    }

    /**
     * @param mixed $promotionAmount
     */
    public function setPromotionAmount($promotionAmount)
    {
        $this->promotionAmount = $promotionAmount;
    }

    /**
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param mixed $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }




    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return mixed
     */
    public function getGoodsAmount()
    {
        return $this->goodsAmount;
    }

    /**
     * @param mixed $goodsAmount
     */
    public function setGoodsAmount($goodsAmount)
    {
        $this->goodsAmount = $goodsAmount;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param mixed $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    public function putProduct($key,$product){
        if($this->products==null){
            $this->products                 =   new Collection();
        }
        $this->products->put($key,$product);
    }

    public function pushProduct(Product $product){
        if(null==$this->products){
            $this->products                 =   new Collection();
        }
        $this->products->push($product);

        if(null==$this->goodsAmount){
            $this->goodsAmount                   =   0;
        }
        if($product->is_check){
            $this->goodsAmount                       +=  $product->final_price*$product->num;
        }

    }

    public function toArray(){
        $array                              =   parent::toArray();
        $a['amount']                        =   $this->goodsAmount;
        $a['products']                      =   $this->products;
        $a['promotionAmount']               =   $this->promotionAmount;
        $a['promotion']                     =   $this->promotion;
        return array_merge($a,$array);
    }

    public static function getEnable($id){
        return static ::where(['id'=>$id,'enable'=>Promotion::ENABLE])->where('start_time','<=',new Carbon())->firstOrFail();
    }
}