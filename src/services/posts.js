import { destroy, get, patch, post } from "@/composable/useApi.js";

export async function getPosts(pageQuery = '', filters = {}) {
    try {
        const res = await get('api/posts' + pageQuery, { params: filters });
        if (res.status === 200) {
            return res; // Return full response object
        }
        console.error('Failed to fetch posts:', res.status);
        return null;
    } catch (e) {
        console.error('Error in getPosts:', e);
        return null;
    }
}

export async function showPost(id) {
    try {
        const res = await get('api/posts/' + id);
        if (res.status === 200) {
            return res; // Return full response object
        }
        console.error('Failed to fetch post:', res.status);
        return null;
    } catch (e) {
        console.error('Error in showPost:', e);
        return null;
    }
}

export async function storePost(form) {
    try {
        const res = await post('api/posts', form, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        console.log("Raw response from storePost:", res);
        if (res && (res.status === 201 || res.status === 200)) {
            return res;
        }
        console.error('Failed to create post:', res?.status, res?.data);
        return null;
    } catch (e) {
        console.error('Error in storePost:', e?.data || e.message);
        throw e; // Propagate the error with response data
    }
}

export async function updatePost(form, id) {
    try {
        const res = await patch('api/posts/' + id, form);
        if (res.status === 200) {
            return res; // Return full response object
        }
        console.error('Failed to update post:', res.status);
        return null;
    } catch (e) {
        console.error('Error in updatePost:', e);
        return null;
    }
}

export async function deletePost(id) {
    try {
        const res = await destroy('api/posts/' + id);
        if (res.status === 200) {
            return res; // Return full response object
        }
        console.error('Failed to delete post:', res.status);
        return null;
    } catch (e) {
        console.error('Error in deletePost:', e);
        return null;
    }
}