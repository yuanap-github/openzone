// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
// import Cookies from 'js-cookie'
import VueCookies from 'vue-cookie'
// import lrz from 'lrz'
/* vant button 、tabbar */

import { Button, Tabbar, TabbarItem, Icon, Picker, Popup, Field, Tab, Tabs, List, Cell, CellGroup, Collapse, CollapseItem, Lazyload, Skeleton, Row, Col, Loading, NavBar, Toast, Uploader, Checkbox, CheckboxGroup, Tag, SwipeCell, Image, Sticky, Dialog, NoticeBar, AddressEdit, ActionSheet, Divider, ImagePreview, RadioGroup, Radio, PullRefresh } from 'vant'
Vue.use(Tabbar).use(TabbarItem).use(Button).use(Icon).use(Picker).use(Popup).use(Field).use(Tab).use(Tabs).use(List).use(Cell).use(CellGroup).use(Collapse).use(CollapseItem).use(Lazyload).use(Skeleton).use(Row).use(Col).use(Loading).use(NavBar).use(Toast).use(Uploader).use(Checkbox).use(CheckboxGroup).use(Tag).use(SwipeCell).use(Image).use(Sticky).use(Dialog).use(NoticeBar).use(AddressEdit).use(ActionSheet).use(Divider).use(ImagePreview).use(RadioGroup).use(Radio).use(PullRefresh)
Vue.config.productionTip = false
Vue.use(VueCookies)
// Vue.use(lrz)
// Vue.use(lrz)
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
})
