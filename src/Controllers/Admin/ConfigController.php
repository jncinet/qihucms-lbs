<?php

namespace Qihucms\Lbs\Controllers\Admin;

use App\Admin\Controllers\Controller;
use Encore\Admin\Layout\Content;

class ConfigController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->title(__('qihu_lbs::lbs.lbs_setting_title'))
            ->body(new ConfigForm());
    }
}
