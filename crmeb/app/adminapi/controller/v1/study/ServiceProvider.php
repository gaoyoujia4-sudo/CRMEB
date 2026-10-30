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
use app\services\study\ServiceProviderServices;

/**
 * 研学服务商控制器
 * Class ServiceProvider
 * @package app\adminapi\controller\v1\study
 */
class ServiceProvider extends AuthController
{
    /**
     * 构造方法
     * ServiceProvider constructor.
     * @param App $app
     * @param ServiceProviderServices $services
     */
    public function __construct(App $app, ServiceProviderServices $services)
    {
        parent::__construct($app);
        $this->services = $services;
    }

    /**
     * 服务商列表
     * @return mixed
     */
    public function index()
    {
        $where = $this->request->getMore([
            [['keywords', 's'], ''],
            [['status', 'd'], ''],
        ]);
        return app('json')->success($this->services->getProviderList($where));
    }

    /**
     * 获取全部启用的服务商（下拉选择用）
     * @return mixed
     */
    public function all()
    {
        return app('json')->success($this->services->getAllProvider());
    }

    /**
     * 服务商详情
     * @param int $id
     * @return mixed
     */
    public function read($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->getProviderInfo((int)$id);
        if (!$info) return app('json')->fail('服务商不存在');
        return app('json')->success(['info' => $info]);
    }

    /**
     * 保存修改服务商
     * @param int $id
     * @return mixed
     */
    public function save($id = 0)
    {
        $data = $this->request->postMore([
            ['name', ''],
            ['phone', ''],
            ['address', ''],
            ['sort', 0],
            ['status', 1],
        ]);
        $this->validate($data, \app\adminapi\validate\study\ServiceProviderValidate::class, 'save');
        $this->services->saveProvider((int)$id, $data);
        return app('json')->success('设置成功');
    }

    /**
     * 删除服务商
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
