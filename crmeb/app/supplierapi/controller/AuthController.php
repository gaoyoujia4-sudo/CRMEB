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
namespace app\supplierapi\controller;

use crmeb\basic\BaseController;

/**
 * 供应商端基类
 * Class AuthController
 * @package app\supplierapi\controller
 */
abstract class AuthController extends BaseController
{
    /**
     * 当前供应商账号id
     * @var int
     */
    protected $supplierId;

    /**
     * 当前供应商账号信息
     * @var array
     */
    protected $supplierInfo;

    /**
     * 初始化
     */
    protected function initialize()
    {
        $this->supplierId = $this->request->supplierId();
        $this->supplierInfo = $this->request->supplierInfo();
    }
}
