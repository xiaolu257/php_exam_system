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
        path: 'JobAreas',
        name: 'JobAreas',
        component: () => import("@/view/OperationManager/JobAreasTableManager.vue"),
    },
    {
        path: 'JobCategories',
        name: 'JobCategories',
        component: () => import("@/view/OperationManager/JobCategoriesTableManager.vue"),
    },


    {
        path: 'AuditJob',
        name: 'AuditJob',
        component: () => import("@/view/AuditManager/AuditJob.vue"),
    },
    {
        path: 'AuditResume',
        name: 'AuditResume',
        component: () => import("@/view/AuditManager/AuditResume.vue"),
    },
    {
        path: 'Banners',
        name: 'Banners',
        component: () => import("@/view/OperationManager/BannerTableManager.vue"),
    },
    {
        path: 'PreviewBanners',
        name: 'PreviewBanners',
        component: () => import("@/view/OperationManager/PreviewBanners.vue"),
    },
    {
        path: 'AuditEnterpriseAuthentication',
        name: 'AuditEnterpriseAuthentication',
        component: () => import("@/view/AuditManager/AuditEnterpriseAuthentication.vue"),
    },
];
