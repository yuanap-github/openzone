import request from '@/utils/request'
// 获取回复列表
export function GetReplysList (params) {
  return request({
    method: 'get',
    url: '/api/replys/getreplyslist',
    params
  })
}
// 回复发布
export function AddReplys (params) {
  return request({
    method: 'post',
    url: '/api/replys/addreplys',
    data: params
  })
}
