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

namespace app\dao\study;

use app\dao\BaseDao;
use app\model\study\StudyProject;

/**
 * 研学项目dao
 * Class StudyProjectDao
 * @package app\dao\study
 */
class StudyProjectDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return StudyProject::class;
    }

    /**
     * 项目分页列表
     * @param array $where
     * @param array $field
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getProjectList(array $where, array $field = ['*'], int $page = 0, int $limit = 0)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->field($field)->order('sort desc,id desc')->select()->toArray();
    }
}
