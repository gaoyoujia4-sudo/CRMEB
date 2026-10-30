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

namespace app\services\study;

use app\dao\study\LecturerDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * 研学讲师
 * Class LecturerServices
 * @package app\services\study
 * @method update($id, array $data, ?string $key = null) 修改数据
 * @method get(int $id, ?array $field = []) 获取数据
 * @method delete($id, ?string $key = null) 删除数据
 */
class LecturerServices extends BaseServices
{
    /**
     * 构造方法
     * LecturerServices constructor.
     * @param LecturerDao $dao
     */
    public function __construct(LecturerDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 讲师列表
     * @param array $where
     * @return array
     */
    public function getLecturerList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getLecturerList($where, ['*'], $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 获取全部启用的讲师（下拉选择用）
     * @return array
     */
    public function getAllLecturer()
    {
        return $this->dao->getAllLecturer();
    }

    /**
     * 讲师详情
     * @param int $id
     * @return array
     */
    public function getLecturerInfo(int $id)
    {
        if (!$id) return [];
        $info = $this->dao->get($id);
        if (!$info) return [];
        $info = $info->toArray();
        return [
            'id' => $info['id'],
            'name' => $info['name'],
            'phone' => $info['phone'],
            'intro' => $info['intro'],
        ];
    }

    /**
     * 讲师姓名（订单快照用）
     * @param int $id
     * @return string
     */
    public function getLecturerName(int $id): string
    {
        return $id ? (string)$this->dao->value(['id' => $id], 'name') : '';
    }

    /**
     * 保存或修改讲师
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function saveLecturer(int $id, array $data)
    {
        return $this->transaction(function () use ($id, $data) {
            if ($id) {
                $data['update_time'] = time();
                if ($this->dao->update($id, $data)) {
                    return true;
                }
                throw new AdminException('修改失败');
            } else {
                $data['add_time'] = time();
                $data['update_time'] = time();
                if ($this->dao->save($data)) {
                    return true;
                }
                throw new AdminException('保存失败');
            }
        });
    }
}
