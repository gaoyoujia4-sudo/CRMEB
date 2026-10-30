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
namespace app\api\controller\v1\study;

use app\services\study\LecturerServices;

/**
 * 研学讲师（用户端）
 * Class LecturerController
 * @package app\api\controller\v1\study
 */
class LecturerController
{
    /**
     * @var LecturerServices
     */
    protected $services;

    /**
     * LecturerController constructor.
     * @param LecturerServices $services
     */
    public function __construct(LecturerServices $services)
    {
        $this->services = $services;
    }

    /**
     * 讲师列表（预订选择用）
     * @return mixed
     */
    public function lst()
    {
        return app('json')->success($this->services->getAllLecturer());
    }
}
