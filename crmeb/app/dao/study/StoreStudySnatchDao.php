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
use app\model\study\StoreStudySnatch;

/**
 * 研学抢购场次dao
 * Class StoreStudySnatchDao
 * @package app\dao\study
 */
class StoreStudySnatchDao extends BaseDao
{
    /**
     * 设置模型
     * @return string
     */
    protected function setModel(): string
    {
        return StoreStudySnatch::class;
    }

    /**
     * 场次分页列表
     * @param array $where
     * @param array $field
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getList(array $where, array $field = ['*'], int $page = 0, int $limit = 0)
    {
        return $this->search($where)->when($page && $limit, function ($query) use ($page, $limit) {
            $query->page($page, $limit);
        })->field($field)->order('sort desc,id desc')->select()->toArray();
    }

    /**
     * 原子扣减名额并增加已抢数（防超卖）
     * @param int $id
     * @param int $num
     * @return bool
     */
    public function decSnatchQuotaIncSales(int $id, int $num)
    {
        if ($num <= 0) return false;
        $res = $this->getModel()->where('id', $id)->where('quota', '>=', $num)
            ->dec('quota', $num)->inc('sales', $num)->update();
        return (bool)$res;
    }

    /**
     * 回补名额并减少已抢数（退款/取消订单）
     * @param int $id
     * @param int $num
     * @return bool
     */
    public function incSnatchQuotaDecSales(int $id, int $num)
    {
        if ($num <= 0) return true;
        $sales = $this->getModel()->where('id', $id)->value('sales');
        if (!$sales) return true;
        $salesNum = $num > $sales ? $sales : $num;
        $this->getModel()->where('id', $id)->inc('quota', $num)->dec('sales', $salesNum)->update();
        return true;
    }
}
