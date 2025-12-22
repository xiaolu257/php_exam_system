import {myAxios, myPost} from "@/api/utils/axios";
import MyMessage from "@/utils/MyMessage";
import myMessage from "@/utils/MyMessage";
import router from "@/router";
import {storeToRefs} from "pinia";
import {useGlobalStore} from "@/stores/counter";

export function getAdminType(userType: number): string {
    const adminTypes: Record<number, string> = {
        0: '用户',
        1: '管理员',
    };

    return adminTypes[userType] ?? '未知类型';
}

export async function login(data: Record<string, any>) {
    myPost('user/login', data).then(async ({access_token, refresh_token}) => {
        localStorage.setItem('access_token', access_token);
        localStorage.setItem('refresh_token', refresh_token);
        MyMessage.success('登录成功！');
        router.push('/Manager');
    })
}

export const intToIp = (ipInt: number): string => {
    // 按字节提取IP地址的4个部分
    const byte1 = (ipInt >> 24) & 255;  // 获取第1字节
    const byte2 = (ipInt >> 16) & 255;  // 获取第2字节
    const byte3 = (ipInt >> 8) & 255;   // 获取第3字节
    const byte4 = ipInt & 255;          // 获取第4字节

    // 返回标准的点分十进制格式
    return `${byte1}.${byte2}.${byte3}.${byte4}`;
}
export const loadAdminData = (userData: any) => {
    const {username: _username = '', type = 0, nickname = '', avatar_url = ''} = userData;
    const {username, userType, userNickName, userAvatarUrl} = storeToRefs(useGlobalStore());
    username.value = _username;
    userType.value = type;
    userNickName.value = nickname;
    userAvatarUrl.value = avatar_url;
}

export async function getMyself() {
    try {
        // 解构用户数据并设置全局变量

        // 调用 API 验证 token
        const response = await myAxios.post('/Admin/getMyself');
        const {success = false, error = '获取个人信息失败，请刷新浏览器重试', userData = {}} = response.data;
        if (!success) {
            return myMessage.error(error);
        }
        loadAdminData(userData);
        return true;
    } catch (err: any) {
        // 捕获异常并显示服务器故障信息
        return MyMessage.error(err.message || '服务器故障，验证失败，请稍后再试');
    }
}

export function quitLogin() {
    localStorage.clear();
    router.push('/');
}