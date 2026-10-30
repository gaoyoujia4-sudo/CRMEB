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

use app\Request;
use app\services\study\StudyPackageServices;

/**
 * 研学套餐
 * Class StudyPackageController
 * @package app\api\controller\v1\study
 */
class StudyPackageController
{
    /**
     * @var StudyPackageServices
     */
    protected $services;

    /**
     * StudyPackageController constructor.
     * @param StudyPackageServices $services
     */
    public function __construct(StudyPackageServices $services)
    {
        $this->services = $services;
    }

    /**
     * 商品套餐列表（支持团型/场次筛选）
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request)
    {
        $productId = (int)$request->get('product_id', 0);
        $groupType = (int)$request->get('group_type', 0);
        $sessionType = (int)$request->get('session_type', 0);
        return app('json')->success($this->services->getProductPackageList($productId, $groupType, $sessionType));
    }
}
