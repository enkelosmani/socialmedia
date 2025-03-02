import axios from 'axios'
import {useUserStore} from "../stores/userSession";
import router from "@/router";
import {useNotification} from "@kyvg/vue3-notification";

const { notify }  = useNotification()
const backend_url = import.meta.env.VITE_BACKEND_URL

export const api = axios.create({
    withCredentials: false,
    baseURL: backend_url,
    headers: {
        "Allow-Control-Allow-Origin": "*",
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': '*/*',
        'Access-Control-Allow-Methods': ' GET, POST, PATCH, PUT, DELETE, OPTIONS',
    }
})

api.interceptors.request.use((config) => {
    const userSession = useUserStore()

    if (userSession.token) {
        config.headers = {
            ...config.headers,
            Authorization: `Bearer ${userSession.token}`,
        }
    }
    return config
})

api.interceptors.response.use(undefined, function (res) {
    switch (res.response.status) {
        case 401:
            router.push({name: 'login'})
            notify({
                title: 'Unauthenticated',
                type: "error",
                text: 'You are not logged in',
            });
            break;
        case 419:
            router.push({name: 'login'})
            notify({
                title: 'Unauthenticated',
                type: "error",
                text: 'You are not logged in',
            });
            break;
        case 400:
            notify({
                title: '',
                type: "error",
                text: res.response.data.message,
            });
            break;
        case 403:
            alert('Forbidden')
            notify({
                title: 'Forbidden',
                type: "error",
                text: res.response.data.message,
            });
            break;
        case 422:
            let text = '';
            for (const value of Object.values(res.response.data.errors).concat()) {
                text += '\n ' + value;
            }
            notify({
                title: "",
                type: "error",
                text: text,
            });
            break;
        case 500:
            notify({
                title: "500 error",
                type: "error",
                text: res.message,
            });
            break;
    }
});

export async function get(path, data) {
    return await api.get(path, data);
}

export async function post(path, data, header) {
    return await api.post(path, data, header);
}

export async function patch(path, data) {
    return await api.patch(path, data);
}

export async function destroy(path) {
    return await api.delete(path);
}
