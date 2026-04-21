import router from "@/router/index";
import {useGlobalStore} from "@/stores/global";
import {buildMenu} from "@/router/menu";

const allAsyncRoutes = [
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
];

function generateRoutesFromMenus(menus: any[]) {
    const routes: any[] = [];

    function traverse(items: any[]) {
        for (const item of items) {
            const matchRoute = allAsyncRoutes.find(r => r.name === item.code);

            if (matchRoute) {
                routes.push(matchRoute);
            }

            if (item.children?.length) {
                traverse(item.children);
            }
        }
    }

    traverse(menus);
    return routes;
}

export function setupDynamicRoutes(menus: any[]) {
    const routesToAdd = generateRoutesFromMenus(menus);

    routesToAdd.forEach(route => {
        if (!router.hasRoute(route.name)) {
            router.addRoute('Manager', route);
        }
    });

    useGlobalStore().setSidebarMenus(buildMenu(menus));
}
