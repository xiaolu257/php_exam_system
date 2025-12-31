import type {FormItemRule} from "element-plus";

export const adminAccountRules: Record<string, FormItemRule[]> = {
    username: [
        {required: true, message: '请输入账号', trigger: ['blur', 'change']},
        {
            pattern: /^[a-zA-Z0-9]{1,12}$/,
            message: '账号必须是6到12位的英文或数字',
            //trigger: ['blur', 'change'],
            trigger: ['blur', 'change'],
        },
    ],
    password: [
        {required: true, message: '请输入密码', trigger: ['blur', 'change']},
        {
            pattern: /^[a-zA-Z0-9!@#?.]{6,18}$/,
            message: '密码必须是6到18位的英文、数字或“!@#?. ”',
            trigger: ['blur', 'change'],
        },
    ],
    nickname: [
        {required: true, message: '请输入用户名', trigger: ['blur', 'change']},
        {
            pattern: /^[a-zA-Z0-9\u4e00-\u9fa5]{3,12}$/,
            message: '用户名必须是3到12位的英文、数字或汉字',
            //trigger: ['blur', 'change'],
            trigger: ['blur', 'change'],
        },
    ],
    type: [
        {required: true, message: '请选择管理员类型', trigger: ['blur', 'change']},
    ],
    avatar: [
        {required: true, message: '请选择图片上传', trigger: ['blur', 'change']},
    ],
};