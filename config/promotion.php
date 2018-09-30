<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/13
 * Time: 17:32
 */
return [
    'single_query_limit'            =>  env('SINGLE_QUERY_LIMIT',100), //单次最多可查促销商品个数
    'types'                         =>  [
        'fullSubtraction',
    ],
    'route'                         =>  [
        'prefix'                    =>  'shop'
    ]
];