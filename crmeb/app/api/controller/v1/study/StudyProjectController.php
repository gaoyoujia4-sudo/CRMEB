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

use app\services\study\StudyProjectServices;

/**
 * 研学项目（用户端）
 * Class StudyProjectController
 * @package app\api\controller\v1\study
 */
class StudyProjectController
{
    /**
     * @var StudyProjectServices
     */
    protected $services;

    /**
     * StudyProjectController constructor.
     * @param StudyProjectServices $services
     */
    public function __construct(StudyProjectServices $services)
    {
        $this->services = $services;
    }

    /**
     * 研学项目列表（已启用）
     * @return mixed
     */
    public function lst()
    {
        $where = ['status' => 1, 'is_del' => 0];
        return app('json')->success($this->services->getProjectList($where));
    }

    /**
     * 研学项目详情
     * @param int $id
     * @return mixed
     */
    public function detail($id)
    {
        if (!$id) return app('json')->fail('参数错误');
        $info = $this->services->getProjectInfo((int)$id);
        if (!$info || (int)$info['status'] !== 1 || (int)$info['is_del'] !== 0) {
            return app('json')->fail('项目不存在');
        }
        return app('json')->success(['info' => $info]);
    }
}
