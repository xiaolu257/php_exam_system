import router from "@/router/index";
import {useGlobalStore} from "@/stores/counter";
import {buildMenu} from "@/router/menu";

const allAsyncRoutes = [
    {
        path: 'SingleChoiceQuestionManagement',
        name: 'SingleChoiceQuestionManagement',
        component: () => import("@/view/SingleChoiceQuestionManagement.vue"),
    },
    {
        path: 'MultipleChoiceQuestionManagement',
        name: 'MultipleChoiceQuestionManagement',
        component: () => import("@/view/MultipleChoiceQuestionManagement.vue"),
    },
    {
        path: 'TrueFalseQuestionManagement',
        name: 'TrueFalseQuestionManagement',
        component: () => import("@/view/TrueFalseQuestionManagement.vue"),
    },
    {
        path: 'ShortAnswerQuestionManagement',
        name: 'ShortAnswerQuestionManagement',
        component: () => import("@/view/ShortAnswerQuestionManagement.vue"),
    },
    {
        path: 'ExamPaperManagement',
        name: 'ExamPaperManagement',
        component: () => import("@/view/ExamPaperManagement.vue"),
    },
    {
        name: 'BeforeExam',
        path: '/exam/:id/before',
        component: () => import("@/view/Exam/BeforeExam.vue"),
    },
    {
        name: 'ExamStart',
        path: '/exam/:id/start',
        component: () => import('@/view/Exam/ExamStart.vue'),
    },
    {
        name: 'ExamPreview',
        path: '/exam/:id/preview',
        component: () => import('@/view/Exam/ExamPreview.vue'),
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
