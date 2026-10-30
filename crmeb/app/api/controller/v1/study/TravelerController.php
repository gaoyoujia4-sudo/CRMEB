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
use app\services\study\TravelerServices;

/**
 * 研学出行人
 * Class TravelerController
 * @package app\api\controller\v1\study
 */
class TravelerController
{
    /**
     * @var TravelerServices
     */
    protected $services;

    /**
     * TravelerController constructor.
     * @param TravelerServices $services
     */
    public function __construct(TravelerServices $services)
    {
        $this->services = $services;
    }

    /**
     * 出行人列表
     * @param Request $request
     * @return mixed
     */
    public function lst(Request $request)
    {
        $uid = (int)$request->uid();
        return app('json')->success($this->services->getTravelerList($uid));
    }

    /**
     * 保存出行人
     * @param Request $request
     * @return mixed
     */
    public function save(Request $request)
    {
        $uid = (int)$request->uid();
        $data = $request->postMore([
            ['name', ''],
            ['id_card', ''],
            ['phone', ''],
        ]);
        $this->services->saveTraveler($uid, $data);
        return app('json')->success('保存成功');
    }

    /**
     * 删除出行人
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function del(Request $request, $id)
    {
        $uid = (int)$request->uid();
        if (!$id) return app('json')->fail('参数错误');
        $this->services->deleteTraveler((int)$id, $uid);
        return app('json')->success('删除成功');
    }
}
