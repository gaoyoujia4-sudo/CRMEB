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
use app\model\study\Lecturer;

/**
 * 研学讲师dao
 * Class LecturerDao
 * @package app\dao\study
 */
class LecturerDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return Lecturer::class;
    }

    /**
     * 讲师分页列表
     * @param array $where
     * @param array $field
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getLecturerList(array $where, array $field = ['*'], int $page = 0, int $limit = 0)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->field($field)->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * 获取全部启用的讲师（下拉选择用）
     * @param array $field
     * @return array
     */
    public function getAllLecturer(array $field = ['id', 'name'])
    {
        return $this->search(['status' => 1])->field($field)->order('sort desc,id desc')->select()->toArray();
    }
}
