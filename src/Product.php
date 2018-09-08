<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/13
 * Time: 16:02
 */

namespace Jcove\Promotion;




use Illuminate\Contracts\Support\Arrayable;

class Product implements Arrayable
{

    protected $_data;

    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return false;
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }
    /**
     * Product constructor.
     * @param $id
     * @param $name
     * @param $price
     */
    public function __construct($array)
    {
        if(is_string($array)){
            $this->stringToProduct($array);
        }
        if(is_array($array)){
            $this->arrayToProduct($array);
        }

    }



    public function arrayToProduct($array){
        if(count($array)){
            foreach ($array as $key => $value){
                $this->_data[$key]      =   $value;
            }
        }
        if(!isset($this->_data['final_price'])){
            $this->_data['final_price'] = $this->_data['price'] ? : 0;
        }
    }

    public function stringToProduct($string){
        $array                          =   json_decode($string,true);
        $this->arrayToProduct($array);
    }

    public function toArray(){
        $array                          =   [];
        foreach ($this->_data as $key=>$value){
            $array[$key]                =   $value;
        }
        return $array;
    }





}