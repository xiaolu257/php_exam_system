
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import ElementPlus from 'element-plus' //导入 ElementPlus 组件库的所有模块和功能
import 'element-plus/dist/index.css' //导入 ElementPlus 组件库所需的全局 CSS 样式
import '@/assets/styles/main.scss'
import App from '@/App.vue'
import router from './router'
const app = createApp(App)
app.use(createPinia())
app.use(ElementPlus) //将 ElementPlus 插件注册到 Vue 应用中
app.use(router)
app.mount('#app')


