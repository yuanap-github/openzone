import request from '@/utils/request'
// 获取学校列表
export function GetSchoolList (params) {
  return request({
    method: 'get',
    url: '/api/school/getschoollist',
    params
  })
}
