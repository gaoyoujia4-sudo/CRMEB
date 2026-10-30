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
namespace app\api\controller\v1;

use crmeb\utils\CommonCaptcha;

/**
 * 通用图形验证码
 * Class CaptchaController
 * @package app\api\controller\v1
 */
class CaptchaController
{
    /**
     * @var CommonCaptcha
     */
    protected $captcha;

    /**
     * CaptchaController constructor.
     * @param CommonCaptcha $captcha
     */
    public function __construct(CommonCaptcha $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * 获取验证码图片与 key
     * @return mixed
     */
    public function create()
    {
        return app('json')->success($this->captcha->create());
    }
}
