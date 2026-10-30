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
use app\services\study\LecturerServices;

/**
 * 研学讲师控制器
 * Class Lecturer
 * @package app\adminapi\controller\v1\study
 */
class Lecturer extends AuthController
{
    /**
     * 构造方法
     * Lecturer constructor.
     * @param App $app
     * @param LecturerServices $services
     */
    public function __construct(App $app, LecturerServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 讲师列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['keywords', 's'], ''],
            [['status', 'd'], ''],
        ]);
        return app('json')->success($this->services->getLecturerList($where));
    }

    /**
     * 获取全部启用的讲师（下拉选择用）
     * @return mixed
     */
    public function all()
    {
        return app('json')->success($this->services->getAllLecturer());
    }

    /**
     * 讲师详情
     * @param int $id
     * @return mixed
     */
    public function read($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->get((int)$id);
        if (!$info) return app('json')->fail('讲师不存在');
        return app('json')->success(['info' => $info->toArray()]);
    }

    /**
     * 保存修改讲师
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['phone', ''],
            ['intro', ''],
            ['sort', 0],
            ['status', 1],
        ]);
        $this->validate($data, \app\adminapi\validate\study\LecturerValidate::class, 'save');
        $this->services->saveLecturer((int)$id, $data);
        return app('json')->success('设置成功');
    }

    /**
     * 删除讲师
     * @param int $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $res = $this->services->delete((int)$id);
        if ($res) {
            return app('json')->success('删除成功');
        } else {
            return app('json')->fail('删除失败');
        }
    }
}
