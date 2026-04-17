import {myPost} from "@/api/utils/axios";
import MyMessage from "@/utils/myMessage";
import router from "@/router";
import {storeToRefs} from "pinia";
import {useGlobalStore} from "@/stores/global";

export async function login(data: Record<string, any>) {
    myPost('user/login', data).then(async ({access_token, refresh_token}) => {
        localStorage.setItem('access_token', access_token);
        localStorage.setItem('refresh_token', refresh_token);
        MyMessage.success('登录成功！');
        await router.replace({name: 'Home'});
    })
}

export const loadAdminData = (userData: any) => {
    const {username: _username = '', type = 0, nickname = '', avatar_url = ''} = userData;
    const {username, userType, userNickName, userAvatarUrl} = storeToRefs(useGlobalStore());
    username.value = _username;
    userType.value = type;
    userNickName.value = nickname;
    userAvatarUrl.value = avatar_url;
}

export async function quitLogin() {
    localStorage.clear();
    await router.push('/');
}