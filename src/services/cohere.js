import {post} from "@/composable/useCohereApi.js";

export async function checkText(form) {
    try {
        return await post('chat', form).then(res => {
                if (res.status === 200) {
                    return res.data;
                }
                return null
            }
        );
    } catch (e) {
        return null;
    }
}