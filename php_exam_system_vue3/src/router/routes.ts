import router from "@/router/index";
import {useGlobalStore} from "@/stores/global";
import {buildMenu} from "@/router/menu";
import type {RouteRecordRaw} from "vue-router";

const menuRoutes = [
    {
        path: 'SingleChoiceQuestionManagement',
        name: 'SingleChoiceQuestionManagement',
        component: () => import("@/view/question/SingleChoiceQuestionManagement.vue"),
    },
    {
        path: 'MultipleChoiceQuestionManagement',
        name: 'MultipleChoiceQuestionManagement',
        component: () => import("@/view/question/MultipleChoiceQuestionManagement.vue"),
    },
    {
        path: 'TrueFalseQuestionManagement',
        name: 'TrueFalseQuestionManagement',
        component: () => import("@/view/question/TrueFalseQuestionManagement.vue"),
    },
    {
        path: 'ShortAnswerQuestionManagement',
        name: 'ShortAnswerQuestionManagement',
        component: () => import("@/view/question/ShortAnswerQuestionManagement.vue"),
    },
    {
        path: 'ExamPaperManagement',
        name: 'ExamPaperManagement',
        component: () => import("@/view/exam/ExamPaperManagement.vue"),
    },
    {
        name: 'MenuManagement',
        path: 'MenuManagement',
        component: () => import('@/view/permission/MenuManagement.vue'),
    },
    {
        name: 'RoleManagement',
        path: 'RoleManagement',
        component: () => import('@/view/permission/RoleManagement.vue'),
    },
    {
        name: 'RoleMenuManagement',
        path: 'RoleMenuManagement',
        component: () => import('@/view/permission/RoleMenuManagement.vue'),
    },
    {
        name: 'PermissionList',
        path: 'PermissionList',
        component: () => import('@/view/permission/PermissionList.vue'),
    },
    {
        name: 'RolePermissionManagement',
        path: 'RolePermissionManagement',
        component: () => import('@/view/permission/RolePermissionManagement.vue'),
    },
    {
        name: 'UserManagement',
        path: 'UserManagement',
        component: () => import('@/view/permission/UserManagement.vue'),
    },
    {
        name: 'UserRoleManagement',
        path: 'UserRoleManagement',
        component: () => import('@/view/permission/UserRoleManagement.vue'),
    }
];
const standaloneRoutes = [
    {
        name: 'BeforeExam',
        path: '/exam/:id/before',
        component: () => import("@/view/exam/BeforeExam.vue"),
    },
    {
        name: 'ExamStart',
        path: '/exam/:id/start',
        component: () => import('@/view/exam/ExamStart.vue'),
    },
    {
        name: 'ExamPreview',
        path: '/exam/:id/preview',
        component: () => import('@/view/exam/ExamPreview.vue'),
    },
]

// 通用函数：根据菜单 code 筛选对应的路由
function generateRoutesFromMenus(
    menus: any[],
    routeConfigs: RouteRecordRaw[]
): RouteRecordRaw[] {
    const menuCodes = new Set<string>();

    function traverse(items: any[]) {
        for (const item of items) {
            menuCodes.add(item.code);

            if (item.children?.length) {
                traverse(item.children);
            }
        }
    }

    traverse(menus);

    return routeConfigs.filter(route =>
        route.name != null && menuCodes.has(String(route.name))
    );
}

export function setupDynamicRoutes(menus: any[]) {
    // 1. 菜单路由：添加到 Manager 下
    const routesToAdd = generateRoutesFromMenus(
        menus,
        menuRoutes
    );

    routesToAdd.forEach(route => {
        if (!router.hasRoute(route.name!)) {
            router.addRoute('Manager', route);
        }
    });

    // 2. 独立路由：添加到根路由
    const standaloneRoutesToAdd = generateRoutesFromMenus(
        menus,
        standaloneRoutes
    );

    standaloneRoutesToAdd.forEach(route => {
        if (!router.hasRoute(route.name!)) {
            router.addRoute(route);
        }
    });
    
    // 3. 更新侧边栏
    useGlobalStore().setSidebarMenus(buildMenu(menus.filter(route => {
        return route.code !== 'HidePage';
    })));
}
