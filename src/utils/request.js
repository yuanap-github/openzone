/* eslint-disable */
import axios from 'axios'
// import router from 'vue-router'
import Cookies from 'js-cookie'
import qs from 'qs'
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=UTF-8';
// 创建axios实例
const service = axios.create({
  baseURL: process.env.API_ROOT, // api 的 base_url
  timeout: 5000,// 请求超时时间

})
// request拦截器
service.interceptors.request.use((config) => {
  if(config.method  === 'post'){
    config.data = qs.stringify(config.data);
  }

  return config;
},(error) =>{
  return Promise.reject(error);
});


export default service