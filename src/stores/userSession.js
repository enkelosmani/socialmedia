import {acceptHMRUpdate, defineStore} from 'pinia'
import {useStorage} from '@vueuse/core'

export const useUserStore = defineStore("userSession", () => {
    const token = useStorage('token', '');
    const user = useStorage('user', {});
    const isLoggedIn = useStorage('isLoggedIn',false);

    function setToken(newToken) {
        token.value = newToken
    }

    function setUser(userData) {
        user.value = userData
    }

    function setIsLoggedIn() {
        isLoggedIn.value = true
    }

    async function logoutUser() {
        token.value = '';
        user.value = {};
        isLoggedIn.value = false;
        return true;
    }

    return {
        token,
        user,
        isLoggedIn,
        logoutUser,
        setToken,
        setUser,
        setIsLoggedIn,
    }
})

/**
 * Pinia supports Hot Module replacement so you can edit your stores and
 * interact with them directly in your app without reloading the page.
 *
 * @see https://pinia.esm.dev/cookbook/hot-module-replacement.html
 * @see https://vitejs.dev/guide/api-hmr.html
 */
if (import.meta.hot) {
    import.meta.hot.accept(acceptHMRUpdate(useUserStore, import.meta.hot))
}
.0