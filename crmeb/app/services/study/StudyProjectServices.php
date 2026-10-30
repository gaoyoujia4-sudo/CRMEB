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

use app\dao\product\product\StoreProductDao;
use app\dao\study\LecturerDao;
use app\dao\study\ServiceProviderDao;
use app\dao\study\StudyProjectDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;

/**
 * 研学项目
 * Class StudyProjectServices
 * @package app\services\study
 * @method update($id, array $data, ?string $key = null) 修改数据
 * @method get(int $id, ?array $field = []) 获取数据
 */
class StudyProjectServices extends BaseServices
{
    /**
     * 构造方法
     * StudyProjectServices constructor.
     * @param StudyProjectDao $dao
     */
    public function __construct(StudyProjectDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 项目列表
     * @param array $where
     * @return array
     */
    public function getProjectList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getProjectList($where, ['*'], $page, $limit);
        $count = $this->dao->count($where);
        $list = $this->decorateLecturers($list);
        $list = $this->decorateProviders($list);
        $list = $this->decorateProducts($list);
        return compact('list', 'count');
    }

    /**
     * 项目详情（含讲师信息）
     * @param int $id
     * @return array
     */
    public function getProjectInfo(int $id)
    {
        if (!$id) return [];
        $info = $this->dao->get($id);
        if (!$info) return [];
        $info = $info->toArray();
        $ids = $this->parseLecturerIds($info['lecturer_ids'] ?? '');
        $info['lecturer_ids'] = $ids;
        $info['lecturers'] = $this->getLecturerList($ids);
        $providerId = (int)($info['service_provider_id'] ?? 0);
        $info['provider_name'] = $providerId ? (string)app()->make(ServiceProviderDao::class)->value(['id' => $providerId], 'name') : '';
        $productId = (int)($info['product_id'] ?? 0);
        $info['product_name'] = '';
        $info['product_online'] = 0;
        if ($productId) {
            /** @var StoreProductDao $productDao */
            $productDao = app()->make(StoreProductDao::class);
            $info['product_name'] = (string)$productDao->value(['id' => $productId], 'store_name');
            $info['product_online'] = $productDao->count(['id' => $productId, 'is_del' => 0, 'is_show' => 1]) ? 1 : 0;
        }
        return $info;
    }

    /**
     * 保存或修改项目
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function saveProject(int $id, array $data)
    {
        return $this->transaction(function () use ($id, $data) {
            $lecturerIds = $data['lecturer_ids'] ?? [];
            if (is_string($lecturerIds)) {
                $lecturerIds = json_decode($lecturerIds, true) ?: [];
            }
            $lecturerIds = array_values(array_unique(array_filter(array_map('intval', (array)$lecturerIds))));
            $data['lecturer_ids'] = json_encode($lecturerIds, JSON_UNESCAPED_UNICODE);
            unset($data['lecturers']);
            $data['service_provider_id'] = (int)($data['service_provider_id'] ?? 0);
            $data['product_id'] = (int)($data['product_id'] ?? 0);
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

    /**
     * 删除项目
     * @param int $id
     * @return bool
     */
    public function deleteProject(int $id)
    {
        return $this->transaction(function () use ($id) {
            if (!$this->dao->get($id)) throw new AdminException('项目不存在');
            return (bool)$this->dao->delete($id);
        });
    }

    /**
     * 解析讲师id（兼容数组与JSON字符串）
     * @param array|string $lecturerIds
     * @return array
     */
    protected function parseLecturerIds($lecturerIds): array
    {
        if (is_array($lecturerIds)) {
            $ids = $lecturerIds;
        } else {
            $ids = json_decode((string)$lecturerIds, true) ?: [];
        }
        return array_values(array_unique(array_filter(array_map('intval', (array)$ids))));
    }

    /**
     * 讲师id集合转讲师信息
     * @param array $ids
     * @return array
     */
    protected function getLecturerList(array $ids): array
    {
        if (!$ids) return [];
        $nameMap = app()->make(LecturerDao::class)->getColumn([['id', 'in', $ids]], 'name', 'id');
        return array_map(function ($id) use ($nameMap) {
            return ['id' => $id, 'name' => $nameMap[$id] ?? ''];
        }, $ids);
    }

    /**
     * 批量补充列表的讲师信息
     * @param array $list
     * @return array
     */
    protected function decorateLecturers(array $list): array
    {
        if (!$list) return $list;
        $allIds = [];
        foreach ($list as $item) {
            $allIds = array_merge($allIds, $this->parseLecturerIds($item['lecturer_ids'] ?? ''));
        }
        $allIds = array_values(array_unique(array_filter($allIds)));
        $nameMap = $allIds ? app()->make(LecturerDao::class)->getColumn([['id', 'in', $allIds]], 'name', 'id') : [];
        foreach ($list as &$item) {
            $ids = $this->parseLecturerIds($item['lecturer_ids'] ?? '');
            $item['lecturer_ids'] = $ids;
            $item['lecturers'] = array_map(function ($id) use ($nameMap) {
                return ['id' => $id, 'name' => $nameMap[$id] ?? ''];
            }, $ids);
            $item['lecturer_name'] = implode('、', array_column($item['lecturers'], 'name'));
        }
        unset($item);
        return $list;
    }

    /**
     * 批量补充列表的服务商名称
     * @param array $list
     * @return array
     */
    protected function decorateProviders(array $list): array
    {
        if (!$list) return $list;
        $providerIds = array_values(array_unique(array_filter(array_map('intval', array_column($list, 'service_provider_id')))));
        $nameMap = $providerIds ? app()->make(ServiceProviderDao::class)->getColumn([['id', 'in', $providerIds]], 'name', 'id') : [];
        foreach ($list as &$item) {
            $item['provider_name'] = $nameMap[$item['service_provider_id'] ?? 0] ?? '';
        }
        unset($item);
        return $list;
    }

    /**
     * 批量补充列表的关联商品信息（名称/是否在线可购买）
     * @param array $list
     * @return array
     */
    protected function decorateProducts(array $list): array
    {
        if (!$list) return $list;
        $productIds = array_values(array_unique(array_filter(array_map('intval', array_column($list, 'product_id')))));
        $nameMap = [];
        $onlineMap = [];
        if ($productIds) {
            /** @var StoreProductDao $productDao */
            $productDao = app()->make(StoreProductDao::class);
            $nameMap = $productDao->getColumn([['id', 'in', $productIds]], 'store_name', 'id');
            $onlineMap = $productDao->getColumn([['id', 'in', $productIds], ['is_del', '=', 0], ['is_show', '=', 1]], 'id', 'id');
        }
        foreach ($list as &$item) {
            $pid = (int)($item['product_id'] ?? 0);
            $item['product_name'] = $pid ? ($nameMap[$pid] ?? '') : '';
            $item['product_online'] = ($pid && isset($onlineMap[$pid])) ? 1 : 0;
        }
        unset($item);
        return $list;
    }
}
