import request from '@/utils/request'
// touken校验
export function CheckToken (params) {
  return request({
    method: 'post',
    url: '/api/user/checktouken',
    data: params
  })
}
// 短信验证码发送
export function SendSms (params) {
  return request({
    method: 'post',
    url: '/api/user/sendsms',
    data: params
  })
}
// 短信验证码校验
export function CheckPhoneCode (params) {
  return request({
    method: 'post',
    url: '/api/user/checkphonecode',
    data: params
  })
}
// 修改个人信息
export function UpdateUserInfo (params) {
  return request({
    method: 'post',
    url: '/api/user/updateuserinfo',
    data: params
  })
}
// 校验昵称重复
export function CheckNickname (params) {
  return request({
    method: 'post',
    url: '/api/user/checknickname',
    data: params
  })
}
