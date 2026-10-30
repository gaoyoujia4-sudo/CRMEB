<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
namespace app\adminapi\validate\study;

use think\Validate;

class StoreStudySnatchValidate extends Validate
{
    /**
     * 定义验证规则
     * @var array
     */
    protected $rule = [
        'product_id' => 'require',
        'title' => 'require',
        'start_time' => 'require',
        'stop_time' => 'require',
        'price' => 'require',
        'quota_show' => 'require|gt:0',
    ];

    /**
     * 定义错误信息
     * @var array
     */
    protected $message = [
        'product_id.require' => '请选择研学商品',
        'title.require' => '请填写场次名称',
        'start_time.require' => '请选择开抢时间',
        'stop_time.require' => '请选择结束时间',
        'price.require' => '请填写抢购价',
        'quota_show.require' => '请填写总名额',
        'quota_show.gt' => '总名额必须大于0',
    ];

    protected $scene = [
        'save' => ['product_id', 'title', 'start_time', 'stop_time', 'price', 'quota_show'],
    ];
}
