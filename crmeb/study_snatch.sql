-- 研学抢购场次表
CREATE TABLE IF NOT EXISTS `eb_store_study_snatch` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '场次id',
  `product_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '研学商品id(eb_store_product.id)',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '场次名称(如第1期)',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '开抢时间(秒级时间戳)',
  `stop_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '结束时间(秒级时间戳)',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '抢购价',
  `ot_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '划线价/原价',
  `quota` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余名额',
  `quota_show` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总名额',
  `sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已抢名额',
  `num` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '每人总限购名额',
  `once_num` int(11) unsigned NOT NULL DEFAULT '1' COMMENT '单次限购名额',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0关闭 1启用',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除 0否 1是',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_time` (`start_time`,`stop_time`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='研学抢购场次';

-- 订单表新增研学抢购场次id
ALTER TABLE `eb_store_order`
  ADD COLUMN `study_snatch_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '研学抢购场次id' AFTER `advance_id`;
