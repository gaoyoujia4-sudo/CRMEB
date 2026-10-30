// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import request from "@/utils/request.js";

/**
 * 研学抢购聚合列表
 * @param {Object} data { page, limit }
 */
export function snatchIndexApi(data) {
	return request.get("study/snatch/index", data);
}

/**
 * 研学商品场次列表
 * @param {Object} data { product_id }
 */
export function snatchListApi(data) {
	return request.get("study/snatch/list", data);
}

/**
 * 研学商品套餐列表（浏览，无需登录）
 * @param {Object} data { product_id, group_type, session_type }
 */
export function studyPackageListApi(data) {
	return request.get("study/package/list", data, {
		noAuth: true
	});
}

/**
 * 研学讲师列表
 */
export function lecturerListApi() {
	return request.get("lecturer/list");
}

/**
 * 研学项目列表（浏览，无需登录）
 * @param {Object} data { page, limit }
 */
export function studyProjectListApi(data) {
	return request.get("study/project/list", data, {
		noAuth: true
	});
}

/**
 * 研学项目详情（浏览，无需登录）
 * @param {Number} id
 */
export function studyProjectDetailApi(id) {
	return request.get("study/project/detail/" + id, {}, {
		noAuth: true
	});
}
