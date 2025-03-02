import {destroy, get, patch, post} from "@/composable/useApi.js";

export async function getContent(pageQuery = '', filters = {}) {
    try {
        return await get('api/contents' + pageQuery, {params: filters}).then(res =>{
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

export async function showContent(id) {
    try {
        return await get('api/contents/' + id).then(res =>{
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

export async function storeContent(form) {
    try {
        return await post('api/contents', form).then(res =>{
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


export async function updateContent(form, id) {
    try {
        return await patch('api/contents/' + id, form).then(res =>{
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

export async function deleteContent(id) {
    try {
        return await destroy('api/contents/' + id).then(res =>{
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