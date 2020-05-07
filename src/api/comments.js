import request from '@/utils/request'
// 获取评论列表
export function GetCommentsList (params) {
  return request({
    method: 'get',
    url: '/api/comments/getcommentslist',
    params
  })
}
// 评论发布
export function AddComments (params) {
  return request({
    method: 'post',
    url: '/api/comments/addcomments',
    data: params
  })
}
