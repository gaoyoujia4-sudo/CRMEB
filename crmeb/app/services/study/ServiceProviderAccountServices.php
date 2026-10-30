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

use app\dao\study\ServiceProviderAccountDao;
use app\dao\study\ServiceProviderDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\AuthException;
use crmeb\services\CacheService;
use crmeb\utils\JwtAuth;

/**
 * 研学供应商账号（后台管理 + 供应商端登录）
 * Class ServiceProviderAccountServices
 * @package app\services\study
 * @method update($id, array $data, ?string $key = null) 修改数据
 * @method get(int $id, ?array $field = []) 获取数据
 * @method delete($id, ?string $key = null) 删除数据
 */
class ServiceProviderAccountServices extends BaseServices
{
    /**
     * 构造方法
     * ServiceProviderAccountServices constructor.
     * @param ServiceProviderAccountDao $dao
     */
    public function __construct(ServiceProviderAccountDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 账号列表（含服务商名称）
     * @param array $where
     * @return array
     */
    public function getAccountList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        // 不返回密码hash，避免泄露
        $list = $this->dao->getAccountList($where, ['id', 'provider_id', 'account', 'real_name', 'status', 'last_time', 'last_ip', 'add_time', 'update_time'], $page, $limit);
        $count = $this->dao->count($where);
        if ($list) {
            $providerIds = array_values(array_unique(array_filter(array_map('intval', array_column($list, 'provider_id')))));
            $nameMap = $providerIds ? app()->make(ServiceProviderDao::class)->getColumn([['id', 'in', $providerIds]], 'name', 'id') : [];
            foreach ($list as &$item) {
                $item['provider_name'] = $nameMap[$item['provider_id']] ?? '';
                $item['last_time'] = $item['last_time'] ? date('Y-m-d H:i:s', $item['last_time']) : '暂无';
            }
            unset($item);
        }
        return compact('list', 'count');
    }

    /**
     * 账号详情
     * @param int $id
     * @return array
     */
    public function getAccountInfo(int $id)
    {
        if (!$id) return [];
        $info = $this->dao->get($id);
        if (!$info) return [];
        return [
            'id' => $info['id'],
            'provider_id' => $info['provider_id'],
            'account' => $info['account'],
            'real_name' => $info['real_name'],
            'status' => $info['status'],
        ];
    }

    /**
     * 保存或修改账号（密码仅在传入时更新）
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function saveAccount(int $id, array $data)
    {
        return $this->transaction(function () use ($id, $data) {
            if (!empty($data['pwd'])) {
                $data['pwd'] = $this->passwordHash($data['pwd']);
            } else {
                unset($data['pwd']);
            }
            if ($id) {
                if (!$this->dao->get($id)) throw new AdminException('账号不存在');
                $data['update_time'] = time();
                if (!$this->dao->update($id, $data)) throw new AdminException('修改失败');
            } else {
                if (empty($data['account'])) throw new AdminException('请填写登录账号');
                if (empty($data['pwd'])) throw new AdminException('请设置登录密码');
                $data['add_time'] = time();
                $data['update_time'] = time();
                if (!$this->dao->save($data)) throw new AdminException('保存失败');
            }
            return true;
        });
    }

    /**
     * 供应商账号密码登录
     * @param string $account
     * @param string $password
     * @return array
     */
    public function authLogin(string $account, string $password = null)
    {
        $info = $this->dao->get(['account' => $account]);
        if (!$info) throw new AuthException('没有此账号');
        if ($password && !password_verify($password, $info->pwd)) throw new AuthException('账号或密码错误');
        if (!$info->status) throw new AuthException('您已被禁止登录，请联系管理员');
        $token = $this->createToken($info->id, 'supplier');
        $info->last_time = time();
        $info->last_ip = request()->ip();
        $info->save();
        return [
            'token' => $token['token'],
            'exp_time' => $token['params']['exp'],
            'supplierInfo' => $info->hidden(['pwd', 'last_ip', 'add_time', 'update_time', 'status'])->toArray(),
        ];
    }

    /**
     * 解析token
     * @param string $token
     * @return array
     */
    public function parseToken(string $token)
    {
        $noCli = !request()->isCli();
        $md5Token = md5($token);
        if (!$token || $token === 'undefined' || !CacheService::has($md5Token) || !(CacheService::get($md5Token, '', NULL, 'supplier'))) {
            throw new AuthException('请登录', [], 402);
        }

        /** @var JwtAuth $jwtAuth */
        $jwtAuth = app()->make(JwtAuth::class);
        [$id, $type] = $jwtAuth->parseToken($token);

        try {
            $jwtAuth->verifyToken();
        } catch (\Throwable $e) {
            $noCli && CacheService::delete($md5Token);
            throw new AuthException('登录已过期,请重新登录', [], 402);
        }

        $info = $this->dao->get($id);
        if (!$info || !$info->id) {
            $noCli && CacheService::delete($md5Token);
            throw new AuthException('登录状态有误,请重新登录', [], 402);
        }
        if (!$info->status) {
            $noCli && CacheService::delete($md5Token);
            throw new AuthException('您已被禁止登录', [], 402);
        }

        $info->type = $type;
        return $info->hidden(['pwd', 'last_ip', 'status'])->toArray();
    }

    /**
     * 退出登录
     * @param string $token
     * @return bool
     */
    public function logout(string $token)
    {
        if ($token) CacheService::delete(md5($token));
        return true;
    }
}
