-- 研学供应商独立网站 数据库变更
-- 执行库：crmeb31（表前缀 eb_）
-- 说明：供应商(服务商) + 供应商账号 + 研学项目归属服务商

-- 1. 研学服务商/供应商（幂等，兼容已存在的表）
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

-- 2. 研学项目表：新增归属服务商字段（若已存在该列请先跳过/删除后执行）
ALTER TABLE `eb_study_project`
  ADD COLUMN `service_provider_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务商id' AFTER `lecturer_ids`;

-- 3. 供应商账号（供应商端网站登录账号，后台代建）
CREATE TABLE IF NOT EXISTS `eb_service_provider_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '账号id',
  `provider_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '服务商id(eb_service_provider.id)',
  `account` varchar(64) NOT NULL DEFAULT '' COMMENT '登录账号',
  `pwd` varchar(255) NOT NULL DEFAULT '' COMMENT '密码hash',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `last_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(64) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_account` (`account`),
  KEY `idx_provider_id` (`provider_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='研学供应商账号';

-- 4. 后台菜单：服务商、供应商账号（pid 3465 为「研学」顶级菜单，请按实际库中 id 调整）
INSERT INTO `eb_system_menus`
  (`pid`,`icon`,`menu_name`,`module`,`controller`,`action`,`api_url`,`methods`,`params`,`sort`,`is_show`,`is_show_path`,`access`,`menu_path`,`path`,`auth_type`,`header`,`is_header`,`unique_auth`,`is_del`,`mark`)
VALUES
  (3465,'','服务商','admin','','','','','[]',5,1,1,1,'/study/service_provider','',1,'',0,'admin-study-service-provider',0,''),
  (3465,'','供应商账号','admin','','','','','[]',6,1,1,1,'/study/service_provider_account','',1,'',0,'admin-study-service-provider-account',0,'');
