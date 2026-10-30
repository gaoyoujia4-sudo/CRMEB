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

use app\dao\study\ServiceProviderDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * 研学服务商/供应商
 * Class ServiceProviderServices
 * @package app\services\study
 * @method update($id, array $data, ?string $key = null) 修改数据
 * @method get(int $id, ?array $field = []) 获取数据
 * @method delete($id, ?string $key = null) 删除数据
 */
class ServiceProviderServices extends BaseServices
{
    /**
     * 构造方法
     * ServiceProviderServices constructor.
     * @param ServiceProviderDao $dao
     */
    public function __construct(ServiceProviderDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 服务商列表
     * @param array $where
     * @return array
     */
    public function getProviderList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getProviderList($where, ['*'], $page, $limit);
        $count = $this->dao->count($where);
        return compact('list', 'count');
    }

    /**
     * 获取全部启用的服务商（下拉选择用）
     * @return array
     */
    public function getAllProvider()
    {
        return $this->dao->getAllProvider();
    }

    /**
     * 服务商名称
     * @param int $id
     * @return string
     */
    public function getProviderName(int $id): string
    {
        return $id ? (string)$this->dao->value(['id' => $id], 'name') : '';
    }

    /**
     * 服务商详情
     * @param int $id
     * @return array
     */
    public function getProviderInfo(int $id)
    {
        if (!$id) return [];
        $info = $this->dao->get($id);
        if (!$info) return [];
        return [
            'id' => $info['id'],
            'name' => $info['name'],
            'phone' => $info['phone'],
            'address' => $info['address'],
            'sort' => $info['sort'],
            'status' => $info['status'],
        ];
    }

    /**
     * 保存或修改服务商
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function saveProvider(int $id, array $data)
    {
        return $this->transaction(function () use ($id, $data) {
            if ($id) {
                $data['update_time'] = time();
                if (!$this->dao->update($id, $data)) throw new AdminException('修改失败');
            } else {
                $data['add_time'] = time();
                $data['update_time'] = time();
                if (!$this->dao->save($data)) throw new AdminException('保存失败');
            }
            return true;
        });
    }
}
