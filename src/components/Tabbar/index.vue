<template>
  <div name='tabbar' class="tabbar">
    <router-view />

<van-tabbar route active-color="red"
  inactive-color="#000">
  <van-tabbar-item
    replace
    to="/"
    icon="home-o"
    style='font-weight: bold;'
  >
    首页
  </van-tabbar-item>
  <van-tabbar-item
    replace
    to="/add"
  >
   <van-icon name="add" size='45' color='red'/>
  </van-tabbar-item>
    <van-tabbar-item
    replace
    to="/mycenter"
    icon="user-o"
    style='font-weight: bold;'
  >
    我的
  </van-tabbar-item>
</van-tabbar>
  </div>
</template>
<script>
export default {
  data () {
    return {
      active: 0
    }
  }
}

</script>
<style>
.van-icon-home-o:before {

    font-weight: bold;

}
.van-icon-user-o:before {
   font-weight: bold;
}

</style>
