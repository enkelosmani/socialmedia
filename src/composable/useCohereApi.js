import axios from 'axios'
import {useNotification} from "@kyvg/vue3-notification";

const { notify }  = useNotification()

export const api = axios.create({
    withCredentials: false,
    baseURL: 'https://api.cohere.com/v1/',
    headers: {
        'Authorization': 'Bearer 4DTbJNzHjJaYimBDqDbt8p00RyodZuDCZA32FYT2',
        "Allow-Control-Allow-Origin": "*",
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': '*/*',
        'Access-Control-Allow-Methods': ' GET, POST, PATCH, PUT, DELETE, OPTIONS',
    }
})

// api.interceptors.request.use((config) => {
//     const userSession = useUserStore()
//
//     if (userSession.token) {
//         config.headers = {
//             ...config.headers,
//             Authorization: `Bearer ${userSession.token}`,
//         }
//     }
//     return config
// })

api.interceptors.response.use(undefined, function (res) {

    switch (res.response.status) {
//         case 401:
//             router.replace({name: 'login'})
//             // window.location.href = "/login";
//             break;
//         case 419:
//             router.push({name: 'login'})
//             //window.location.href = "/login";
//             break;
//         case 400:
//             alert(res.response.data.message)
//             break;
//         case 403:
//             alert('Forbidden')
//             //window.location.href = "/403";
//             break;
//         case 422:
//             let text = '';
//             for (const value of Object.values(res.response.data.errors).concat()) {
//                 text += '\n ' + value;
//             }
//             alert(text)
//             break;
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
