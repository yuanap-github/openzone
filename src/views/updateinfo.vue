<template>
  <div class="updateinfo">
<van-nav-bar
  title="个人信息修改"
  left-text="返回"
  left-arrow
  @click-left="onClickLeft"
  @click-right="onClickRight"
  style='font-weight: bold;'
/>
<div class="mxs">
<div class="upload_div">
<van-uploader
  v-model="headportrait"
  multiple
  :max-count="1"
  :after-read="afterRead"
/>
</div>
 <p style="margin-top: 50px;">昵称:</p>
<van-cell-group>
 <van-field v-model="nickname" placeholder="请输入昵称" :maxlength='this.user_info.official != 1 ? 8:10'/>
</van-cell-group>
 <p style="margin-top:30px;">性别:</p>
<van-radio-group v-model="radio" v-bind:disabled="disabled">
  <van-radio name="1" checked-color="#07c160" @click="onRadio">男</van-radio>
  <van-radio name="2" checked-color="#07c160" @click="onRadio" style='margin-top: 5px;'>女</van-radio>
</van-radio-group>
<p style="margin-top:30px;"  v-if='this.user_info.official != 1'>学校:</p>
 <div class="school_select" @click="onField"  v-if='this.user_info.official != 1'>
      <van-popup v-model="showPicker" position="bottom">
        <van-picker
          show-toolbar
          title="学校选择"
          :columns="columns"
          @cancel="onCancel"
          @confirm="onConfirm"

        />
      </van-popup>
      <van-field
        readonly
        clickable
        :value="value.text"
        :placeholder="if_value"
        @click="showPicker = if_select"

      />
    </div>
<van-button round type="danger" size="large" @click="onSubmit">提交</van-button>
<!--     tabar结束 -->
</div>
<!-- 底部tabar -->
    <Tabbar />
<!--     tabar结束 -->
  </div>
</template>

<script>
import Tabbar from '@/components/Tabbar'
import { GetSchoolList } from '@/api/school.js'
import { CheckToken, UpdateUserInfo, CheckNickname } from '@/api/user.js'
export default {
  components: {
    Tabbar
  },
  data () {
    return {
      showPicker: false,
      if_user_login: false,
      if_select: true,
      user_info: [],
      nickname: '',
      school: '',
      value: '',
      headportrait: [],
      columns: [],
      list: [],
      radio: '1',
      file: null,
      if_value: '选择学校',
      disabled: false,
      school_id: null
    }
  },
  created () {
    if (this.$cookie.get('user_info')) {
      this.user_info = JSON.parse(this.$cookie.get('user_info'))
      this.checktoken()
      this.nickname = this.user_info.nickname
      this.school = this.user_info.school
      if (this.user_info.sex !== 0) {
        this.radio = String(this.user_info.sex)
        this.disabled = true
      }
      if (this.user_info.official === 1) {
        this.disabled = false
      }
      if (this.school.length !== 0) {
        this.if_select = false
        this.if_value = this.user_info.schoolname
      }
      this.headportrait = [{ url: this.user_info.headportrait }]
      const query = {}
      GetSchoolList(query).then(res => {
        if (res.data.status === 1) {
          this.columns = res.data.data
        }
      })
    } else {
      this.$router.push({path: '/mycenter', query: {}})
    }
  },
  methods: {
    onClickLeft () {
      this.$router.go(-1)
    },
    checktoken () {
      const querys = { user_id: this.user_info.id, token: this.user_info.token }
      CheckToken(querys).then(res => {
        if (res.data.status === 10003) {
          this.$cookie.set('user_info', this.user_info, -1)
          this.$router.push({path: '/mycenter', query: {}})
          return false
        } else {
          this.if_user_login = true
        }
      })
    },
    onClickRight () {
      alert(1)
    },
    onConfirm (value, index) {
      this.value = value
      this.showPicker = false
      this.if_value = value.text
    },
    onCancel () {
      this.showPicker = false
    },
    onClick (name, title) {
      this.$toast(title)
    },
    onRadio () {
      if (this.user_info.official !== 1) {
        this.$toast('性别提交后不可修改！')
      }
    },
    onField () {
      this.$toast('学校提交后不可修改！')
    },
    onSubmit () {
      const querys = { user_id: this.user_info.id, token: this.user_info.token }
      CheckToken(querys).then(res => {
        if (res.data.status === 10003) {
          this.$cookie.set('user_info', this.user_info, -1)
          this.show_sheet_login = true
          return false
        } else if (res.data.status === 10007) {
          this.if_user_login = false
          this.$cookie.set('user_info', this.user_info, -1)
          this.$router.push({path: '/mycenter', query: {}})
          this.$toast.fail(res.data.msg)
          return false
        } else {
          this.checktoken()
          if (this.user_info.official !== 1) {
            if (this.if_value === '选择学校') {
              this.$toast('学校不能为空！')
              return
            }
          }
          if (this.nickname.length === 0) {
            this.$toast('昵称不能为空！')
            return
          }
          const query = { nickname: this.nickname, token: this.user_info.token, user_id: this.user_info.id }
          CheckNickname(query).then(res => {
            if (res.data.status === 1) {
              if (res.data.data === false) {
                this.$toast('昵称重复！')
              } else {
                this.$dialog.confirm({
                  title: '确认修改提示',
                  message: '性别和学校提交后不可修改！'
                }).then(() => {
                  if (this.value.id == null) {
                    this.school_id = this.user_info.school
                  } else {
                    this.school_id = this.value.id
                  }
                  const query = { file: this.file, nickname: this.nickname, sex: this.radio, school_id: this.school_id, token: this.user_info.token, user_id: this.user_info.id }
                  UpdateUserInfo(query).then(res => {
                    if (res.data.status === 1) {
                    // this.columns = res.data.data
                      this.$cookie.set('user_info', JSON.stringify(res.data.data))
                      this.user_info = JSON.parse(this.$cookie.get('user_info'))
                      this.nickname = this.user_info.nickname
                      this.school = this.user_info.school
                      if (this.user_info.sex !== 0) {
                        this.radio = String(this.user_info.sex)
                      }
                      if (this.school.length !== 0) {
                        // this.if_select = false
                        this.if_select = true
                      }
                      this.headportrait = [{ url: this.user_info.headportrait }]
                      this.$toast.success('修改成功！')
                      this.$router.push({path: '/mycenter', query: {}})
                    }
                  })
                }).catch(() => {
                // on cancel
                })
              }
            }
          })
        }
      })
    },
    replace_https (urlstr) {
      return urlstr.replace(/^http/, 'https')
    },
    afterRead (file) {
      // 此时可以自行将文件上传至服务器
      this.imgPreview(file)
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
            self.compress(img, 0.4, files)
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
      let ndata = canvas.toDataURL('image/jpeg', size)
      console.log('*******压缩后的图片大小*******')
      // console.log(ndata)
      console.log(ndata.length / 1024)
      files.content = ndata
      this.file = files
      // this.file.data.push(files)
      // this.last_file.data.push({ content: ndata, id: id })
    }
  }
}
</script>
<style>
.mxs{
  width: 60%;margin: auto;
}
.upload_div{
  width: 80px;height: 80px;margin: auto;
}
.van-button{
  margin-top:50px;
}
.van-image__img,.van-uploader__upload{
    border-radius: 80px;
}
.updateinfo .van-cell {
    line-height: 10px;
}
.updateinfo .van-cell {
 padding: 0px;
}
.school_select .van-field{
   color: black
  }
.school_select .van-field{
    color: black;
}
.school_select .van-field__control{
    font-size: 15px;
    color: black
  }
 .updateinfo .van-field__body input::-webkit-input-placeholder {
  color: #c8c9cc
}
 .updateinfo .van-field__body input::-moz-input-placeholder {
   color: #c8c9cc
}

 .updateinfo .van-field__body input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color: #c8c9cc
}
 .updateinfo .van-field__body input::-ms-input-placeholder {
   color: #c8c9cc
}
</style>
