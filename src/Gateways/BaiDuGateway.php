<?php

namespace Qihucms\Lbs\Gateways;

use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Qihucms\Lbs\Contracts\GatewayInterface;

class BaiDuGateway implements GatewayInterface
{
    const BASE_URI = 'https://api.map.baidu.com';

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
     * 生成签名
     *
     * @param string $strUrl 不带网址的路径
     * @param array $arrQS 请求参数
     * @return array
     */
    private function sn($strUrl, $arrQS)
    {
        if (empty(config('qihu_lbs.baidu_sk'))) {
            return [];
        }

        $arrQS = Arr::sortRecursive($arrQS);
        $qs = urlencode($strUrl . '?' . Arr::query($arrQS) . config('qihu_lbs.baidu_sk'));

        return ['sn' => md5($qs)];
    }

    /**
     * 格式化请求参数生成签名
     *
     * @param string $path 请求路径
     * @param array $query 请求参数
     * @param string $method 请求参数
     * @return array
     */
    private function formatQueryString($path, $query, $method)
    {
        $query = array_merge($query, ['ak' => config('qihu_lbs.baidu_key')]);
        $query = array_merge($query, $this->sn($path, $query));
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
    private function request($path = '', $query = [], $method = 'GET')
    {
        $client = new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 26.0,
            'verify' => false
        ]);

        $queryString = $this->formatQueryString($path, $query, $method);
        if ($method === 'GET') {
            $query = ['query' => $queryString];
        }

        $response = $client->request($method, $path, $query);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * IP定位
     *
     * @param $ip
     * @param string $coor
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ipLocation($ip, $coor = 'bd09ll')
    {
        $this->data['result']['ip'] = $ip;

        if (false !== filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $result = $this->request('/location/ip', [
                'ak' => '',
                'coor' => $coor,
                'ip' => $ip,
            ]);

            if (isset($result['status']) && $result['status'] === 0) {
                $this->data['message'] = $result['address'];
                $this->data['result']['location']['lat'] = $result['content']['point']['x'];
                $this->data['result']['location']['lng'] = $result['content']['point']['y'];
                $this->data['result']['ad_info']['province'] = $result['content']['address_detail']['province'];
                $this->data['result']['ad_info']['city'] = $result['content']['address_detail']['city'];
                $this->data['result']['ad_info']['adcode'] = $result['content']['address_detail']['city_code'];
            }
        }

        return $this->data;
    }

    /**
     * 经纬度逆地址解析（坐标位置描述）
     *
     * @param string $latitude
     * @param string $longitude
     * @param string $coordtype
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function gpsLocation($latitude = '0', $longitude = '0', $coordtype = 'bd09ll')
    {
        $this->data['result']['location'] = [
            'lng' => $longitude,
            'lat' => $latitude
        ];

        if ($latitude != '0' && $longitude != 0) {
            $result = $this->request('/reverse_geocoding/v3/', [
                'ak' => '',
                'coordtype' => $coordtype,
                'location' => $latitude . ',' . $longitude,
                'output' => 'json',
            ]);

            if (isset($result['status']) && $result['status'] === 0) {
                $this->data['message'] = $result['result']['formatted_address'];
                $this->data['result']['ad_info']['province'] = $result['result']['addressComponent']['province'];
                $this->data['result']['ad_info']['city'] = $result['result']['addressComponent']['city'];
                $this->data['result']['ad_info']['adcode'] = $result['result']['addressComponent']['adcode'];
            }
        }

        return $this->data;
    }
}