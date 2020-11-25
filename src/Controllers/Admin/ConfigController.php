<?php

namespace Qihucms\Lbs\Controllers\Admin;

use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title('地图设置')
            ->body(new ConfigForm());
    }
}
