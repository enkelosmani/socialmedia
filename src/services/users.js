import {destroy, get, patch, post} from "@/composable/useApi.js";

export async function getUsers(pageQuery = '', filters = {}) {
    try {
        return await get('api/users' + pageQuery, {params: filters}).then(res =>{
                if(res.status === 200){
                    return res.data;
                }
                return null
            }
        );
    }catch (e) {
        return null;
    }
}

export async function showUser(id) {
    try {
        return await get('api/users/' + id).then(res =>{
                if(res.status === 200){
                    return res.data;
                }
                return null
            }
        );
    }catch (e) {
        return null;
    }
}

export async function storeUser(form) {
    try {
        return await post('api/users', form).then(res =>{
                if(res.status === 201){
                    return res.data;
                }
                return null
            }
        );
    }catch (e) {
        return null;
    }
}


export async function updateUser(form, id) {
    try {
        return await patch('api/users/' + id, form).then(res =>{
                if(res.status === 200){
                    return res.data;
                }
                return null
            }
        );
    }catch (e) {
        return null;
    }
}

export async function deleteUser(id) {
    try {
        return await destroy('api/users/' + id).then(res =>{
                if(res.status === 200){
                    return res;
                }
                return null
            }
        );
    }catch (e) {
        return null;
    }
}