<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/16
 * Time: 9:42
 */

namespace Jcove\Promotion\Controllers;


use Illuminate\Routing\Controller;
use Jcove\Promotion\Facades\Promotion;
use Jcove\Promotion\Models\ProductPromotion;
use Jcove\Promotion\Requests\RegisterProductPromotion;
use Jcove\Restful\Restful;

class PromotionController extends Controller
{
    use Restful;


    /**
     * PromotionController constructor.
     */
    public function __construct()
    {
        $this->model                        =   new \Jcove\Promotion\Models\Promotion();
    }

    public function index(){
        return Promotion::promotions(request()->all);
    }
    public function search(){
        $q                                  =   request()->q;
        return \Jcove\Promotion\Models\Promotion::where('name','like','%'.$q.'%')->paginate(config('restful.page_max_rows'));
    }
    public function store(){
        return Promotion::create();
    }

    public function register(RegisterProductPromotion $request){
        $products                           =   $request->products;
        $promotionId                        =   $request->promotionId;
        return Promotion::register($products,$promotionId);
    }
    public function products(){
        $products                           =   request()->products;
        return Promotion::products($products);
    }
    public function product(){
        $product                            =   request()->product;
        return Promotion::product($product);
    }
    public function query(){
        $where                              =   [];
        if($promotionId = request()->promotion_id){
            $where['promotion_id']          =   $promotionId;
        }
        if(($enable = request()->enable) !== null){
            $where['enable']                =   $enable;
        }
        if($type = request()->type){
            $where['type']                  =   $type;
        }
        $list                               =   $this->paginate(new ProductPromotion(),$where);
        $this->setData($list);
        return $this->respond($this->data);
    }

    public function deleteProduct($id){
        ProductPromotion::where('id',$id)->delete();
        return $this->success();
    }


}