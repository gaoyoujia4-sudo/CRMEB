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

use app\dao\study\StoreOrderTravelerDao;
use app\services\BaseServices;

/**
 * 研学订单出行人
 * Class StoreOrderTravelerServices
 * @package app\services\study
 */
class StoreOrderTravelerServices extends BaseServices
{
    /**
     * 构造方法
     * StoreOrderTravelerServices constructor.
     * @param StoreOrderTravelerDao $dao
     */
    public function __construct(StoreOrderTravelerDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 保存订单出行人/联系人
     * @param int $oid
     * @param int $uid
     * @param array $travelers
     */
    public function saveOrderTravelers(int $oid, int $uid, array $travelers)
    {
        if (!$oid || !$travelers) return;
        $data = [];
        foreach ($travelers as $traveler) {
            $name = trim($traveler['name'] ?? '');
            $idCard = trim($traveler['id_card'] ?? '');
            if (!$name && !$idCard) continue;
            $data[] = [
                'oid' => $oid,
                'uid' => $uid,
                'name' => $name,
                'id_card' => $idCard,
                'gender' => isset($traveler['gender']) && $traveler['gender'] ? (int)$traveler['gender'] : TravelerServices::getGenderByIdCard($idCard),
                'phone' => trim($traveler['phone'] ?? ''),
                'is_contact' => isset($traveler['is_contact']) && $traveler['is_contact'] ? 1 : 0,
                'add_time' => time(),
            ];
        }
        if ($data) {
            $this->dao->saveAll($data);
        }
    }

    /**
     * 获取订单出行人列表
     * @param int $oid
     * @return array
     */
    public function getOrderTravelers(int $oid)
    {
        if (!$oid) return [];
        return $this->dao->getListByOrder(['oid' => $oid]);
    }
}
