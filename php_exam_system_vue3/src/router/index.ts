// router/index.js
import {createRouter, createWebHistory} from 'vue-router';
import {myPost} from "@/api/utils/axios";
import {loadAdminData} from "@/api/admin";
import {setupDynamicRoutes} from "@/router/routes";

const routes = [
    {
        name: 'index',
        path: '/',
        redirect: '/Login',
    },
    {
        name: 'Login',
        path: '/Login',
        component: () => import("@/view/public/Login.vue"),
    },
    {
        name: 'Register',
        path: '/Register',
        component: () => import("@/view/public/Register.vue"),
    },
    {
        name: 'Test',
        path: '/test',
        component: () => import('@/view/public/Test.vue'),
    },
    {
        name: 'Manager',
        path: '/Manager',
        redirect: '/Manager/Home',
        component: () => import("@/view/public/Manager.vue"),
        children: [
            {
                path: 'Home',
                name: 'Home',
                component: () => import("@/view/public/Home.vue"),
            },
            {
                path: 'Profile',
                name: 'Profile',
                component: () => import("@/view/public/Profile.vue"),
            },
        ]
    },
    {
        name: 'NotFound',
        path: '/:pathMatch(.*)*', // Matches any path that hasn't been matched by other routes
        component: () => import("@/view/public/NotFound.vue")
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

let isTokenValidated = false;
const whitePath = [
    '/Login',
    '/Register'
]
router.beforeEach(async (to) => {
    const token = localStorage.getItem('access_token');
    if (token && !isTokenValidated) {
        try {
            const response = await myPost('user/validate-admin-token', {}, false);
            const {userData, menus} = response;
            loadAdminData(userData);
            setupDynamicRoutes(menus);
            isTokenValidated = true;
            //重定向一次，避免当前去往的路由刚被动态添加而进入NotFound页面
            return {path: to.fullPath, replace: true};
        } catch {
            localStorage.clear();
            return '/Login';
        }
    }
    // 未登录拦截
    if (!token && !whitePath.includes(to.path)) {
        return '/Login';
    }
    // ⭐新增：已登录访问登录/注册页 → 跳首页
    if (token && (whitePath.includes(to.path))) {
        return '/Manager/Home';
    }
    return true;
});
export default router;
