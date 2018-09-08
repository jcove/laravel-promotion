<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/13
 * Time: 15:56
 */

namespace Jcove\Promotion;


use Carbon\Carbon;
use DateTime;
use Illuminate\Config\Repository;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Jcove\Promotion\Exceptions\PromotionException;
use Jcove\Promotion\Models\ProductPromotion;
use Jcove\Promotion\Models\Promotion;


class PromotionMain
{

    /**
     * @var \Jcove\Promotion\Models\Promotion
     */
    private $promotion;


    public function __construct(SessionManager $session, Repository $config)
    {
        $this->session = $session;
        $this->config = $config;
    }


    /**
     * register promotions
     * @param $products \Illuminate\Support\Collection
     * @param $promotion \Jcove\Promotion\Models\Promotion
     * @return mixed
     */
    public function register($products,$promotionId){
        $this->validatePromotion($promotionId);
        $array                                      =   [];
        if($products && count($products)){
            foreach ($products as $product){
                $productPromotion                   =   new ProductPromotion();
                $productPromotion->product_id       =   $product['product_id'];
                $productPromotion->product_name     =   $product['product_name'];
                $productPromotion->start_time       =   $this->promotion->start_time;
                $productPromotion->end_time         =   $this->promotion->end_time;
                $productPromotion->type             =   $this->promotion->type;
                $productPromotion->promotion_id     =   $promotionId;
                $productPromotion->save();
                $array[]                            =   $productPromotion;
            }
        }
        return $array;
    }

    /**
     * get products promotions
     * @param $products \Illuminate\Support\Collection
     * @throws
     * @return array
     */
    public function products($products){
        if(is_array($products)){
            if($products && count($products)){
                $in                                     =   [];
                $originalProducts                       =   [];
                foreach ($products as $product){

                    $product                            =   new Product($product);

                    $in[]                               =   $product->id;
                    $originalProducts[$product->id]     =   $product;

                }
                $promotions                             =   [];
                $productPromotions                      =   ProductPromotion::whereIn('product_id',$in)->where('start_time','<=',new Carbon())->get();

                if($productPromotions && count($productPromotions) > 0){
                    $promotionIds                       =   [];

                    foreach ($productPromotions as $productPromotion){
                        if(!in_array($productPromotion->promotion_id,$promotionIds)){
                            $promotionIds[]             =   $productPromotion->promotion_id;
                            $promotions[$productPromotion->promotion_id]    =   Promotion::FindOrFail($productPromotion->promotion_id);
                        }
                        $promotions[$productPromotion->promotion_id]->pushProduct($originalProducts[$productPromotion->product_id]);
                        unset($originalProducts[$productPromotion->product_id]);
                    }
                }
                if(count($originalProducts) > 0){
                    $nothingPromotions              =   new Promotion();
                    $nothingPromotions->name        =   trans('promotion.nothing');
                    $nothingPromotions->type        =   'nothing';
                    $nothingPromotions->id          =   0;
                    foreach ($originalProducts as $originalProduct){
                        $nothingPromotions->pushProduct($originalProduct);
                    }
                    $promotions['nothing']           =   $nothingPromotions;
                }
                $array                              =   [];
                foreach ($promotions as $promotion){

                    $class                              =   '\Jcove\Promotion\Promotions\\'.ucwords($promotion->type);
                    if(!class_exists($class)){
                        throw new PromotionException(trans('promotion.validation.class_not_exist'));
                    }
                    $array[]                         =  (new $class($promotion))->calculate();


                }
                return $array;
            }
        }
        return [];
    }

    /**
     * @param $product
     * @return mixed
     * @throws PromotionException
     */
    public function product($product){
        $temp                                   =   new Product($product);
        $product                                =   new Product($product);

        $productPromotion                       =   ProductPromotion::where('product_id',$product->id)->where('start_time','<=',new Carbon())->firstOrFail();
        if($productPromotion){
            $promotion                          =   Promotion::getEnable($productPromotion->promotion_id);
            $class                              =   '\Jcove\Promotion\Promotions\\'.ucwords($promotion->type);
            if(!class_exists($class)){
                throw new PromotionException(trans('promotion.validation.class_not_exist'));
            }
            $promotion->pushProduct($product);
            $temp->promotion                 =   (new $class($promotion))->calculate();
        }
        return $temp;
    }

    /**
     * @param $promotion \Jcove\Promotion\Models\Promotion
     * @throws
     */
    protected function validatePromotion($promotionId){
        $this->promotion                    =   Promotion::where('enable',Promotion::ENABLE)->FindOrFail($promotionId);
        if($this->promotion && $this->promotion->enable == Promotion::DISABLE){
            throw new PromotionException(trans('promotion.promotion_disabled'));
        }
    }

    public function promotions($enable = Promotion::ENABLE){
        $where                              =   [];
        if($enable !=null ){
            $where['enable']                =   $enable;
        }
        $rows                               =   request()->per_page ? request()->per_page : $this->config['promotion.per_page'];
        return Promotion::where($where)->paginate($rows);
    }

    /**
     * @param Promotion|null $promotion
     * @throws PromotionException
     * @return mixed
     */
    public function create(Promotion $promotion = null){
        $this->validate($promotion);
        $this->promotion->save();
        return $this->promotion;
    }

    /**
     * @param Promotion|null $promotion
     * @throws PromotionException
     */
    public function validate(Promotion $promotion = null){
        $data                               =   [];
        if (null != $promotion ){
            if(!($promotion instanceof Promotion)){
                throw new PromotionException(trans('promotion.validations.parameter_must_promotion',['attribute'=>Promotion::class]));
            }
            $data['name']                   =   $promotion->name;
            $data['description']            =   $promotion->description;
            $data['start_time']             =   $promotion->start_time;
            $data['end_time']               =   $promotion->end_time;
            $data['type']                   =   $promotion->type;
            $data['rule']                   =   $promotion->rule;
        }else{
            $promotion                      =   new \Jcove\Promotion\Models\Promotion();

            $data                           =   request()->all();
        }
        $types                              =   $this->config['promotion.types'];
        $validator                          =   Validator::make($data,[
            'name'                          =>  'required',
            'start_time'                    =>  'required',
            'end_time'                      =>  'required',
            'type'                          =>  [
                'required',
                Rule::in($types)
            ],
            'rule'                          =>  'required|json'
        ]);
        //验证
        $validator->validate();
        //验证时间
        if(!$this->validateDate($data['start_time']) || !$this->validateDate($data['end_time'])){
            throw new PromotionException(trans('promotion.validations.datetime'));
        }
        $startTime                          =   new Carbon($data['start_time']);
        $endTime                            =   new Carbon($data['end_time']);
        $now                                =   new Carbon();
        if($startTime->lt($now)){
            throw new PromotionException(trans('promotion.validations.start_time_must_egt_now'));
        }
        if($startTime->gte($endTime)){
            throw new PromotionException(trans('promotion.validations.end_time_must_gt_start_time'));
        }
        //验证规则
        $promotion->name                    =   $data['name'];
        $promotion->description                =   $data['description'];
        $promotion->start_time              =   $data['start_time'];
        $promotion->end_time                =   $data['end_time'];
        $promotion->type                    =   $data['type'];
        $promotion->rule                    =   $data['rule'];
        $class                              =   '\Jcove\Promotion\Promotions\\'.ucwords($data['type']);
        if(!class_exists($class)){
            throw new PromotionException(trans('promotion.validation.class_not_exist'));
        }
        $promotionType                      =   new $class($promotion);
        $promotionType->validateRule($data['rule']);

        $this->promotion                    =   $promotion;

    }
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public function routes(){
        $attributes = [
            'prefix'        => config('promotion.route.prefix'),
            'namespace'     => 'Jcove\Promotion\Controllers',
        ];

        Route::group($attributes, function ($router) {

            /* @var \Illuminate\Routing\Router $router */
            $router->group([], function ($router) {

                $router->post('promotion/register','PromotionController@register');
                $router->post('promotion/product','PromotionController@product');
                $router->post('promotion/products','PromotionController@products');
                $router->resource('promotion', 'PromotionController');


            });

        });
    }
}