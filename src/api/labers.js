import request from '@/utils/request'
// 获取版块标签列表(首页)
export function GetHomeLaberList (params) {
  return request({
    method: 'get',
    url: '/api/laber/gethomelaberlist',
    params
  })
}
// 获取版块标签列表(发布)
export function GetAddLaberList (params) {
  return request({
    method: 'get',
    url: '/api/laber/getaddlaberlist',
    params
  })
}
