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
use app\services\study\StudyPackageServices;

/**
 * 研学套餐控制器
 * Class StudyPackage
 * @package app\adminapi\controller\v1\study
 */
class StudyPackage extends AuthController
{
    /**
     * StudyPackage constructor.
     * @param App $app
     * @param StudyPackageServices $services
     */
    public function __construct(App $app, StudyPackageServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 套餐列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['product_id', 'd'], 0],
            [['group_type', 'd'], 0],
            [['session_type', 'd'], 0],
            [['status', 'd'], ''],
            [['keywords', 's'], ''],
            [['is_del', 'd'], 0],
        ]);
        return app('json')->success($this->services->getPackageList($where));
    }

    /**
     * 套餐详情
     * @param int $id
     * @return mixed
     */
    public function read($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->get((int)$id);
        if (!$info) return app('json')->fail('套餐不存在');
        return app('json')->success(['info' => $info->toArray()]);
    }

    /**
     * 保存修改套餐
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['product_id', 0],
            ['group_type', 1],
            ['session_type', 1],
            ['title', ''],
            ['price', 0],
            ['ot_price', 0],
            ['status', 1],
            ['sort', 0],
        ]);
        $this->validate($data, \app\adminapi\validate\study\StudyPackageValidate::class, 'save');
        $this->services->savePackage((int)$id, $data);
        return app('json')->success('设置成功');
    }

    /**
     * 删除套餐
     * @param int $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $res = $this->services->deletePackage((int)$id);
        if ($res) {
            return app('json')->success('删除成功');
        } else {
            return app('json')->fail('删除失败');
        }
    }
}
