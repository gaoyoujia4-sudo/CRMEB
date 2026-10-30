-- 研学功能扩展 数据库变更
-- 执行库：crmeb31（表前缀 eb_）
-- 说明：服务商 / 出行人 / 订单出行人 三张新表 + 商品表新增 service_provider_id 字段

-- 1. 研学服务商
CREATE TABLE IF NOT EXISTS `eb_service_provider` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '服务商id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '服务商名称',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='研学服务商';

-- 2. 研学出行人（常用出行人）
CREATE TABLE IF NOT EXISTS `eb_traveler` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '出行人id',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
  `id_card` varchar(18) NOT NULL DEFAULT '' COMMENT '身份证号',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0未知 1男 2女',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除 0否 1是',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`),
  KEY `idx_uid_card` (`uid`, `id_card`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='研学出行人';

-- 3. 研学订单出行人/联系人
CREATE TABLE IF NOT EXISTS `eb_store_order_traveler` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `oid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '订单id(eb_store_order.id)',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '姓名',
  `id_card` varchar(18) NOT NULL DEFAULT '' COMMENT '身份证号',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别 0未知 1男 2女',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `is_contact` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否联系人 0否 1是',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `idx_oid` (`oid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='研学订单出行人';

-- 4. 商品表新增研学服务商字段
ALTER TABLE `eb_store_product`
  ADD COLUMN `service_provider_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '研学服务商id' AFTER `gift_price`;
