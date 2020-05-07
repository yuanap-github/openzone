<template>
  <div style="background: #fbfbfb;">
    <div class="myceneter_header">
      <a  href="#/updateinfo" style="color: black" v-show='if_user_login'>
        <img class="user-poster" :src="headportrait">
        <div style="height: 100px;width: 200px;position: relative;left: 120px;">
        <span class="nick_name" v-if='user_info.official != 1'>{{nickname}}</span>
        <span class="nick_name" v-if='user_info.official === 1' style="color: red">{{nickname}}</span>
        <span class="school" v-if='user_info.official != 1'>{{school}}</span>
      </div>
       </a>
       <div style="width: 80px;height: 80px;border-radius: 60px;position:relative;top: 20px;left: 20px;line-height: 110px;text-align: center;" v-show='!if_user_login' @click="showLoginbox"><van-icon name="http://bbshonglanxiaoyuan.oss-cn-hangzhou.aliyuncs.com/default.jpg" info='登录' size='50'/></div>
    </div>
    <van-row class="user-links">
      <van-col span="8">
        <van-icon name="notes-o" @click="showListInfo"/>
        我的发布
      </van-col>
      <van-col span="8">
        <van-icon name="like-o" @click="showlikeInfo"/>
        喜欢
      </van-col>
      <van-col span="8" v-show='if_user_login'>
        <van-icon name="bars" @click="out_login"/>
        退出
      </van-col>
    </van-row>
<!--     作品 -->
    <div style="padding-bottom: 100px;" v-show='list_info_status && if_user_login'>
      <van-pull-refresh v-model="isLoading" @refresh="onRefresh" style='z-index: 100;'>
                <van-list
                  v-model="loading"
                  :finished="finished"
                  finished-text="没有更多了"
                  @load="works_function"
                >
                <div class="list_info"  v-for="item in myworkslist" v-bind:key="item.id">
                 <van-cell>
                   <template slot="title">
                   <img :src="item.headportrait" style="width: 30px;height: 30px;float: left;border-radius: 30px;">
               <van-tag type="danger"  v-if='item.official === 1' style='margin-left: 10px;'>{{item.nickname}}</van-tag>
                <van-tag round  v-if='item.official === 1'>版块：{{item.labername}}</van-tag>
              <span v-if='item.official!== 1' style='margin-left: 10px;'>{{item.nickname}}</span>
                <span :style="item.sex === 1?'font-size: 13px;color:#1296db;':'font-size: 13px;color:#ec1139'" v-if='item.official!==1'>{{item.sex === 1? '男':'女'}}</span>
                    </template>
                     <span class="add_time">{{timeago(getDateTimeStamp(item.created_at))}}<van-icon name="delete" size='25' class='delete' @click="deletemyworks(item.id)"/></span>
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
    </div>
<!--     喜欢 -->
     <div style="padding-bottom: 100px;" v-show='list_like_status && if_user_login'>
      <van-pull-refresh v-model="isLoading" @refresh="onRefresh" style='z-index: 100;'>
          <van-list
            v-model="praises_loading"
            :finished="praises_finished"
            finished-text="没有更多了"
            @load="works_function"
          >
          <div class="list_info"  v-for="item in mypraisesworkslist" v-bind:key="item.id">
          <van-cell>
             <template slot="title">
              <img :src="item.headportrait" style="width: 30px;height: 30px;float: left;border-radius: 30px;">
              <van-tag type="danger"  v-if='item.official === 1' style='margin-left: 10px;'>{{item.nickname}}</van-tag>
                    <van-tag round  v-if='user_info.official === 1'>版块：{{item.labername}}</van-tag>
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
              <van-col span="6"><van-icon name="eye-o" size='30'  @click="messagesAction()"/></van-col><span style="position: relative;left: 30px;line-height: 30px;">{{item.browse_number}}</span>
              </span>
              <span @click="commentShow(item)">
              <van-col span="6"><van-icon name="comment-circle-o
  " style='float: right;' size='30' class='comment'/></van-col><span class="comment_number">{{item.comments_count}}</span></span>
            </van-row>
          </div>
        </div>
      </van-list>
      </van-pull-refresh>

     </div>
<van-image-preview
  v-model="show_img"
  :images="images"
  @change="onChange"
>
</van-image-preview>
<!-- 评论列表 -->
  <van-action-sheet v-model="show_sheet" title="评论" @cancel='cancelcomment' @click='placeholdercancel'>
 <!--   <van-button round type="danger" style='height: 25px;line-height: 25px;margin-left: 5px'>发布</van-button> -->
     <van-cell>
       <template slot="title">
        <img :src="comment_work.headportrait" style="width: 30px;height: 30px;float: left;border-radius: 30px;">
             <van-tag type="danger"  v-if='comment_work.official === 1'  style='margin-left: 10px;'>{{comment_work.nickname}}</van-tag>
                    <van-tag round v-if='user_info.official === 1'>版块：{{comment_work.labername}}</van-tag>
                 <span v-if='comment_work.official!== 1'  style='margin-left: 10px;'>{{comment_work.nickname}}</span>
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
        <!--   <van-pull-refresh v-model="isLoading" @refresh="onRefresh" style='z-index: 100;'> -->
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
        <van-divider>所有回复</van-divider>
        <p style="text-align: center;color: #969799;font-size: 12px;" v-show='dividers'>暂无评论</p>
        <div style="" class="comment_div">
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

</van-action-sheet>
<!-- 底部tabar -->
    <Tabbar />
<!--     tabar结束 -->

  </div>
</template>

<script>
import Tabbar from '@/components/Tabbar'
import { CheckToken, SendSms, CheckPhoneCode } from '@/api/user.js'
import { MyWorksList, DeleteMyWorks, WorkPraises, UnWorkPraises, MyPraisesWorksList } from '@/api/works.js'
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
      show_sheet_login: false,
      list_info_status: true,
      list_like_status: false,
      list_messages_status: false,
      if_user_login: false,
      show_sheet: false,
      replys_show_sheet: false,
      user_info: [],
      nickname: '',
      school: '',
      headportrait: '',
      myworkslist: [],
      show: true,
      show_img: false,
      images: [],
      isLoading: false,
      loading: false,
      finished: false,
      praises_loading: false,
      praises_finished: false,
      comments_loading: false,
      comments_finished: false,
      replys_loading: false,
      replys_finished: false,
      myworks_page_number: 0,
      placeholder: [],
      replyinfo: [],
      my_reply_input: '',
      comments_page_number: 0,
      replys_page_number: 0,
      fontlength: 0,
      like: 'like-o',
      like_switch: false,
      comment_work: [],
      replys_work: [],
      comments_box: false,
      comments_text: '',
      commentslist: [],
      replyslist: [],
      mypraisesworkslist: [],
      mypraisesworks_page_number: 0,
      comment_img: false,
      dividers: false

    }
  },
  created () {
    if (this.$cookie.get('user_info')) {
      this.user_info = JSON.parse(this.$cookie.get('user_info'))
      this.checktoken()
      this.nickname = this.user_info.nickname
      this.school = this.user_info.schoolname
      if (this.school == null) {
        this.school = '请选择自己的学校'
      }
      this.headportrait = this.user_info.headportrait
      this.getmyworkslist()
    } else {
      this.$toast('请先登录！')
      this.show_sheet_login = true
    }
  },
  methods: {
    // token验证
    checktoken () {
      const querys = { user_id: this.user_info.id, token: this.user_info.token }
      CheckToken(querys).then(res => {
        if (res.data.status === 10003) {
          this.$cookie.set('user_info', this.user_info, -1)
          this.show_sheet_login = true
          return false
        } else {
          this.if_user_login = true
        }
      })
    },
    works_function (functionname) {
      if (functionname == null) {
        this.getmyworkslist()
      } else {
        this.getmypraisesworkslist()
      }
    },
    // 展示list info
    showListInfo () {
      if (this.if_user_login) {
        this.myworkslist = []
        this.myworks_page_number = 0
        this.works_function()
        this.mypraisesworkslist = []
        this.mypraisesworks_page_number = 0
        this.list_info_status = true
        this.list_like_status = false
        this.list_messages_status = false
      } else {
        this.show_sheet_login = true
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
    // 展示喜欢 info
    showlikeInfo () {
      if (this.if_user_login) {
        this.list_info_status = false
        this.list_like_status = true
        this.list_messages_status = false
        this.getmypraisesworkslist()
      } else {
        this.show_sheet_login = true
      }
    },
    // 获取我的点赞作品 list
    getmypraisesworkslist () {
      setTimeout(() => {
        this.mypraisesworks_page_number = this.mypraisesworks_page_number + 1
        const query = { user_id: this.user_info.id, token: this.user_info.token, page: this.mypraisesworks_page_number }
        MyPraisesWorksList(query).then(res => {
          if (res.data.status === 1) {
            // this.myworkslist = res.data.data
            for (let i = 0; i < res.data.data.data.length; i++) {
              this.mypraisesworkslist.push(res.data.data.data[i])
            }
            // 加载状态结束
            this.praises_loading = false
            // 数据全部加载完成
            if (this.mypraisesworkslist.length >= res.data.data.total) {
              this.praises_finished = true
            }
          }
        })
      }, 500)
    },
    // 评论点赞权限
    // commentpraiseAction () {
    //   if (!this.if_user_login) {
    //     this.$toast('请先登录！')
    //     this.show_sheet_login = true
    //     this.show_sheet = false
    //   }
    // },
    // 评论点赞权限
    // 评论展示
    commentShow (info) {
      this.placeholder = { placeholder: '说点什么吧...', type: ' comment', info: null }
      this.comments_box = true
      this.comment_work = info
      this.replyinfo = []
      this.comment_work.created = this.timeago(this.getDateTimeStamp(this.comment_work.created_at))
      console.log(this.comment_work.img_path[0])
      this.show_sheet = true
      this.comment_img = true
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
      } else {
        if (this.user_info.schoolname == null) {
          this.$toast.fail('选择学校之后才能评论/回复哦！')
          this.$router.push({path: '/updateinfo', query: {}})
          return false
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
    // 登录框展示
    showLoginbox () {
      this.show_sheet_login = true
    },
    // 图片展示
    showImg (images) {
      this.show_img = true
      this.images = images
    },
    onChange (index) {
      this.index = index
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
            this.if_user_login = true
            this.nickname = this.user_info.nickname
            this.school = this.user_info.schoolname
            this.headportrait = this.user_info.headportrait
            this.getmyworkslist()
            if (this.school == null) {
              this.school = '请选择自己的学校'
            }
            this.$toast.success('登录成功！')
            console.log(JSON.parse(this.$cookie.get('user_info')))
          } else {
            this.show_sheet_login = false
            this.$toast.fail(res.data.msg)
          }
          console.log(res)
        })
      }
    },
    // 获取我的作品 list
    getmyworkslist () {
      setTimeout(() => {
        this.myworks_page_number = this.myworks_page_number + 1
        const query = { user_id: this.user_info.id, token: this.user_info.token, page: this.myworks_page_number }
        MyWorksList(query).then(res => {
          if (res.data.status === 1) {
            // this.myworkslist = res.data.data
            for (let i = 0; i < res.data.data.data.length; i++) {
              this.myworkslist.push(res.data.data.data[i])
            }
            // 加载状态结束
            this.loading = false
            // 数据全部加载完成
            if (this.myworkslist.length >= res.data.data.total) {
              this.finished = true
            }
          }
        })
      }, 500)
    },
    // 删除我的作品
    deletemyworks (id) {
      console.log(this.myworkslist)
      this.$dialog.confirm({
        title: '删除提示',
        message: '确认删除吗？'
      }).then(() => {
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
            const query = { user_id: this.user_info.id, token: this.user_info.token, id: id }
            DeleteMyWorks(query).then(res => {
              if (res.data.status === 1) {
                for (var i = 0; i < this.myworkslist.length; i++) {
                  if (this.myworkslist[i].id === id) {
                    this.myworkslist.splice(i, 1)
                  }
                }
                this.$toast.success('删除成功！')
              } else {
                this.$toast.fail('删除失败！')
              }
            })
          }
        })
      }).catch(() => {
        // on cancel
      })
    },
    replace_https (urlstr) {
      return urlstr.replace(/^http/, 'https')
    },
    // 点赞权限
    praiseAction (item) {
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
    },
    // 退出登录
    out_login () {
      this.$dialog.confirm({
        title: '退出提示',
        message: '确认退出吗？'
      }).then(() => {
        this.$cookie.set('user_info', this.user_info, -1)
        this.if_user_login = false
        this.$toast.success('退出成功！')
      }).catch(() => {
        // on cancel
      })
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

<style lang="less">
.user {
  &-poster {
    width: 100%;
    height: 53vw;
    display: block;
  }

  &-group {
    margin-bottom: 15px;
  }

  &-links {
    padding: 15px 0;
    font-size: 12px;
    text-align: center;
    background-color: #fff;

    .van-icon {
      display: block;
      font-size: 24px;
    }
  }

}

</style>
<style>
.myceneter_header{
   width: 100%;height: 100px;

  }
.myceneter_header .user-poster{
    width: 80px;height: 80px;border-radius: 60px;display: block;position:absolute;top: 10px;left: 20px;
  }

.van-dialog__content img{
  width: 100%;
}
.list_info .van-image__img,.van-action-sheet .van-image__img{
    width: 30px;
    height: 30px;
    border-radius: 30px;
  }

 .list_info .van-cell__title,.van-action-sheet .van-cell__title{
    margin-left: 0px;
  }
  .list_info .van-cell__title span,.van-action-sheet .van-cell__title span{
    font-weight: bold;
  }
  .list_info .van-cell,.van-action-sheet .van-cell{
    line-height: 30px;border-bottom: none
  }
   .list_operation .van-col--6{
    width: 11%;
  }
  .van-cell:not(:last-child)::after{
    border-bottom: none
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
  .nick_name{
    display: block;line-height: 68px;    font-weight: bold;
    font-size: 20px;
  }
  .school{
   display: block;line-height: 0px;

  }
  .van-icon{
    cursor: pointer;
  }
  .mxs .van-field__control{
    text-align: left;
  }
  /*d*/
.main_tabs{
  margin-top: 10px;padding-bottom: 100px;width: 100%;height:auto;margin: auto;background: #f8f8f8;
}
.add_time{
  padding-left: 15px;color: #7d7e80;font-size: 12px;margin-top: 5px;
}
.delete{
  top: 8px;
}
.content_font{
  font-size: 16px;line-height: 20px;display: block;padding-left: 15px;padding-right: 15px;color: #333333;font-family: PingFang SC,Hiragino Sans GB,Microsoft YaHei,WenQuanYi Micro Hei,Helvetica Neue,Arial,sans-serif;
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
  width: 100%;height: 100%;margin-top: 10px;background: #fbfbfb;
}
.comment_info{
  width: 100%;height: 110px;margin-top: 10px;background: white;padding-top: 10px;
}
.comment_font{
  line-height: 0px;font-size: 12px;margin-left: 10px;line-height: 15px;
}

.comment_praise{

}
.mxs .van-field__control{
  text-align: left
}
.mxs .van-cell {
  padding-left: 0px;
}
.mxs .van-cell__title{
  margin-left: 10px;
}

.van-cell {
  padding-left: 10px;
}
</style>
