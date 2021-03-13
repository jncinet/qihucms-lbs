<?php

namespace Qihucms\Lbs;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\Types\Self_;
use Qihucms\Lbs\Contracts\GatewayInterface;
use Qihucms\Lbs\Gateways\BaiDuGateway;
use Qihucms\Lbs\Gateways\GaoDeGateway;
use Qihucms\Lbs\Gateways\TenXunGateway;

/**
 * Class Lbs
 *
 * @method static BaiDuGateway BaiDu();
 * @method static GaoDeGateway GaoDe();
 * @method static TenXunGateway TenXun();
 * @package Qihucms\Lbs
 */
class Lbs
{
    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $arguments)
    {
        $app = new self();
        return $app->create($method);
    }

    /**
     * @param $method
     * @return mixed
     * @throws \Exception
     */
    protected function create($method)
    {
        $gateway = __NAMESPACE__ . '\\Gateways\\' . Str::studly($method) . 'Gateway';

        if (class_exists($gateway)) {
            return $this->make($gateway);
        }

        throw new \Exception("Gateway [{$method}] Not Exists");
    }

    /**
     * @param $gateway
     * @return mixed
     * @throws \Exception
     */
    protected function make($gateway)
    {
        $app = new $gateway();

        if ($app instanceof GatewayInterface) {
            return $app;
        }

        throw new \Exception("Gateway [{$gateway}] Must Be An Instance Of GatewayInterface");
    }
}