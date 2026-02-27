const menuBaseUrl = '/Manager';
export const AuditManagerMenuRoutes = [
    {
        name: '首页',
        path: menuBaseUrl + '/Home'
    },
    {
        name: '题目管理',
        children: [
            {
                name: '单选题管理',
                path: menuBaseUrl + '/SingleChoiceQuestionManagement',
            },
            {
                name: '多选题管理',
                path: menuBaseUrl + '/MultipleChoiceQuestionManagement',
            },
            {
                name: '判断题管理',
                path: menuBaseUrl + '/TrueFalseQuestionManagement',
            },
        ]
    },
    {
        name: '个人资料',
        path: menuBaseUrl + '/Profile',
    },
];
export const OperationManagerMenuRoutes = [
    {
        name: '首页',
        path: menuBaseUrl + '/Home'
    },
    {
        name: '小程序管理',
        children: [
            {
                name: '轮播图管理',
                path: menuBaseUrl + '/Banners',
            },
            {
                name: '轮播图预览',
                path: menuBaseUrl + '/PreviewBanners',
            }
        ]
    },
    {
        name: '工作分类管理',
        children: [
            {
                name: '工作分类',
                path: menuBaseUrl + '/JobCategories',
            },
            /*{
                name: '操作日志',
                path: menuBaseUrl + '/JobCategoryLogs',
            },*/
        ]
    },
    {
        name: '工作区域管理',
        children: [
            {
                name: '工作区域',
                path: menuBaseUrl + '/JobAreas',
            },
            /*{
                name: '操作日志',
                path: menuBaseUrl + '/JobAreasLogs',
            },*/
        ]
    },
    {
        name: '个人资料',
        path: menuBaseUrl + '/Profile',
    },
];
export const SuperManagerMenuRoutes = [
    {
        name: '首页',
        path: menuBaseUrl + '/Home'
    },
    {
        name: '账号管理',
        children: [
            {
                name: '管理员账号',
                path: menuBaseUrl + '/AdminAccountManager',
            },
            {
                name: '用户账号',
                path: menuBaseUrl + '/UserAccountManager',
            },
            {
                name: '权限管理',
                path: menuBaseUrl + '/AdminPermissionsManager',
            }
        ]
    },
    {
        name: '开发日志',
        path: menuBaseUrl + '/MyLogs',
    },
    {
        name: '个人资料',
        path: menuBaseUrl + '/Profile',
    },
];
export const DevelopMenuRoutes = [
    {
        name: '首页',
        path: menuBaseUrl + '/Home'
    },
    {
        name: '信息审核',
        children: [
            {
                name: '审核用户简历',
                path: menuBaseUrl + '/AuditResume',
            },
            {
                name: '审核招工信息',
                path: menuBaseUrl + '/AuditJob',
            },
            {
                name: '审核企业认证',
                path: menuBaseUrl + '/AuditEnterpriseAuthentication',
            },
        ]
    },
    {
        name: '小程序管理',
        children: [
            {
                name: '轮播图管理',
                path: menuBaseUrl + '/Banners',
            },
            {
                name: '轮播图预览',
                path: menuBaseUrl + '/PreviewBanners',
            }
        ]
    },
    {
        name: '工作分类管理',
        children: [
            {
                name: '工作分类',
                path: menuBaseUrl + '/JobCategories',
            },
            /*{
                name: '操作日志',
                path: menuBaseUrl + '/JobCategoryLogs',
            },*/
        ]
    },
    {
        name: '工作区域管理',
        children: [
            {
                name: '工作区域',
                path: menuBaseUrl + '/JobAreas',
            },
            /*{
                name: '操作日志',
                path: menuBaseUrl + '/JobAreasLogs',
            },*/
        ]
    },
    {
        name: '账号管理',
        children: [
            {
                name: '管理员账号',
                path: menuBaseUrl + '/AdminAccountManager',
            },
            {
                name: '用户账号',
                path: menuBaseUrl + '/UserAccountManager',
            },
            {
                name: '权限管理',
                path: menuBaseUrl + '/AdminPermissionsManager',
            }
        ]
    },
    {
        name: '开发日志',
        path: menuBaseUrl + '/MyLogs',
    },
    {
        name: '个人资料',
        path: menuBaseUrl + '/Profile',
    },
];

export function getMenuByUserType(userType: number) {
    switch (userType) {
        case 0:
            return AuditManagerMenuRoutes;
        case 1:
            return OperationManagerMenuRoutes;
        case 2:
            return SuperManagerMenuRoutes;
        case 3:
            return DevelopMenuRoutes;
        default:
            return [];
    }
}