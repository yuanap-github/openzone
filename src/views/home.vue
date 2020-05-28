<template>
  <div>
 <!--    顶部学校选择 -->
    <van-sticky :offset-top="0">
    <div class="school_select_home">
      <van-popup v-model="showPicker" position="bottom">
        <van-picker
          show-toolbar
          :columns="columns"
          @cancel="showPicker = false"
          @confirm="onConfirm"
          @change="onChange"

        />
      </van-popup>
      <van-field
        readonly
        clickable
        :value="value.text"
        :placeholder="if_value"
        @click="showPicker = true"
      />
    </div>
    </van-sticky>
    <div class="main_tabs">
      <van-tabs @click="onClick"  title-active-color='white' type="line">
        <van-tab :title="item.labername" v-for="item in labers_list" v-bind:key="item.id">
          <van-pull-refresh v-model="isLoading" @refresh="onRefresh" style='z-index: 100;'>
          <van-list
            v-model="loading"
            :finished="finished"
            finished-text="没有更多了"
            @load="works_function"
          >
          <div class="list_info"  v-for="item in workslist" v-bind:key="item.id">
         <van-cell>
             <template slot="title">
              <img :src="item.headportrait" style="width: 30px;height: 30px;float: left;border-radius: 30px;">
               <van-tag type="danger"  v-if='item.official === 1' style='margin-left: 10px;'>{{item.nickname}}</van-tag>
              <span v-if='item.official!== 1' style='margin-left: 10px;'>{{item.nickname}}</span>
                <span :style="item.sex === 1?'font-size: 13px;color:#1296db;':'font-size: 13px;color:#ec1139'" v-if='item.official!==1'>{{item.sex === 1? '男':'女'}}</span>
            </template>
               <span class="add_time">{{timeago(getDateTimeStamp(item.created_at))}}</span>
            <van-icon
              slot="right-icon"
              style="line-height: inherit;"
            />
        </van-cell>
        <span class="content_font">{{item.content}}</span>
        <img :src="item.img_path[0]" @click="showImg(item.img_path)" class="show_img">
          <div class="list_operation">
            <van-row type="flex" justify="space-around" class='operation_box'>

              <van-col span="6"><van-icon :name="item.praise? 'like':'like-o'" size='30' class='praise' @click="praiseAction(item)" :color="item.praise? 'red':''"/></van-col><span class="praise_number" @click="praiseAction(item)">{{item.praise_number}}</span>
              <span style="margin: auto">
              <van-col span="6"><van-icon name="eye-o" size='30'  @click="messagesAction()"/></van-col><span style="position: relative;left: 30px;line-height: 30px;font-size: 14px">{{item.browse_number}}</span>
              </span>
              <span @click="commentShow(item)">
              <van-col span="6"><van-icon name="comment-circle-o
  " style='float: right;' size='30' class='comment'/></van-col><span class="comment_number">{{item.comments_count}}</span></span>
            </van-row>
          </div>
        </div>
      </van-list>
      </van-pull-refresh>
        </van-tab>
      </van-tabs>
    </div>
<!-- 评论列表 -->
  <van-action-sheet v-model="show_sheet" title="评论" @cancel='cancelcomment' @click='placeholdercancel' @click-overlay ='onoverlay'>
   <van-cell>
    <template slot="title">
      <img :src="comment_work.headportrait" style="width: 30px;height: 30px;float: left;border-radius: 30px;">
       <van-tag type="danger"  v-if='comment_work.official === 1' style='margin-left: 10px;'>{{comment_work.nickname}}</van-tag>
              <span v-if='comment_work.official!== 1' style='margin-left: 10px;'>{{comment_work.nickname}}</span>
                <span :style="comment_work.sex === 1?'font-size: 12px;color:#1296db;':'font-size: 12px;color:#ec1139'" v-if='comment_work.official!== 1'>{{comment_work.sex === 1? '男':'女'}}</span>
            </template>
        <span class="add_time">{{comment_work.created}}</span>
          <van-icon
              slot="right-icon"
              style="line-height: inherit;"
            />
    </van-cell>

    <span class="content_font">{{comment_work.content}}</span>
    <img :src="comment_work.img_path[0]" @click="showImg(comment_work.img_path)" class="show_img" v-if='comment_img'>
        <van-divider>所有评论</van-divider>
        <p style="text-align: center;color: #969799;font-size: 12px;" v-show='dividers'>暂无评论</p>
        <div style="" class="comment_div">
          <van-list
            v-model="comments_loading"
            :finished="comments_finished"
            finished-text="没有更多了"
            @load="getcommentlist"
          >
          <div class="comment_info" :key="item.comments_id" v-for="item in commentslist">
            <img :src="item.user_headportrait" style="width: 30px;height: 30px;border-radius: 30px;float: left;margin-left: 15px;"><div style="width: 87%;;height: 70px;float: right;margin-top: 5px">
             <van-tag type="danger"  v-if='item.official === 1'  style='margin-left: 10px;'>{{item.user_nickname}}</van-tag>
                 <span v-if='item.official!== 1'  style='margin-left: 10px;'>{{item.user_nickname}}</span>
               <span :style="item.user_sex === 1?'font-size: 12px;color:#1296db;':'font-size: 12px;color:#ec1139'" v-if='item.official!== 1'>{{item.user_sex === 1? '男':'女'}}</span>
               <span style="float: right;margin-right: 15px;font-size: 12px;color: #7d7e80;">{{timeago(getDateTimeStamp(item.comments_time))}}</span>
              <p class="comment_font">{{item.comments_text}}</p>

             <van-button round type="danger" size="mini" style='background: #f4f4f4;color: black;border: none;margin-top: 0px;padding: 0 10px;line-height: 22px' @click="replyAction(item)" @click.stop v-show='item.replys_count === 0'>回复</van-button>
             <van-button round type="danger" size="mini" style='background: #f4f4f4;color: black;border: none;margin-top: 0px;padding: 0 10px;line-height: 22px' @click="replylistAction(item)" @click.stop v-show='item.replys_count > 0'>{{item.replys_count}}条回复 ></van-button>
            </div>
          </div>
          </van-list>
     <!--  </van-pull-refresh> -->
        </div>
</van-action-sheet>
<!-- 评论列表结束 -->
<!-- 回复列表 -->
  <van-action-sheet v-model="replys_show_sheet" title="回复" @cancel='cancelreplys'>
       <van-cell>
        <template slot="title">
           <img :src="replys_work.user_headportrait" style="width: 30px;height: 30px;float: left;border-radius: 30px;">
          <van-tag type="danger"  v-if='replys_work.official === 1' style='margin-left: 10px;'>{{replys_work.user_nickname}}</van-tag>
              <span v-if='replys_work.official!== 1' style='margin-left: 10px;'>{{replys_work.user_nickname}}</span>
                <span :style="replys_work.user_sex === 1?'font-size: 12px;color:#1296db;':'font-size: 12px;color:#ec1139'" v-if='replys_work.official!== 1'>{{replys_work.user_sex === 1? '男':'女'}}</span>
            </template>
        <span class="add_time">{{replys_work.created}}</span>
          <van-icon
              slot="right-icon"
              style="line-height: inherit;"
            />
    </van-cell>
    <span class="content_font">{{replys_work.comments_text}}</span>
   <!--  <img :src="comment_work.img_path[0]?1:2" @click="showImg(comment_work.img_path)"> -->
        <van-divider>所有回复</van-divider>
        <p style="text-align: center;color: #969799;font-size: 12px;" v-show='dividers'>暂无评论</p>
        <div style="" class="comment_div">
        <!--   <van-pull-refresh v-model="isLoading" @refresh="onRefresh" style='z-index: 100;'> -->
          <van-list
            v-model="comments_loading"
            :finished="comments_finished"
            finished-text="没有更多了"
            @load="getcommentlist"
          >
          <div class="comment_info" :key="item.replys_id" v-for="item in replyslist">
            <img :src="item.user_headportrait" style="width: 30px;height: 30px;border-radius: 30px;float: left;margin-left: 15px;"><div style="width: 87%;;height: 70px;float: right;margin-top: 5px">
               <van-tag type="danger"  v-if='item.official === 1'  style='margin-left: 10px;'>{{item.user_nickname}}</van-tag>
                 <span v-if='item.official!== 1'  style='margin-left: 10px;'>{{item.user_nickname}}</span>
               <span :style="item.user_sex === 1?'font-size: 12px;color:#1296db;':'font-size: 12px;color:#ec1139'" v-if='item.official!== 1'>{{item.user_sex === 1? '男':'女'}}</span>
               <span style="float: right;margin-right: 15px;font-size: 12px;color: #7d7e80;">{{timeago(getDateTimeStamp(item.replys_time))}}</span>
              <p class="comment_font">{{item.replys_text}}</p>
            </div>
          </div>
          </van-list>
     <!--  </van-pull-refresh> -->
        </div>
</van-action-sheet>
<!-- 回复列表结束 -->
<div style="width: 100%;height: 50px;position: fixed;bottom: 0px;background: white;z-index: 10000" v-show='comments_box'>
  <input type="text" name="comments_text" style="height: 30px;width: 60%;border: 1px solid #f8f8f8;margin-left: 20px;margin-bottom: 5px;margin-top: 8px;text-indent:2px;" v-model="comments_text" :placeholder="placeholder.placeholder" id="input"  @input="fontNumber()" @click.stop maxlength='50'>{{fontlength}}/50<button style="border: 1px solid #f44;background: #f44;border-radius:4px;margin-left: 10px;color:white;height: 30px;font-size: 13px;float: right;margin-top: 10px;margin-right: 5px;width: 60px;"  @click="commentsAction()">发布</button>
</div>
<!-- 登录框 -->
<van-image-preview
  v-model="show_img"
  :images="images"
  @change="onChange"
>
</van-image-preview>
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
    <van-button :disabled='disabled_button' slot="button" size="small" type="primary" style='margin-top: 0px;border: 1px solid red;
background: red;border-radius: 30px;' @click="sendSms()">{{code_message_button}}</van-button>
  </van-field>
</van-cell-group>
<van-button round type="danger" size="large" @click="checkphoneCode()" style='margin-bottom: 30px;'>登录</van-button>
</div>
<!-- 登录框结束 -->
</van-action-sheet>
<!-- 底部tabar -->
    <Tabbar />
<!--     tabar结束 -->
  </div>
</template>

<script>
import Tabbar from '@/components/Tabbar'
import { CheckToken, SendSms, CheckPhoneCode } from '@/api/user.js'
import { GetWorksList, GetMySchoolWorksList, GetSelectSchoolWorksList, WorkPraises, UnWorkPraises } from '@/api/works.js'
import { GetSchoolList } from '@/api/school.js'
import { GetHomeLaberList } from '@/api/labers.js'
import { GetCommentsList, AddComments } from '@/api/comments.js'
import { GetReplysList, AddReplys } from '@/api/replys.js'
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
      columns: [],
      list: [],
      loading: false,
      finished: false,
      comments_loading: false,
      comments_finished: false,
      replys_loading: false,
      replys_finished: false,
      if_no_user_login: false,
      if_user_login: false,
      show_sheet_login: false,
      replys_show_sheet: false,
      workslist: [],
      show_sheet: false,
      show_img: false,
      index: 0,
      images: [],
      user_info: [],
      divider: false,
      if_value: '选择学校',
      works_page_number: 0,
      isLoading: false,
      crest: false,
      labers_list: [],
      laber_id: null,
      like: 'like-o',
      like_switch: false,
      comment_work: [],
      replys_work: [],
      comments_box: false,
      comments_text: '',
      commentslist: [],
      replyslist: [],
      dividers: true,
      replys_text: '',
      placeholder: [],
      replyinfo: [],
      my_reply_input: '',
      comments_page_number: 0,
      replys_page_number: 0,
      fontlength: 0,
      comment_img: false
    }
  },
  // mounted () {
  //   // window.onresize监听页面高度的变化
  //   window.onresize = () => {
  //     return (() => {
  //       this.showHeight = document.body.clientHeight
  //     })()
  //   }
  // },
  // watch: {
  //   showHeight: function () {
  //     if (this.docmHeight > this.showHeight) {
  //       alert('弹开')
  //       this.hidshow = false
  //     } else {
  //       alert('关闭')
  //       this.hidshow = true
  //     }
  //   }
  // },
  created () {
    const query = {}
    GetSchoolList(query).then(res => {
      if (res.data.status === 1) {
        this.columns = res.data.data
      }
    })
    const querys = {}
    GetHomeLaberList(querys).then(res => {
      if (res.data.status === 1) {
        this.labers_list = res.data.data
        this.laber_id = this.labers_list[0].id
      }
    })
    if (this.$cookie.get('user_info')) {
      this.user_info = JSON.parse(this.$cookie.get('user_info'))
      const query = { user_id: this.user_info.id, token: this.user_info.token }
      CheckToken(query).then(res => {
        if (res.data.status === 10003) {
          this.$cookie.set('user_info', this.user_info, -1)
          this.show_sheet_login = true
          // this.getworkslist()
          return false
        } else {
          if (this.user_info.school.length !== 0 && this.user_info.school !== '0') {
            this.if_value = this.user_info.schoolname
          }
          this.if_user_login = true
        }
      })
    } else {
      this.if_user_login = false
      // this.onLoad
    }
  },
  methods: {
    onLoad () {
      if (this.value.id != null) {
        this.crest = true
        this.getselectschoolworkslist()
      } else {
        if (this.$cookie.get('user_info')) {
          this.crest = true
          this.getmyschoolworkslist()
        } else {
          this.getworkslist()
        }
      }
    },
    works_function (functionname) {
      if (functionname == null) {
        this.onLoad()
      } else if (functionname === 'getselectschoolworkslist') {
        this.getselectschoolworkslist()
      }
    },
    // 下拉刷新
    onRefresh () {
      this.workslist = []
      this.works_page_number = 0
      this.onLoad()
      setTimeout(() => {
        this.$toast('刷新成功')
        this.isLoading = false
      }, 1000)
    },
    onoverlay () {
      this.comments_box = false
    },
    // 未登录展示数据
    getworkslist () {
      // 异步更新数据
      setTimeout(() => {
        this.works_page_number = this.works_page_number + 1
        const query = { page: this.works_page_number, laber_id: this.laber_id }
        GetWorksList(query).then(res => {
          if (res.data.status === 1) {
            if (res.data.data.data.length === 0) {
              this.divider = true
              this.workslist = res.data.data.data
              this.loading = false
              this.finished = true
            } else {
              this.divider = false
              // this.workslist = res.data.data
              for (let i = 0; i < res.data.data.data.length; i++) {
                this.workslist.push(res.data.data.data[i])
              }
              // 加载状态结束
              this.loading = false
              // 数据全部加载完成
              if (this.workslist.length >= res.data.data.total) {
                this.finished = true
              }
            }
          }
        })
      }, 500)
    },
    // 已登录展示数据
    getmyschoolworkslist () {
      // 异步更新数据
      setTimeout(() => {
        this.works_page_number = this.works_page_number + 1
        const query = { user_id: this.user_info.id, token: this.user_info.token, page: this.works_page_number, laber_id: this.laber_id }
        GetMySchoolWorksList(query).then(res => {
          if (res.data.status === 1) {
            if (res.data.data.data.length === 0) {
              this.divider = true
              this.workslist = res.data.data.data
              this.loading = false
              this.finished = true
            } else {
              this.divider = false
              // this.workslist = res.data.data
              for (let i = 0; i < res.data.data.data.length; i++) {
                this.workslist.push(res.data.data.data[i])
              }
              // 加载状态结束
              this.loading = false
              // 数据全部加载完成
              if (this.workslist.length >= res.data.data.total) {
                this.finished = true
              }
            }
          }
        })
      }, 500)
    },
    // 选择学校展示数据
    getselectschoolworkslist () {
      // 异步更新数据
      setTimeout(() => {
        this.works_page_number = this.works_page_number + 1
        let query = null
        if (this.if_user_login) {
          query = { school_id: this.value.id, page: this.works_page_number, laber_id: this.laber_id, user_id: this.user_info.id, token: this.user_info.token }
        } else {
          query = { school_id: this.value.id, page: this.works_page_number, laber_id: this.laber_id }
        }
        GetSelectSchoolWorksList(query).then(res => {
          if (res.data.status === 1) {
            if (res.data.data.data.length === 0) {
              this.divider = true
              this.workslist = res.data.data.data
              this.loading = false
              this.finished = true
            } else {
              this.divider = false
              // this.workslist = res.data.data
              for (let i = 0; i < res.data.data.data.length; i++) {
                this.workslist.push(res.data.data.data[i])
              }
              // 加载状态结束
              this.loading = false
              // 数据全部加载完成
              if (this.workslist.length >= res.data.data.total) {
                this.finished = true
              }
            }
          }
        })
      }, 500)
    },
    // 学校选择赋值
    onConfirm (value) {
      this.loading = false
      this.finished = false
      this.value = value
      this.showPicker = false
      this.workslist = []
      this.works_page_number = 0
      // this.works_function('getselectschoolworkslist')
      // this.onLoad()
    },
    // 版块标签切换
    onClick (name, title) {
      this.laber_id = this.labers_list[name].id
      this.switch = true
      this.loading = false
      this.finished = false
      this.workslist = []
      this.works_page_number = 0
      // this.works_function()
    },
    // 评论展示
    commentShow (info) {
      this.placeholder = { placeholder: '说点什么吧...', type: ' comment', info: null }
      this.comment_work = info
      this.replyinfo = []
      this.comment_work.created = this.timeago(this.getDateTimeStamp(this.comment_work.created_at))
      console.log(this.comment_work.img_path[0])
      this.show_sheet = true
      this.comment_img = true
      this.comments_box = this.show_sheet
      this.getcommentlist()
      this.commentslist = []
      this.comments_page_number = 0
      this.comments_loading = false
      this.comments_finished = false
      document.getElementById('input').focus()
    },
    // 评论列表异步接口
    getcommentlist () {
      setTimeout(() => {
        this.comments_page_number = this.comments_page_number + 1
        const query = { works_id: this.comment_work.id, page: this.comments_page_number }
        GetCommentsList(query).then(res => {
          if (res.data.status === 1) {
            if (res.data.data.length === 0) {
              this.dividers = true
            } else {
              // console.log(res.data.data.total)
              for (let i = 0; i < res.data.data.data.length; i++) {
                this.commentslist.push(res.data.data.data[i])
              }
              // 加载状态结束
              this.comments_loading = false
              // 数据全部加载完成
              if (this.commentslist.length >= res.data.data.total) {
                this.comments_finished = true
              }
              this.dividers = false
            }
            // console.log(this.comment_work.img_path[0])
            // this.commentslist = res.data.data.data
          }
        })
      }, 500)
    },
    replace_https (urlstr) {
      return urlstr.replace(/^http/, 'https')
    },
    // 回复列表异步接口
    getreplyslist () {
      setTimeout(() => {
        this.replys_page_number = this.replys_page_number + 1
        const query = { comments_id: this.replys_work.comments_id, page: this.replys_page_number }
        GetReplysList(query).then(res => {
          if (res.data.status === 1) {
            if (res.data.data.length === 0) {
              this.dividers = true
            } else {
              // console.log(res.data.data.total)
              for (let i = 0; i < res.data.data.data.length; i++) {
                this.replyslist.push(res.data.data.data[i])
              }
              // 加载状态结束
              this.replys_loading = false
              // 数据全部加载完成
              if (this.replyslist.length >= res.data.data.total) {
                this.replys_finished = true
              }
              this.dividers = false
            }
            // console.log(this.comment_work.img_path[0])
            // this.commentslist = res.data.data.data
          }
        })
      }, 500)
    },
    commentsAction () {
      if (!this.if_user_login) {
        this.$toast('请先登录！')
        this.comments_box = false
        this.show_sheet_login = true
        this.show_sheet = false
        this.comment = false
        this.replys_show_sheet = false
      } else {
        if (this.user_info.official === 0) {
          if (this.user_info.schoolname == null) {
            this.$toast.fail('选择学校之后才能评论/回复哦！')
            this.$router.push({path: '/updateinfo', query: {}})
            return false
          }
        }
        if (this.comments_text.length === 0) {
          this.$toast('请写点东西吧！')
          return false
        }
        // alert(this.placeholder.type)
        if (this.placeholder.type !== 'reply') {
        // 评论
          const query = { works_id: this.comment_work.id, comments_text: this.comments_text, user_id: this.user_info.id, token: this.user_info.token }
          AddComments(query).then(res => {
            if (res.data.status === 1) {
              this.commentslist.unshift(res.data.data.data[0])
              this.comment_work.comments_count = this.comment_work.comments_count + 1
              this.comments_text = ''
              this.$toast.success('评论成功！')
              // this.columns = res.data.data
            } else {
              this.$toast('操作失败！')
            }
          })
        } else {
          // 回复
          console.log(this.placeholder)
          const query = { works_id: this.comment_work.id, comments_id: this.placeholder.info.comments_id, replys_text: this.comments_text, user_id: this.user_info.id, token: this.user_info.token }
          AddReplys(query).then(res => {
            if (res.data.status === 1) {
              // this.commentslist.push(res.data.data.data[0])
              this.replys_show_sheet = false
              this.replyinfo.replys_count = res.data.data
              this.replyinfo.my_reply_input = this.comments_text
              this.$toast.success('回复成功！')
              this.comments_text = ''
              // this.columns = res.data.data
            } else {
              this.$toast('操作失败！')
            }
          })
        }
      }
      this.placeholder = { placeholder: '说点什么吧...', type: ' comment', info: null }
    },
    // 判断字数
    fontNumber () {
      this.fontlength = this.comments_text.length
      if (this.comments_text.length > 50) {
        this.fontlength = 50
      }
    },
    cancelcomment () {
      this.comments_box = false
    },
    cancelreplys () {
      this.placeholder = { placeholder: '说点什么吧...', type: ' comment', info: null }
      this.comments_box = true
      this.replys_show_sheet = false
      this.show_sheet = true
    },
    // 图片展示
    showImg (images) {
      this.show_img = true
      this.images = images
    },
    onChange (index) {
      this.index = index
    },
    // 点赞权限
    praiseAction (item) {
      console.log(item)
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
            if (item.praise) {
              const query = { user_id: this.user_info.id, works_id: item.id, beuser_id: item.user_id, token: this.user_info.token }
              UnWorkPraises(query).then(res => {
                if (res.data.status === 1) {
                  item.praise = false
                  item.praise_number = res.data.data
                  this.$toast.success('已取消！')
                } else {
                  this.$toast('操作失败！')
                }
              })
            } else {
              const query = { user_id: this.user_info.id, works_id: item.id, beuser_id: item.user_id, token: this.user_info.token }
              WorkPraises(query).then(res => {
                if (res.data.status === 1) {
                  item.praise = true
                  item.praise_number = res.data.data
                  this.$toast.success('已点赞！')
                } else {
                  this.$toast('操作失败！')
                }
              })
            }
          }
        })
      }
    },
    placeholdercancel () {
      this.replyinfo = []
      this.placeholder = { placeholder: '说点什么吧...', type: ' comment', info: null }
      // this.placeholder = '说点什么吧...'
    },
    // 回复权限
    replyAction (item) {
      if (!this.if_user_login) {
        this.$toast('请先登录！')
        this.comments_box = false
        this.show_sheet_login = true
        this.show_sheet = false
        this.replys_show_sheet = false
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
          }
        })
        this.replyinfo = item
        // this.reply_user_nickname = item.user_nickname
        this.placeholder = { placeholder: '回复 ' + item.user_nickname, type: 'reply', info: this.replyinfo }
        console.log(item)
        document.getElementById('input').focus()
      }
    },
    // 评论点赞权限
    commentpraiseAction () {
      if (!this.if_user_login) {
        this.$toast('请先登录！')
        this.show_sheet_login = true
        this.show_sheet = false
      }
    },
    // 回复列表
    replylistAction (item) {
      // this.show_sheet = false
      this.replys_work = item
      this.replys_work.created = this.timeago(this.getDateTimeStamp(this.replys_work.comments_time))
      this.replys_show_sheet = true
      console.log(this.replys_work)
      this.replyinfo = item
      this.placeholder = { placeholder: '回复 ' + item.user_nickname, type: 'reply', info: this.replyinfo }
      this.getreplyslist()
      this.replyslist = []
      this.replys_page_number = 0
      this.replys_loading = false
      this.replys_finished = false
      document.getElementById('input').focus()
    },
    input_blur () {
      if (this.placeholder.type === 'reply') {
        if (this.comments_text.length === 0) {
          this.placeholder = { placeholder: '说点什么吧...', type: ' comment', info: null }
        } else {
          this.placeholder = { placeholder: '回复 ' + this.replyinfo.reply_user_nickname, type: 'reply', info: this.replyinfo }
        }
      } else {
        this.replyinfo = []
        this.placeholder = { placeholder: '说点什么吧...', type: ' comment', info: null }
      }
    },
    // 验证码发送
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
            this.show_sheet_login = false
            if (this.user_info.school.length !== 0 && this.user_info.school !== '0') {
              this.if_value = this.user_info.schoolname
            }
            this.workslist = []
            this.if_user_login = true
            this.$toast.success('登录成功！')
            this.works_page_number = 0
            this.onLoad()
          } else {
            this.show_sheet_login = false
            this.$toast.fail(res.data.msg)
          }
          console.log(res)
        })
      }
    },
    getDateTimeStamp (dateStr) {
      return Date.parse(dateStr.replace(/-/gi, '/'))
    },
    timeago (dateTimeStamp) {
      var minute = 1000 * 60 // 把分，时，天，周，半个月，一个月用毫秒表示
      var hour = minute * 60
      var day = hour * 24
      var week = day * 7
      // var halfamonth = day * 15
      var month = day * 30
      var now = new Date().getTime() // 获取当前时间毫秒
      // console.log(now)
      var diffValue = now - dateTimeStamp// 时间差
      if (diffValue < 0) {
        return
      }
      var minC = diffValue / minute // 计算时间差的分，时，天，周，月
      var hourC = diffValue / hour
      var dayC = diffValue / day
      var weekC = diffValue / week
      var monthC = diffValue / month
      var result
      if (monthC >= 1 && monthC <= 3) {
        result = ' ' + parseInt(monthC) + '月前'
      } else if (weekC >= 1 && weekC <= 3) {
        result = ' ' + parseInt(weekC) + '周前'
      } else if (dayC >= 1 && dayC <= 7) {
        result = ' ' + parseInt(dayC) + '天前'
      } else if (hourC >= 1 && hourC <= 23) {
        result = ' ' + parseInt(hourC) + '小时前'
      } else if (minC >= 1 && minC <= 59) {
        result = ' ' + parseInt(minC) + '分钟前'
      } else if (diffValue >= 0 && diffValue <= minute) {
        result = '刚刚'
      } else {
        var datetime = new Date()
        datetime.setTime(dateTimeStamp)
        var Nyear = datetime.getFullYear()
        var Nmonth = datetime.getMonth() + 1 < 10 ? '0' + (datetime.getMonth() + 1) : datetime.getMonth() + 1
        var Ndate = datetime.getDate() < 10 ? '0' + datetime.getDate() : datetime.getDate()
        // var Nhour = datetime.getHours() < 10 ? '0' + datetime.getHours() : datetime.getHours()
        // var Nminute = datetime.getMinutes() < 10 ? '0' + datetime.getMinutes() : datetime.getMinutes()
        // var Nsecond = datetime.getSeconds() < 10 ? '0' + datetime.getSeconds() : datetime.getSeconds()
        result = Nyear + '-' + Nmonth + '-' + Ndate
      }
      return result
    }
  }
}
</script>
<style>
    .van-field__control::placeholder {
    color: white; opacity: 1;
  }
  .school_select_home .van-field{
    background: red;color:white
  }
  .van-tabs__content .van-image__img,.van-action-sheet .van-image__img{
    width: 30px;
    height: 30px;
    border-radius: 30px;
  }
  .van-cell__title{
    margin-left: 0px;
  }

  .van-cell__value--alone{
     margin-left:0px;
  }

  .van-field__control{
    text-align: center;
    font-weight: bold;
    font-size: 13px;
  }
  .school_select_home .van-field__control{
    font-size: 18px;
    color: white
  }
  .van-cell__title span{
    font-weight: bold;
  }
  .van-cell {
    line-height: 30px;
  }
  .list_info{
      background: white;
      margin-top: 10px;
      padding-bottom: 15px;
      padding-top: 10px;
  }
  .list_info:first-child{
    padding-top: 30px;
    margin-top: 0px;
  }
  [class*=van-hairline]::after{
    border: none
  }
  .van-cell__left-icon{
    line-height: 0px;
  }
  .van-cell:not(:last-child)::after{
    border:none;
  }
  .list_operation .van-col--6{
    width: 11%;
  }
/*d*/
.main_tabs{
  margin-top: 10px;padding-bottom: 100px;width: 100%;height:auto;margin: auto;background: #fbfbfb;
}
.add_time{
  padding-left: 15px;color: #7d7e80;font-size: 12px;margin-top: 5px;
}
.content_font{
  font-size: 16px;line-height: 20px;display: block;padding-left: 10px;padding-right: 15px;color: #333333;font-family: PingFang SC,Hiragino Sans GB,Microsoft YaHei,WenQuanYi Micro Hei,Helvetica Neue,Arial,sans-serif;
}
.show_img{
  max-width: 100%;min-width: auto;max-height: 400px;min-height: auto;margin-top: 10px;
}
.operation_box{
  width: 100%;height: 30px;margin-top: 10px;
}
.praise{
  margin-left: 15px
}
.praise_number{
  line-height: 30px;margin-left: 8px;font-size:14px
}
.comment_number{
  line-height: 30px;margin-left: 8px;font-size:14px;
}
.comment_div{
  width: 100%;height: 100%;margin-top: 10px;background: #f8f8f8;;margin-bottom: 50px;
}
.comment_info{
  width: 100%;height: 110px;margin-top: 10px;background: white;
}
.comment_info:last-child{
margin-bottom: 50px;padding-top: 10px;
}
.comment_font{
font-size: 12px;margin-right: 10px;margin-left: 10px;line-height: 15px;
}

.comment_praise{

}
.mxs .van-field__control{
  text-align: left
}
.mxs .van-cell {
  padding-left: 0px;
}
.van-cell {
  padding-left: 10px;
}
.mxs .van-cell__title{
  margin-left: 10px;
}
.van-list__error-text, .van-list__finished-text, .van-list__loading{
/*  height: 100px;*/
}
.school_select_home .van-field__body input::-webkit-input-placeholder {
  color: white
}
.school_select_home .van-field__body input::-moz-input-placeholder {
   color: white;
}

.school_select_home .van-field__body input:-moz-placeholder { /* Mozilla Firefox 4 to 18 */
   color: white;
}
.school_select_home .van-field__body input::-ms-input-placeholder {
   color: white;
}
.van-tabs__wrap--scrollable .van-tab{
 flex: 0 1 22%;
}
.van-tabs__line {
    width: 41.5px;

    transform: translateX(41.5px) translateX(-70%);

    transition-duration: 0.3s;
}
.van-tabs__wrap--scrollable .van-tabs__nav{
    background: red;
}
.van-tab{
    color: white
}
.van-tabs__line{
    background-color: white;
}
</style>
