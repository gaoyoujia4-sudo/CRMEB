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
namespace app\adminapi\controller\v1\study;

use think\facade\App;
use app\adminapi\controller\AuthController;
use app\services\study\StudyOrderServices;

/**
 * 研学抢购人控制器
 * Class StudyOrder
 * @package app\adminapi\controller\v1\study
 */
class StudyOrder extends AuthController
{
    /**
     * StudyOrder constructor.
     * @param App $app
     * @param StudyOrderServices $services
     */
    public function __construct(App $app, StudyOrderServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 抢购人列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['keywords', 's'], ''],
            [['status', 'd'], ''],
        ]);
        return app('json')->success($this->services->getStudyBuyerList($where));
    }
}
