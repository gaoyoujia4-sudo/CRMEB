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
namespace crmeb\utils;

use crmeb\services\CacheService;

/**
 * 通用图形验证码
 * 与登录专用的 Captcha 不同：下发随机 key，校验按 key 取值、用后即焚，
 * 不依赖 session，小程序 / H5 / PC 任意端都能复用。
 *
 * Class CommonCaptcha
 * @package crmeb\utils
 */
class CommonCaptcha
{
    /**
     * @var Captcha
     */
    protected $captcha;

    /**
     * 验证码有效期（秒）
     * @var int
     */
    protected $expire = 300;

    /**
     * CommonCaptcha constructor.
     * @param Captcha $captcha
     */
    public function __construct(Captcha $captcha)
    {
        $this->captcha = $captcha;
    }

    /**
     * 生成验证码
     * @return array ['key' => 随机key, 'img' => base64图片]
     */
    public function create(): array
    {
        // 复用 Captcha 生成明文与密码 hash（内部会按明文多写一条缓存，下面清理掉）
        $generator = $this->captcha->generate();
        $value = $generator['value'];
        $hash = $generator['key'];

        // 清理 generate() 留下的明文缓存，只保留按随机 key 存的那份
        CacheService::delete('captcha_' . mb_strtolower($value, 'UTF-8'));

        // 随机 key，与明文无关，避免枚举
        $key = bin2hex(random_bytes(16));
        CacheService::set('captcha_' . $key, $hash, $this->expire);

        // 画图并转 base64，小程序可直接 <image :src="img"> 显示
        $response = $this->captcha->create(['value' => $value]);
        $img = 'data:image/png;base64,' . base64_encode($response->getContent());

        return ['key' => $key, 'img' => $img];
    }

    /**
     * 校验验证码（一次性，校验后立即销毁）
     * @param string $key
     * @param string $code
     * @return bool
     */
    public function verify(string $key, string $code): bool
    {
        $key = trim($key);
        if ($key === '' || $code === '') {
            return false;
        }
        $name = 'captcha_' . $key;
        $hash = CacheService::get($name);
        if (!$hash) {
            return false;
        }
        // 先销毁再校验，保证一次性
        CacheService::delete($name);
        return password_verify(mb_strtolower(trim($code), 'UTF-8'), $hash);
    }
}
