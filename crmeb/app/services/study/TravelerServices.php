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

use app\dao\study\TravelerDao;
use app\services\BaseServices;
use crmeb\exceptions\ApiException;

/**
 * 研学出行人
 * Class TravelerServices
 * @package app\services\study
 * @method update($id, array $data, ?string $key = null) 修改数据
 * @method getOne(array $where, ?string $field = '*', array $with = []) 获取单条数据
 */
class TravelerServices extends BaseServices
{
    /**
     * 构造方法
     * TravelerServices constructor.
     * @param TravelerDao $dao
     */
    public function __construct(TravelerDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 根据身份证号自动判断性别
     * 18位身份证第17位(倒数第2位)、15位身份证最后一位：奇数为男，偶数为女
     * @param string $idCard
     * @return int 1男 2女 0未知
     */
    public static function getGenderByIdCard(string $idCard): int
    {
        $idCard = trim($idCard);
        if (!$idCard) return 0;
        $len = strlen($idCard);
        if ($len == 18) {
            $digit = (int)substr($idCard, 16, 1);
        } elseif ($len == 15) {
            $digit = (int)substr($idCard, 14, 1);
        } else {
            return 0;
        }
        return ($digit % 2 == 1) ? 1 : 2;
    }

    /**
     * 校验手机号（大陆手机号）
     * @param string $phone
     * @return bool
     */
    public static function checkPhone(string $phone): bool
    {
        return (bool)preg_match('/^1[3-9]\d{9}$/', $phone);
    }

    /**
     * 校验18位身份证号（GB 11643-1999 校验位）
     * @param string $idCard
     * @return bool
     */
    public static function checkIdCard(string $idCard): bool
    {
        $idCard = strtoupper(trim($idCard));
        if (!preg_match('/^\d{17}[\dX]$/', $idCard)) return false;
        $weights = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $checkCodes = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += (int)$idCard[$i] * $weights[$i];
        }
        return $checkCodes[$sum % 11] === $idCard[17];
    }

    /**
     * 用户常用出行人列表
     * @param int $uid
     * @return array
     */
    public function getTravelerList(int $uid)
    {
        return $this->dao->getTravelerList(['uid' => $uid, 'is_del' => 0]);
    }

    /**
     * 保存常用出行人（按 uid + 身份证号 去重）
     * @param int $uid
     * @param array $data
     * @return mixed
     */
    public function saveTraveler(int $uid, array $data)
    {
        $data['uid'] = $uid;
        $data['id_card'] = trim($data['id_card'] ?? '');
        $data['name'] = trim($data['name'] ?? '');
        $data['phone'] = trim($data['phone'] ?? '');
        if (!$data['id_card'] || !$data['name']) {
            throw new ApiException('请填写完整的出行人信息');
        }
        if ($data['phone'] && !self::checkPhone($data['phone'])) {
            throw new ApiException('手机号格式不正确');
        }
        if (!self::checkIdCard($data['id_card'])) {
            throw new ApiException('身份证号格式不正确');
        }
        $data['gender'] = self::getGenderByIdCard($data['id_card']);
        $data['update_time'] = time();
        $exist = $this->dao->getOne(['uid' => $uid, 'id_card' => $data['id_card'], 'is_del' => 0]);
        if ($exist) {
            $this->dao->update($exist['id'], $data);
            return $exist['id'];
        }
        $data['add_time'] = time();
        $data['is_del'] = 0;
        $res = $this->dao->save($data);
        return $res->id ?? 0;
    }

    /**
     * 批量保存常用出行人（下单时自动保存，供下次复用）
     * @param int $uid
     * @param array $travelers
     */
    public function saveTravelers(int $uid, array $travelers)
    {
        foreach ($travelers as $traveler) {
            $name = trim($traveler['name'] ?? '');
            $idCard = trim($traveler['id_card'] ?? '');
            if (!$name && !$idCard) continue;
            $this->saveTraveler($uid, [
                'name' => $name,
                'id_card' => $idCard,
                'phone' => $traveler['phone'] ?? '',
            ]);
        }
    }

    /**
     * 删除常用出行人
     * @param int $id
     * @param int $uid
     * @return mixed
     */
    public function deleteTraveler(int $id, int $uid)
    {
        $exist = $this->dao->getOne(['id' => $id, 'uid' => $uid, 'is_del' => 0]);
        if (!$exist) {
            throw new ApiException('出行人不存在');
        }
        return $this->dao->update($id, ['is_del' => 1]);
    }
}
