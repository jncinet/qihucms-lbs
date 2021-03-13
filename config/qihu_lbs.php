<?php

return [
    // 默认使用
    'default' => env('LBS', 'tencent'),

    // 腾讯地图
    'tencent_key' => env('TENCENT_LBS_KEY', ''),
    'tencent_sk' => env('TENCENT_LBS_SK', ''),

    // 高德地图
    'amap_key' => env('AMAP_LBS_KEY', ''),
    'amap_sk' => env('AMAP_LBS_SK', ''),

    // 百度地图
    'baidu_key' => env('BAIDU_LBS_KEY', ''),
    'baidu_sk' => env('BAIDU_LBS_SK', ''),
];