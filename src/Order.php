<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/13
 * Time: 16:03
 */

namespace Jcove\Promotion;


abstract class Order
{
    /**
     * @var
     */
    protected $products;

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



}