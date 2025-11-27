import {fileURLToPath, URL} from 'node:url'

import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    /*base:'/web',//必要的属性，如果没有的话和后端接口部署在同一服务器会出现路径问题导致网站渲染有问题，可根据实际命名修改
    //下方这句为辅助功能，不需要手动复制移动打包的网站项目，可根据实际路径修改
    build: {
        outDir: path.resolve(__dirname, 'D:\\MyCodeTool\\phpstudy_pro\\WWW\\xiaolu.cn\\tp8\\public\\web'),
    },*/
    plugins: [
        vue(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url))
        }
    },
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['legacy-js-api', 'import'],
            },
        },
    },
})
