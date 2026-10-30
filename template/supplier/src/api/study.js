import request from '@/libs/request';

/**
 * 我的研学项目列表
 */
export function projectList(data) {
  return request({
    url: 'study_project',
    method: 'get',
    params: data,
  });
}

/**
 * 我的研学项目详情
 */
export function projectDetail(id) {
  return request({
    url: `study_project/${id}`,
    method: 'get',
  });
}
