import {post} from "@/composable/useApi.js";
import {useUserStore} from "@/stores/userSession.js";
import router from "@/router/index.js";


export async function login(form) {
    try {
        return await post('api/login', form).then(res =>{
                if (res) {
                    if (res.status === 200) {
                        useUserStore().setUser(
                            {
                                id: res.data.user.id,
                                firstname: res.data.user.firstname,
                                lastname: res.data.user.lastname,
                                email: res.data.user.email,
                            }
                        );
                        useUserStore().setToken(res.data.token);
                        useUserStore().setIsLoggedIn();
                        return res.data.user;
                    }
                }
                return null
            }
        );
    }catch (e) {
        return null;
    }
}

export async function logout() {
    try {
        const res = await post('api/logout', {}, {
            headers: { Authorization: `Bearer ${useUserStore().token}` }
        });
        if (res?.status === 200) {
            useUserStore().logoutUser();
            router.push('/login');
            return res;
        }
        return null;
    } catch (e) {
        console.error('Logout error:', e);
        return null;
    }
}