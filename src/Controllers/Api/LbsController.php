<?php

namespace Qihucms\Lbs\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Qihucms\Lbs\Tencent;

class LbsController extends ApiController
{
    /**
     * @var Tencent
     */
    protected $tencent;

    /**
     * LbsController constructor.
     * @param Tencent $tencent
     */
    public function __construct(Tencent $tencent)
    {
        $this->tencent = $tencent;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function ip(Request $request)
    {
        $ip = $request->input('ip');

        if (empty($ip)) {
            $ip = $request->ip();
        }

        $result = $this->tencent->ipLocation($ip);

        return $this->jsonResponse($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function gps(Request $request)
    {
        $result = $this->tencent->gpsLocation($request->input('lat', 0), $request->input('lng', 0));

        return $this->jsonResponse($result);
    }
}