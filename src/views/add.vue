<template>
  <div class="add_box">
    <van-nav-bar
    title="发布"

    @click-left="onClickLeft"
    />
   <div class="input">
      <van-cell-group>
      <van-field
        v-model="inputvalue"
        type="textarea"
        placeholder="请输入留言"
        rows="6"
        autosize
        @input="fontNumber()"
         :maxlength="fontmaxlength"
      />
      </van-cell-group>
      <p class="font_number">({{fontlength}} / {{fontmaxlength}})</p>
</div>
<van-uploader
:before-read="beforeRead"
 v-model="fileList"
:max-count="img_max_count"
multiple
upload-text="上传图片"
:after-read="afterRead"
@delete="deletefile"
accept="image/*"
/>
<van-radio-group v-model="radio">
  <van-radio :name="item.id" checked-color="#07c160" v-for="item in labers_list" v-bind:key="item.id" style='float: left;margin: 5px;' @click="Introduce(item.introduce)">{{item.labername}}</van-radio>
</van-radio-group>
<p style="clear: both;width: 100%;margin: auto;color: #969799;font-size: 16px;margin-top: 40px;">{{introduce_value}}</p>
<van-button round type="danger" size="large" @click="onSubmit">发布</van-button>
 <van-action-sheet v-model="show_sheet_login" title="登录">
     <div class="mxs">
    <van-cell-group>
<van-field
    v-model="phone"
    label="手机号"
    placeholder="请输入手机号"
  />
   <van-field
    v-model="sms"
    center
    clearable
    label="短信验证码"
    placeholder="请输入验证码"
  >
    <van-button :disabled='disabled_button' slot="button" size="small" type="primary" style='margin-top: 0px;margin-top: 0px;border: 1px solid red;
background: red;border-radius: 30px;' @click="sendSms()">{{code_message_button}}</van-button>
  </van-field>
</van-cell-group>
<van-button round type="danger" size="large" @click="checkphoneCode()" style='margin-bottom: 30px;'>登录</van-button>
</div>
</van-action-sheet>

<!-- 底部tabar -->
    <Tabbar />
<!--     tabar结束 -->
  </div>
</template>
<script>
import Tabbar from '@/components/Tabbar'
import { CheckToken, SendSms, CheckPhoneCode } from '@/api/user.js'
import { WorksAdd } from '@/api/works.js'
import { GetAddLaberList } from '@/api/labers.js'
export default {
  components: {
    Tabbar
  },
  data () {
    return {
      value: '',
      phone: '',
      sms: '',
      code_message_button: '发送验证码',
      disabled_button: false,
      showPicker: false,
      show_sheet_login: false,
      fileList: [],
      inputvalue: '',
      if_user_login: false,
      user_info: [],
      fontlength: 0,
      fontmaxlength: 200,
      files: { name: '', type: '' },
      headerImage: null,
      picValue: null,
      upImgUrl: null,
      file: { data: [] },
      last_file: { data: [] },
      img_type: ['image/jpeg', 'image/png', 'image/gif'],
      add_switch: false,
      submit_input: null,
      add_status: false,
      radio: 1,
      labers_list: [],
      introduce_value: '',
      img_max_count: 4,
      official_status: 0
    }
  },
  created () {
    if (this.$cookie.get('user_info')) {
      this.user_info = JSON.parse(this.$cookie.get('user_info'))
      if (this.user_info.official === 1) {
        this.fontmaxlength = 1000
        this.img_max_count = 8
        this.official_status = 1
      }
      this.if_user_login = true
    }
    const query = { official: this.official_status }
    GetAddLaberList(query).then(res => {
      if (res.data.status === 1) {
        this.labers_list = res.data.data
        this.introduce_value = res.data.data[0].introduce
        this.radio = res.data.data[0].id
      }
    })
  },
  methods: {
    // 发布提交
    onSubmit () {
      if (!this.if_user_login) {
        this.$toast('请先登录！')
        this.show_sheet_login = true
      } else {
        const querys = { user_id: this.user_info.id, token: this.user_info.token }
        CheckToken(querys).then(res => {
          if (res.data.status === 10003) {
            this.$cookie.set('user_info', this.user_info, -1)
            this.show_sheet_login = true
            return false
          } else if (res.data.status === 10007) {
            this.if_user_login = false
            this.$cookie.set('user_info', this.user_info, -1)
            this.$toast.fail(res.data.msg)
            return false
          } else {
            if (this.user_info.official === 0) {
              if (this.user_info.schoolname == null) {
                this.$toast.fail('选择学校之后才能发布哦！')
                this.$router.push({path: '/updateinfo', query: {}})
                return false
              }
            }
            this.submit_input = this.inputvalue.length + this.file.data.length
            if (this.submit_input === 0) {
              this.$toast('留言和图片必须填写一个！')
              return false
            }
            // if (this.inputvalue.length === 0) {
            //   this.$toast('请输入留言！')
            //   return
            // }
            // if (this.file.data.length === 0) {
            //   this.$toast('请上传图片！')
            //   return
            // }
            if (!this.add_switch) {
              this.add_switch = true
              const query = { content: this.inputvalue, file: this.file, token: this.user_info.token, user_id: this.user_info.id, laber_id: this.radio }
              this.$toast.loading({
                mask: true,
                message: '发布中'
              })
              WorksAdd(query).then(res => {
                if (res.data.status === 1) {
                  this.add_switch = false
                  this.add_status = true
                } else {
                  this.add_switch = false
                  this.add_status = false
                  this.$toast.fail('发布失败！')
                  return false
                }
              })
              const toast = this.$toast.loading({
                duration: 0,
                forbidClick: true,
                loadingType: 'spinner',
                message: '发布中'
              })
              let second = 8
              const timer = setInterval(() => {
                second--
                if (second) {
                  // toast.message = time + '/-已完成' + seconds * percentage + '%'
                  toast.message = '发布中'
                } else {
                  this.add_status = true
                  clearInterval(timer)
                  this.$toast.clear()
                  if (this.add_status) {
                    this.add_switch = false
                    this.add_status = false
                    this.$toast.success('发布成功！')
                    this.$router.push({path: '/', query: {}})
                  }
                }
              }, 1000)
            }
          }
        })
      }
    },
    Introduce (introduce) {
      this.introduce_value = introduce
    },
    // 判断字数
    fontNumber () {
      this.fontlength = this.inputvalue.length
      if (this.inputvalue.length > 200) {
        this.fontlength = 200
      }
    },
    onClickLeft () {
      window.history.go(-1)
    },
    // 判断图片类型
    beforeRead (file) {
      if (file.length != null) {
        for (var i = 0; i < file.length; i++) {
          if (this.img_type.indexOf(file[i].type) === -1) {
            this.$toast('请上传正确图片格式!')
            return false
          }
        }
      } else {
        if (this.img_type.indexOf(file.type) === -1) {
          this.$toast('请上传正确的图片')
          return false
        }
      }
      return true
    },
    replace_https (urlstr) {
      return urlstr.replace(/^http/, 'https')
    },
    // 发送验证码
    sendSms () {
      if (!(/^1[34578]\d{9}$/.test(this.phone))) {
        if (this.phonelength === 0) {
          this.$toast.fail('请输入手机号！')
        } else {
          this.$toast.fail('手机格式错误！')
        }
      } else {
        let second = 60
        const timer = setInterval(() => {
          second--
          if (second) {
            this.disabled_button = true
            this.code_message_button = second + '秒'
          } else {
            this.disabled_button = false
            clearInterval(timer)
            this.code_message_button = '发送验证码'
          }
        }, 1000)
        const query = { 'phone': this.phone }
        SendSms(query).then(res => {
          if (res.data.status === 0) {
            this.$toast.fail('操作频繁,发送失败!')
            this.disabled_button = false
            clearInterval(timer)
            this.code_message_button = '发送验证码'
          }
        })
      }
    },
    // 验证码验证
    checkphoneCode () {
      if (this.sms.length === 0) {
        this.$toast.fail('请输入验证码！')
      } else {
        const query = { 'phone': this.phone, 'phonecode': this.sms }
        CheckPhoneCode(query).then(res => {
          if (res.data.status === 1) {
            this.$cookie.set('user_info', JSON.stringify(res.data.data))
            this.user_info = JSON.parse(this.$cookie.get('user_info'))
            if (this.user_info.official === 1) {
              this.fontmaxlength = 1000
              this.img_max_count = 8
              this.official_status = 1
            }
            this.show_sheet_login = false
            this.if_user_login = true
            this.$toast.success('登录成功！')
            const query = { official: this.official_status }
            GetAddLaberList(query).then(res => {
              if (res.data.status === 1) {
                this.labers_list = res.data.data
                this.introduce_value = res.data.data[0].introduce
                this.radio = res.data.data[0].id
              }
            })
          } else {
            this.show_sheet_login = false
            this.$toast.fail(res.data.msg)
          }
          console.log(res)
        })
      }
    },
    // 文件赋值
    afterRead (file) {
      if (file.length != null) {
        for (var i = 0; i < file.length; i++) {
          this.imgPreview(file[i])
        }
      } else {
        this.imgPreview(file)
      }
    },
    // 删除文件
    deletefile (file) {
      console.log(this.file.data)
      for (var i = 0; i < this.file.data.length; i++) {
        if (this.file.data[i].__ob__.dep.id === file.__ob__.dep.id) {
          this.file.data.splice(i, 1)
        }
      }
      console.log(this.file.data)
    },
    // 获取图片
    imgPreview (files) {
      this.text1 = '正在压缩图片'
      let self = this
      // 判断支不支持FileReader
      if (!files.file || !window.FileReader) return false
      if (/^image/.test(files.file.type)) {
        // 创建一个reader
        let reader = new FileReader()
        // 将图片转成base64格式
        reader.readAsDataURL(files.file)
        // 读取成功后的回调
        reader.onloadend = function () {
          let result = this.result
          let img = new Image()
          img.src = result
          console.log('********未压缩前的图片大小********')
          console.log(result.length / 1024)
          img.onload = function () {
            self.compress(img, 0.3, files)
          }
        }
      }
    },
    // 压缩图片
    compress (img, size, files) {
      let canvas = document.createElement('canvas')
      let ctx = canvas.getContext('2d')
      // let initSize = img.src.length`
      let width = img.width
      let height = img.height
      canvas.width = width
      canvas.height = height
      // 铺底色
      ctx.fillStyle = '#fff'
      ctx.fillRect(0, 0, canvas.width, canvas.height)
      ctx.drawImage(img, 0, 0, width, height)
      // 进行最小压缩
      let ndata = null
      // if (files.file.type === 'image/jpeg') {
      ndata = canvas.toDataURL('image/jpeg', size)
      // }
      // if (files.file.type === 'image/gif') {
      //   ndata = canvas.toDataURL('image/gif', size)
      // }
      // if (files.file.type === 'image/png') {
      //   ndata = canvas.toDataURL('image/png', size)
      // }
      console.log('*******压缩后的图片大小*******')
      console.log(files.file.type)
      console.log(ndata.length / 1024)
      files.content = ndata
      this.file.data.push(files)
      // this.last_file.data.push({ content: ndata, id: id })
    }
  }
}
</script>
<style>
.mxs{
  width: 100%;margin: auto;margin-top: 10px;
}
.input .van-field__control{
text-align: left;
}
  .van-field{
    background: #fbfbfb;color: #7d7e80;
  }
  .input{
    margin-top: 20px;
  }
  .van-ellipsis{
    font-weight: bold;
  }
  /*.van-uploader__preview-image{
    width: 100%;
    height: 100%;
    border-radius: none;
  }*/
  .van-checkbox{
    width: 20%;float: left;margin-top: 5px;
  }
.add_box{
  width: 95%;height:auto;margin: auto;margin-bottom: 100px;
}
.add_box .van-uploader__upload{
  border-radius: 0px;
}
.add_box .van-image__img, .add_box .van-uploader__upload{
  border-radius: 0px;
}
.font_number{
  float: right;
}
.van-uploader{
  margin-top: 30px
}
.van-button{
  margin-top:50px;
}
.mxs .van-field__control{
    text-align: left;
}
</style>
