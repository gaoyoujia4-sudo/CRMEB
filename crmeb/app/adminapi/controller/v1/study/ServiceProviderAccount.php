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
use app\services\study\ServiceProviderAccountServices;

/**
 * 研学供应商账号控制器
 * Class ServiceProviderAccount
 * @package app\adminapi\controller\v1\study
 */
class ServiceProviderAccount extends AuthController
{
    /**
     * 构造方法
     * ServiceProviderAccount constructor.
     * @param App $app
     * @param ServiceProviderAccountServices $services
     */
    public function __construct(App $app, ServiceProviderAccountServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 账号列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['keywords', 's'], ''],
            [['provider_id', 'd'], ''],
            [['status', 'd'], ''],
        ]);
        return app('json')->success($this->services->getAccountList($where));
    }

    /**
     * 账号详情
     * @param int $id
     * @return mixed
     */
    public function read($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->getAccountInfo((int)$id);
        if (!$info) return app('json')->fail('账号不存在');
        return app('json')->success(['info' => $info]);
    }

    /**
     * 保存修改账号
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['provider_id', 0],
            ['account', ''],
            ['pwd', ''],
            ['real_name', ''],
            ['status', 1],
        ]);
        $this->validate($data, \app\adminapi\validate\study\ServiceProviderAccountValidate::class, 'save');
        $this->services->saveAccount((int)$id, $data);
        return app('json')->success('设置成功');
    }

    /**
     * 删除账号
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
