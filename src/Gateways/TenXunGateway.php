<?php

namespace Qihucms\Lbs\Gateways;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Qihucms\Lbs\Contracts\GatewayInterface;

class TenXunGateway implements GatewayInterface
{
    const BASE_URI = 'https://apis.map.qq.com';

    private $data = [
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
    ];

    /**
     * 格式化请求参数生成签名
     *
     * @param string $path 请求路径
     * @param array $query 请求参数
     * @param string $method 请求参数
     * @return array
     */
    protected function formatQueryString($path, $query, $method)
    {
        $query = array_merge(['key' => config('qihu_lbs.tencent_key')], $query);
        if ($method !== 'GET' && is_array($query['data'])) {
            $query['data'] = json_encode($query['data']);
            $query['data'] = '[' . $query['data'] . ']';
        }
        $query = Arr::sortRecursive($query);

        if (!empty(config('qihu_lbs.tencent_sk'))) {
            $sign = md5($path . '?' . urldecode(Arr::query($query)) . config('qihu_lbs.tencent_sk'));
            $query = array_merge(['sig' => $sign], $query);
        }

        return $query;
    }

    /**
     * 发送请求
     *
     * @param string $path
     * @param array $query
     * @param string $method
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request($path = '', $query = [], $method = 'GET')
    {
        $client = new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 26.0,
            'verify' => false
        ]);

        $queryString = $this->formatQueryString($path, $query, $method);
        if ($method === 'GET') {
            $query = ['query' => $queryString];
        } else {
            $path .= '?sig=' . $queryString['sig'];
            unset($queryString['sig']);
            $queryString['data'] = $query['data'];
            $query = ['json' => $queryString];
        }

        $response = $client->request($method, $path, $query);
        return json_decode((string)$response->getBody(), true);
    }

    /**
     * IP定位
     *
     * @param $ip
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ipLocation($ip)
    {
        $this->data['result']['ip'] = $ip;

        if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $result = $this->request('/ws/location/v1/ip', ['ip' => $ip]);

            if (isset($result['status']) && $result['status'] === 0) {
                $this->data['message'] = $result['message'];
                $this->data['result']['location']['lat'] = $result['result']['location']['lat'];
                $this->data['result']['location']['lng'] = $result['result']['location']['lng'];
                $this->data['result']['ad_info']['province'] = $result['result']['ad_info']['province'];
                $this->data['result']['ad_info']['city'] = $result['result']['ad_info']['city'];
                $this->data['result']['ad_info']['adcode'] = $result['result']['ad_info']['adcode'];
            }
        }

        return $this->data;
    }

    /**
     * 经纬度逆地址解析（坐标位置描述）
     *
     * @param string $latitude
     * @param string $longitude
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function gpsLocation($latitude = '0', $longitude = '0')
    {
        $this->data['result']['location'] = [
            'lng' => $longitude,
            'lat' => $latitude
        ];

        if ($latitude != '0' && $longitude != 0) {
            $result = $this->request('/ws/geocoder/v1', [
                'location' => $latitude . ',' . $longitude
            ]);
            if (isset($result['status']) && $result['status'] === 0) {
                $this->data['message'] = $result['result']['address'];
                if (isset($result['result']['ad_info']['province'])) {
                    $this->data['result']['ad_info']['province'] = $result['result']['ad_info']['province'];
                    $this->data['result']['ad_info']['city'] = $result['result']['ad_info']['city'];
                    $this->data['result']['ad_info']['adcode'] = $result['result']['ad_info']['adcode'];
                } elseif (isset($result['result']['address_component']['ad_level_1'])) {
                    $this->data['result']['ad_info']['nation'] = $result['result']['address_component']['nation'];
                    $this->data['result']['ad_info']['province'] = $result['result']['address_component']['ad_level_1'];
                }
            }
        }

        return $this->data;
    }
}