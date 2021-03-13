<h1 align="center">地图GPS、IP定位</h1>

## 安装
```shell
composer require jncinet/qihucms-lbs
```

## 后台
地图设置：lbs/config

## 示例
```php
// ip定位
// 路径
route('api.lbs.ip') = 'lbs/ip?ip=IP地址(可选)'

// GPS定位
// 路径
route('api.lbs.gps') = 'lbs/gps?lat=1&lng=2'

// 返回值
"data": [
    'status' => 0,
    'message' => '局域网IP',
        'result' => [
            'ip' => '127.0.0.1',
            'location' => [
                'lng' => 0,
                'lat' => 0
            ],
            'ad_info' => [
                'nation' => '中国',
                'province' => '本地',
                'city' => '局域网',
                'district' => '',
                'adcode' => -1
            ]
        ]
    ]
]
```