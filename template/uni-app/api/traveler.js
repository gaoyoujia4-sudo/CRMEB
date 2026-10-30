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
 * 常用出行人列表
 */
export function travelerList() {
	return request.get("traveler/list");
}

/**
 * 保存出行人
 * @param {Object} data { name, id_card, phone }
 */
export function travelerSave(data) {
	return request.post("traveler/save", data);
}

/**
 * 删除出行人
 * @param {Number} id
 */
export function travelerDel(id) {
	return request.post("traveler/del/" + id);
}
