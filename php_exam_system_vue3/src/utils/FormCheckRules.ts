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
    name: [
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
export const userAccountRules: Record<string, FormItemRule[]> = {
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
            pattern: /^[a-zA-Z0-9!@#?.]{1,18}$/,
            message: '密码必须是6到18位的英文、数字或“!@#?. ”',
            //trigger: ['blur', 'change'],
            trigger: ['blur', 'change'],
        },
    ],
    name: [
        {required: true, message: '请输入用户名', trigger: ['blur', 'change']},
        {
            pattern: /^[a-zA-Z0-9\u4e00-\u9fa5]{3,12}$/,
            message: '用户名必须是3到12位的英文、数字或汉字',
            //trigger: ['blur', 'change'],
            trigger: ['blur', 'change'],
        },
    ],
    gender: [
        {required: true, message: '请选择性别', trigger: ['blur', 'change']},
    ],
    phone: [
        {
            pattern: /^1[3-9][0-9]{9}$/,
            message: '电话号码必须以1开头，第二位是3-9的11位数字',
            trigger: ['blur', 'change'],
        },
    ],
    avatar: [
        {required: true, message: '请选择图片上传', trigger: ['blur', 'change']},
    ],
    id_card: [
        {
            pattern: /^[1-9]\d{5}(19\d{2}|20[0-2]\d)(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{3}(\d|X|x)$/,
            message: '请输入18位有效的身份证号码',
            trigger: ['blur', 'change'],
        },
    ],

};