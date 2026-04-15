import {ref} from 'vue'
import {defineStore} from 'pinia'
import {type SidebarMenu} from "@/router/menu";

export const useGlobalStore = defineStore('global', () => {
    // UI状态
    const isFolded = ref(false)
    // 用户信息
    const username = ref('')
    const userNickName = ref('')
    const userAvatarUrl = ref('')
    const userType = ref(0)

    const sidebarMenus = ref<any[]>([]);

    function setSidebarMenus(rawMenus: SidebarMenu[]) {
        sidebarMenus.value = rawMenus;
    }

    return {
        isFolded,
        username,
        userType,
        userNickName,
        userAvatarUrl,
        sidebarMenus,
        setSidebarMenus,
    }
})