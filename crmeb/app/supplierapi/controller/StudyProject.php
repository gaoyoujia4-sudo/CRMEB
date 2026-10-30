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
namespace app\supplierapi\controller;

use app\services\study\StudyProjectServices;
use think\facade\App;

/**
 * 供应商端 · 我的研学项目
 * Class StudyProject
 * @package app\supplierapi\controller
 */
class StudyProject extends AuthController
{
    /**
     * @var StudyProjectServices
     */
    protected $services;

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
     * 我的研学项目列表（仅自己归属的项目）
     * @return mixed
     */
    public function lst()
    {
        $providerId = (int)($this->supplierInfo['provider_id'] ?? 0);
        $where = ['status' => 1, 'is_del' => 0, 'service_provider_id' => $providerId];
        return app('json')->success($this->services->getProjectList($where));
    }

    /**
     * 我的研学项目详情（校验归属，防止越权）
     * @param int $id
     * @return mixed
     */
    public function detail($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->getProjectInfo((int)$id);
        if (!$info
            || (int)($info['service_provider_id'] ?? 0) !== (int)($this->supplierInfo['provider_id'] ?? 0)
            || (int)$info['status'] !== 1
            || (int)$info['is_del'] !== 0) {
            return app('json')->fail('项目不存在');
        }
        return app('json')->success(['info' => $info]);
    }
}
