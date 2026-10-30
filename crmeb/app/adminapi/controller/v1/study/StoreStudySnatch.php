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
use app\services\study\StoreStudySnatchServices;

/**
 * 研学抢购场次控制器
 * Class StoreStudySnatch
 * @package app\adminapi\controller\v1\study
 */
class StoreStudySnatch extends AuthController
{
    /**
     * StoreStudySnatch constructor.
     * @param App $app
     * @param StoreStudySnatchServices $services
     */
    public function __construct(App $app, StoreStudySnatchServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 场次列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['product_id', 'd'], 0],
            [['status', 'd'], ''],
            [['keywords', 's'], ''],
            [['is_del', 'd'], 0],
        ]);
        return app('json')->success($this->services->getSnatchList($where));
    }

    /**
     * 场次详情
     * @param int $id
     * @return mixed
     */
    public function read($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->get((int)$id);
        if (!$info) return app('json')->fail('场次不存在');
        return app('json')->success(['info' => $info->toArray()]);
    }

    /**
     * 保存修改场次
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['product_id', 0],
            ['title', ''],
            ['start_time', ''],
            ['stop_time', ''],
            ['price', 0],
            ['ot_price', 0],
            ['quota_show', 0],
            ['num', 1],
            ['once_num', 1],
            ['status', 1],
            ['sort', 0],
            ['meeting_point_name', ''],
            ['meeting_point_address', ''],
            ['meeting_point_lng', ''],
            ['meeting_point_lat', ''],
        ]);
        $this->validate($data, \app\adminapi\validate\study\StoreStudySnatchValidate::class, 'save');
        $this->services->saveSnatch((int)$id, $data);
        return app('json')->success('设置成功');
    }

    /**
     * 删除场次
     * @param int $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $res = $this->services->deleteSnatch((int)$id);
        if ($res) {
            return app('json')->success('删除成功');
        } else {
            return app('json')->fail('删除失败');
        }
    }
}
