require('./bootstrap');
window.Vue = require('vue')
import router from './router'
import store from './store'

// View UI
import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
Vue.use(ViewUI);
// End View UI

import common from './common'
import jsonToHtml from './jsonToHtml'
Vue.mixin(common)
Vue.mixin(jsonToHtml)
import moment from 'moment';

// Editor
import Editor from 'vue-editor-js'
Vue.use(Editor)
// End Editor

Vue.component('mainapp', require('./components/mainapp.vue').default)
Vue.filter('timeformat', (arg) => {
    return moment(arg).format('LL')
})
const app = new Vue({
    el: '#app',
    router,
    store,
})
