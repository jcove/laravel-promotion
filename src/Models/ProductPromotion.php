<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/13
 * Time: 16:46
 */
namespace Jcove\Promotion\Models;
use Illuminate\Database\Eloquent\Model;

class ProductPromotion extends Model
{
    protected $fillable  = ['product_id','product_name','price','start_time','end_time','type','promotion_id'];
}