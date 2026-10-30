-- 研学模块调整：移除服务商、新增讲师、商品研学标记改为 is_study
-- 执行库：crmeb31（表前缀 eb_）

-- 1. 商品表：研学标记由 service_provider_id 改为 is_study
ALTER TABLE `eb_store_product`
  ADD COLUMN `is_study` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否研学商品 0否 1是' AFTER `gift_price`;

-- 2. 研学讲师
CREATE TABLE IF NOT EXISTS `eb_lecturer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '讲师id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '讲师姓名',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '讲师简介',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='研学讲师';

-- 3. 订单表：新增研学讲师字段
ALTER TABLE `eb_store_order`
  ADD COLUMN `lecturer_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '研学讲师id' AFTER `study_snatch_id`;
ALTER TABLE `eb_store_order`
  ADD COLUMN `lecturer_name` varchar(64) NOT NULL DEFAULT '' COMMENT '研学讲师姓名(快照)' AFTER `lecturer_id`;

-- 5. 订单表：新增抢购人身份证号
ALTER TABLE `eb_store_order`
  ADD COLUMN `user_id_card` varchar(18) NOT NULL DEFAULT '' COMMENT '购买人/抢购人身份证号' AFTER `user_phone`;

-- 4. 可选清理（会删除服务商数据，请确认后手动执行）
-- ALTER TABLE `eb_store_product` DROP COLUMN `service_provider_id`;
-- DROP TABLE IF EXISTS `eb_service_provider`;

-- 6. 后台菜单：研学-抢购人信息（pid 3465 为「研学」顶级菜单，请按实际库中 id 调整）
INSERT INTO `eb_system_menus`
  (`pid`,`icon`,`menu_name`,`module`,`controller`,`action`,`api_url`,`methods`,`params`,`sort`,`is_show`,`is_show_path`,`access`,`menu_path`,`path`,`auth_type`,`header`,`is_header`,`unique_auth`,`is_del`,`mark`)
VALUES
  (3465,'','抢购人信息','admin','','','','','[]',3,1,1,1,'/study/study_order','',1,'',0,'admin-study-study-order',0,'');
