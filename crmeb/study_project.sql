-- 研学模块新增：研学项目
-- 执行库：crmeb31（表前缀 eb_）
-- 说明：研学项目 = 名称/介绍/封面图/价格类型(按人·按团)/价格/多讲师 的管理表

-- 1. 研学项目表
CREATE TABLE IF NOT EXISTS `eb_study_project` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '项目id',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '项目名称',
  `intro` text COMMENT '项目介绍',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `price_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '价格类型 1按人计价 2按团计价',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '价格(按人=每人价/按团=整团价)',
  `ot_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT '划线价/原价',
  `lecturer_ids` varchar(255) NOT NULL DEFAULT '' COMMENT '讲师id集合(JSON数组)',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除 0否 1是',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_is_del` (`is_del`),
  KEY `idx_sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='研学项目';

-- 2. 后台菜单：研学项目（pid 3465 为「研学」顶级菜单）
INSERT INTO `eb_system_menus`
  (`pid`,`icon`,`menu_name`,`module`,`controller`,`action`,`api_url`,`methods`,`params`,`sort`,`is_show`,`is_show_path`,`access`,`menu_path`,`path`,`auth_type`,`header`,`is_header`,`unique_auth`,`is_del`,`mark`)
VALUES
  (3465,'','研学项目','admin','','','','','[]',4,1,1,1,'/study/study_project','',1,'',0,'admin-study-study-project',0,'');
