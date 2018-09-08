<?php
/**
 * Author: XiaoFei Zhai
 * Date: 2018/7/14
 * Time: 9:21
 */
return [
    'nothing'                       =>  '无',
    'promotion_disabled'            =>  '促销规则已过期或禁用',
    'validations'                   =>  [
        'datetime'                  =>  '日期无效',
        'start_time_must_egt_now'   =>  '开始时间必须大于当前时间',
        'end_time_must_gt_start_time'=> '结束时间必须大于开始时间',
        'parameter_must_promotion'  =>  '参数必须是:attribute实例',
        'class_not_exist'           =>  '促销规则类:attribute不存在',
        'full_must_great_subtract'  =>  'full必须大于subtract'
    ]
];