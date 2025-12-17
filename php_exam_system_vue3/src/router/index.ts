// router/index.js
import {createRouter, createWebHistory} from 'vue-router';
import {myPost} from "@/api/utils/axios";
import {loadAdminData, quitLogin} from "@/api/Admin";
import {extractAllowedPathsFromMenu, setupDynamicRoutes} from "@/router/permission";
import {getMenuByUserType} from "@/utils/AuditManagerMenu";
import {useGlobalStore} from "@/stores/counter";

const routes = [
    {
        name: 'Login',
        path: '/',
        component: () => import("@/view/Public/Login.vue"),
    },
    {
        name: 'Register',
        path: '/Register',
        component: () => import("@/view/Public/Register.vue"),
    },
    {
        name: 'Manager',
        path: '/Manager',
        redirect: '/Manager/Home',
        component: () => import("@/view/Public/Manager.vue"),
        children: [
            {
                path: 'Home',
                name: 'Home',
                component: () => import("@/view/Public/Home.vue"),
            },
            {
                path: 'Profile',
                name: 'Profile',
                component: () => import("@/view/Public/Profile.vue"),
            },
        ]
    },
    {
        name: 'NotFound',
        path: '/:pathMatch(.*)*', // Matches any path that hasn't been matched by other routes
        component: () => import("@/view/Public/NotFound.vue")
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});
let lastRegisteredUserType = -1;
// 全局前置守卫
router.beforeEach(async (to) => {
    const isLogin = await checkIsLogin();
    if ((to.path === '/' || to.path === '/Register') && isLogin) {
        return '/Manager';
    }
    if ((to.path !== '/' && to.path !== '/Register') && !isLogin) {
        return '/';
    }
    const {userType} = useGlobalStore();
    if (userType !== lastRegisteredUserType) {
        const menu = getMenuByUserType(userType);
        const allowedPaths = extractAllowedPathsFromMenu(menu);
        setupDynamicRoutes(allowedPaths);
        lastRegisteredUserType = userType;
        return {path: to.fullPath, replace: true};
    }
    return true;
});

let isTokenValidated = false;

async function checkIsLogin() {
    const access_token = localStorage.getItem('access_token');
    if (!access_token) return false;

    // 刷新页面才验证一次
    if (!isTokenValidated) {
        try {
            const {userData} = await myPost('user/validateAdminToken', {}, false);
            loadAdminData(userData);
            isTokenValidated = true;
            return true;
        } catch {
            quitLogin();
            return false;
        }
    }

    return true; // 内存状态已有，不必每次路由跳转都请求
}


export default router;
