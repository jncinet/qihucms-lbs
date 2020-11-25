<?php

namespace Qihucms\Lbs\Controllers\Admin;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;
use Qihucms\EditEnv\EditEnv;

class ConfigForm extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = '地图设置';

    public function handle(Request $request)
    {
        $data = $request->all();

        if (app(EditEnv::class)->setEnv($data)) {
            admin_success('更新成功');
        } else {
            admin_error('更新失败');
        }
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->divider('腾讯地图');
        $this->text('tencent_lbs_key', 'Key')
            ->help('获取地址：<a href="https://lbs.qq.com/dev/console/key/manage" target="_blank">https://lbs.qq.com/dev/console/key/manage</a>');
        $this->text('tencent_lbs_sk', '签名');
    }

    /**
     * @return array
     */
    public function data()
    {
        $data = app(EditEnv::class)->getEnv();
        return [
            'tencent_lbs_key' => $data['TENCENT_LBS_KEY'] ?? null,
            'tencent_lbs_sk' => $data['TENCENT_LBS_SK'] ?? null,
        ];
    }
}
