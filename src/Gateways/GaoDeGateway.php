<?php

namespace Qihucms\Lbs\Gateways;

use GuzzleHttp\Client;
use Qihucms\Lbs\Contracts\GatewayInterface;

class GaoDeGateway implements GatewayInterface
{
    const BASE_URI = 'https://restapi.amap.com';

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
        $query = array_merge(['key' => config('qihu_lbs.amap_key')], $query);

        if (!empty(config('qihu_lbs.amap_sk'))) {
            $query = array_merge($query, ['sig' => config('qihu_lbs.amap_sk')]);
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
            $result = $this->request('v3/ip', [
                'ip' => $ip,
                'output' => 'JSON'
            ]);

            if (isset($result['status']) && $result['status'] > 0 && !empty($result['rectangle'])) {
                $data['message'] = $result['info'];
                $result['rectangle'] = explode(';', $result['rectangle']);

                if (count($result['rectangle'])) {
                    $result['rectangle'][0] = explode(',', $result['rectangle'][0]);
                    if (count($result['rectangle'][0])) {
                        $this->data['result']['location']['lat'] = sprintf("%.6f", $result['rectangle'][0][0]);
                        $this->data['result']['location']['lng'] = sprintf("%.6f", $result['rectangle'][0][1]);
                    }
                }

                $this->data['result']['ad_info']['province'] = $result['province'];
                $this->data['result']['ad_info']['city'] = $result['city'];
                $this->data['result']['ad_info']['adcode'] = $result['adcode'];
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
            $result = $this->request('v3/geocode/regeo', [
                'location' => $latitude . ',' . $longitude,
                'output' => 'JSON',
            ]);

            if (isset($result['status']) && $result['status'] > 0) {
                $this->data['message'] = $result['regeocode']['formatted_address'];
                $this->data['result']['ad_info']['province'] = $result['regeocode']['addressComponent']['province'];
                $this->data['result']['ad_info']['city'] = $result['regeocode']['addressComponent']['city'];
                if (is_array($this->data['result']['ad_info']['city'])) {
                    if (count($this->data['result']['ad_info']['city'])) {
                        $this->data['result']['ad_info']['city'] = $this->data['result']['ad_info']['city'][0];
                    } else {
                        $this->data['result']['ad_info']['city'] = '';
                    }
                }
                $this->data['result']['ad_info']['adcode'] = $result['regeocode']['addressComponent']['adcode'];
            }
        }

        return $this->data;
    }
}