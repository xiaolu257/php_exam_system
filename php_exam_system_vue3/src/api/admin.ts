import router from "@/router";
import {storeToRefs} from "pinia";
import {useGlobalStore} from "@/stores/global";

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