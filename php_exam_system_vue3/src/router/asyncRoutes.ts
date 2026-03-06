export const allAsyncRoutes = [
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
];
