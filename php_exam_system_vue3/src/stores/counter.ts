import {computed, ref} from 'vue'
import {defineStore} from 'pinia'

defineStore('counter', () => {
    const count = ref(0)
    const doubleCount = computed(() => count.value * 2)

    function increment() {
        count.value++
    }

    return {count, doubleCount, increment}
});
export const useGlobalStore = defineStore('global', () => {
    const isFolded = ref(false);
    const username = ref('');
    const userType = ref(0);
    const ipAddress = ref('');
    const userNickName = ref('');
    const userAvatarUrl = ref('');
    return {isFolded, username, userType, ipAddress, userNickName, userAvatarUrl};
})