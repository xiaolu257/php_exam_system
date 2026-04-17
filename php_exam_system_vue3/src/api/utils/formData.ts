// 通用处理 FormData 的函数
import {myPost} from "@/api/utils/axios";
import MyMessage from "@/utils/myMessage";

export const buildFormData = (data: Record<string, any>, avatarKey: string) => {
    const formData = new FormData();

    // 封装函数，处理文件类型数据
    const appendData = (key: string, value: any) => {
        if (value instanceof Array && value[0] instanceof File) {
            formData.append(key, value[0]);
        } else if (value !== undefined && value !== null) {
            formData.append(key, value);
        }
    };

    // 处理文件字段（例如 avatar）
    if (data[avatarKey]) {
        appendData(avatarKey, data[avatarKey]);
    }

    // 处理其他数据
    Object.keys(data).forEach(key => {
        if (key !== avatarKey) { // 避免重复处理 avatar
            appendData(key, data[key]);
        }
    });

    return formData;
};

// 通用的提交处理函数
export const submitAction = async (controlName: string, url: string, data: Record<string, any>, avatarKey: string, callback: () => void) => {
    const formData = buildFormData(data, avatarKey);
    myPost(url, formData, true, {
        headers: {'Content-Type': 'multipart/form-data'}
    }).then(({msg}) => {
        MyMessage.success(msg);
        callback();
    })
};