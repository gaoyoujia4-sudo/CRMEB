-- 研学项目关联已有商品：打通「研学项目 → 商品」购买链路
-- 执行方式：mysql -u root -p crmeb < study_project_link.sql （或 navicat 直接运行）

ALTER TABLE eb_study_project ADD COLUMN product_id int unsigned NOT NULL DEFAULT 0 COMMENT '关联商品id(eb_store_product.id)' AFTER service_provider_id;
