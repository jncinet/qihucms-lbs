<?php

namespace Qihucms\Lbs\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LbsController extends Controller
{
    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    protected $app;

    /**
     * LbsController constructor.
     */
    public function __construct()
    {
        $this->app = app('lbs.' . config('qihu_lbs.default'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ip(Request $request)
    {
        $ip = $request->input('ip');

        if (empty($ip)) {
            $ip = $request->ip();
        }

        $result = $this->app->ipLocation($ip);

        // 国外IP无法解析时，能过取到的经纬度再次解析
        if ($result['result']['ad_info']['adcode'] < 1
            && $result['result']['location']['lng'] !== 0
            && $result['result']['location']['lat'] !== 0) {
            $result = $this->app->gpsLocation(
                $result['result']['location']['lat'],
                $result['result']['location']['lng']
            );
        }

        return $this->jsonResponse($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function gps(Request $request)
    {
        $result = $this->app->gpsLocation(
            $request->input('lat', 0),
            $request->input('lng', 0)
        );

        return $this->jsonResponse($result);
    }
}