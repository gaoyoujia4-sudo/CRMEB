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
use app\services\study\StoreStudySnatchServices;

/**
 * 研学抢购场次
 * Class StudySnatchController
 * @package app\api\controller\v1\study
 */
class StudySnatchController
{
    /**
     * @var StoreStudySnatchServices
     */
    protected $services;

    /**
     * StudySnatchController constructor.
     * @param StoreStudySnatchServices $services
     */
    public function __construct(StoreStudySnatchServices $services)
    {
        $this->services = $services;
    }

    /**
     * 商品详情场次列表
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request)
    {
        $productId = (int)$request->get('product_id', 0);
        return app('json')->success($this->services->getProductSnatchList($productId));
    }

    /**
     * 研学抢购聚合列表
     * @return mixed
     */
    public function index()
    {
        return app('json')->success($this->services->getSnatchIndex([]));
    }
}
