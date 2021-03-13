<?php

namespace Qihucms\Lbs\Controllers\Admin;

use Encore\Admin\Widgets\Form;
use Illuminate\Http\Request;

class ConfigForm extends Form
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title;

    /**
     * ConfigForm constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->title = __('qihu_lbs::lbs.lbs_setting_h1');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function handle(Request $request)
    {
        $data = $request->all();

        if (app('env-editor')->setEnv($data)) {
            admin_success(__('qh.update_success'));
        } else {
            admin_error(__('qh.update_failed'));
        }
        return back();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->select('lbs', __('qihu_lbs::lbs.lbs_select'))
            ->options([
                'tencent' => __('qihu_lbs::lbs.tencent'),
                'gaode' => __('qihu_lbs::lbs.gaode'),
                'baidu' => __('qihu_lbs::lbs.baidu'),
            ]);
        $this->divider(__('qihu_lbs::lbs.tencent'));
        $this->text('tencent_lbs_key', 'Key')
            ->help(__('qihu_lbs::lbs.tencent_help'));
        $this->text('tencent_lbs_sk', __('qihu_lbs::lbs.sign'));

        $this->divider(__('qihu_lbs::lbs.gaode'));
        $this->text('amap_lbs_key', 'Key')
            ->help(__('qihu_lbs::lbs.gaode_help'));
        $this->text('amap_lbs_sk', __('qihu_lbs::lbs.sign'));

        $this->divider(__('qihu_lbs::lbs.baidu'));
        $this->text('baidu_lbs_key', 'Key')
            ->help(__('qihu_lbs::lbs.baidu_help'));
        $this->text('baidu_lbs_sk', __('qihu_lbs::lbs.sign'));
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            'lbs' => config('qihu_lbs.default'),
            'tencent_lbs_key' => config('qihu_lbs.tencent_key'),
            'tencent_lbs_sk' => config('qihu_lbs.tencent_sk'),
            'amap_lbs_key' => config('qihu_lbs.amap_key'),
            'amap_lbs_sk' => config('qihu_lbs.amap_sk'),
            'baidu_lbs_key' => config('qihu_lbs.baidu_key'),
            'baidu_lbs_sk' => config('qihu_lbs.baidu_sk'),
        ];
    }
}
