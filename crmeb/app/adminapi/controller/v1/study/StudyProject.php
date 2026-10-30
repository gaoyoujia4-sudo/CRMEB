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
use app\services\study\StudyProjectServices;

/**
 * 研学项目控制器
 * Class StudyProject
 * @package app\adminapi\controller\v1\study
 */
class StudyProject extends AuthController
{
    /**
     * 构造方法
     * StudyProject constructor.
     * @param App $app
     * @param StudyProjectServices $services
     */
    public function __construct(App $app, StudyProjectServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 项目列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['keywords', 's'], ''],
            [['price_type', 'd'], ''],
            [['service_provider_id', 'd'], ''],
            [['status', 'd'], ''],
        ]);
        return app('json')->success($this->services->getProjectList($where));
    }

    /**
     * 项目详情
     * @param int $id
     * @return mixed
     */
    public function read($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->getProjectInfo((int)$id);
        if (!$info) return app('json')->fail('项目不存在');
        return app('json')->success(['info' => $info]);
    }

    /**
     * 保存修改项目
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['title', ''],
            ['intro', ''],
            ['image', ''],
            ['price_type', 1],
            ['price', 0],
            ['ot_price', 0],
            ['lecturer_ids', []],
            ['service_provider_id', 0],
            ['product_id', 0],
            ['sort', 0],
            ['status', 1],
        ]);
        $this->validate($data, \app\adminapi\validate\study\StudyProjectValidate::class, 'save');
        $this->services->saveProject((int)$id, $data);
        return app('json')->success('设置成功');
    }

    /**
     * 删除项目
     * @param int $id
     * @return mixed
     */
    public function delete($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        if ($this->services->deleteProject((int)$id)) {
            return app('json')->success('删除成功');
        }
        return app('json')->fail('删除失败');
    }
}
