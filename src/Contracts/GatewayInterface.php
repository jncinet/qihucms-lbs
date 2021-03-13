<?php

namespace Qihucms\Lbs\Contracts;

interface GatewayInterface
{
    /**
     * IP定位
     *
     * @param $ip
     * @return mixed
     */
    public function ipLocation($ip);

    /**
     * GPS定位
     *
     * @param $ip
     * @return mixed
     */
    public function gpsLocation($ip);
}