import request from '@/utils/request'
// 发布作品
export function WorksAdd (params) {
  return request({
    method: 'post',
    url: '/api/works/worksadd',
    data: params
  })
}
// 我的作品列表
export function MyWorksList (params) {
  return request({
    method: 'get',
    url: '/api/works/myworkslist',
    params
  })
}
// 我的点赞列表
export function MyPraisesWorksList (params) {
  return request({
    method: 'get',
    url: '/api/works/mypraisesworkslist',
    params
  })
}
// 删除我的作品
export function DeleteMyWorks (params) {
  return request({
    method: 'get',
    url: '/api/works/deletemyworks',
    params
  })
}
// 未登录首页全部作品
export function GetWorksList (params) {
  return request({
    method: 'get',
    url: '/api/works/getworkslist',
    params
  })
}
// 已登录首页全部作品
export function GetMySchoolWorksList (params) {
  return request({
    method: 'get',
    url: '/api/works/getmyschoolworkslist',
    params
  })
}
// 选择学校后首页全部作品
export function GetSelectSchoolWorksList (params) {
  return request({
    method: 'get',
    url: '/api/works/getselectschoolworkslist',
    params
  })
}
// 点赞
export function WorkPraises (params) {
  return request({
    method: 'post',
    url: '/api/works/workpraises',
    data: params
  })
}
// 取消点赞
export function UnWorkPraises (params) {
  return request({
    method: 'post',
    url: '/api/works/unworkpraises',
    data: params
  })
}
