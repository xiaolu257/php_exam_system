export const allAsyncRoutes = [
    {
        path: 'AdminAccountManager',
        name: 'AdminAccountManager',
        component: () => import("@/view/SuperManager/AdminTableManager.vue"),
    },
    {
        path: 'UserAccountManager',
        name: 'UserAccountManager',
        component: () => import("@/view/SuperManager/UserTableManager.vue"),
    },
    {
        path: 'MyLogs',
        name: 'MyLogs',
        component: () => import("@/view/SuperManager/MyLogsTableManager.vue"),
    },
    {
        path: 'SingleChoiceQuestionManagement',
        name: 'SingleChoiceQuestionManagement',
        component: () => import("@/view/SingleChoiceQuestionManagement.vue"),
    },

];
