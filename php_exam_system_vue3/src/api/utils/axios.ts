// utils/myAxios.ts
import axios, {type AxiosError, type AxiosRequestConfig} from 'axios';
import {ElLoading, ElMessage} from 'element-plus';
import {useFingerprint} from "@/utils/fingerprint";
import {quitLogin} from "@/api/Admin";
import MyMessage from "@/utils/MyMessage";

let loadingInstance: any = null;

// const baseURL = 'https://xiaolu.cn/api';
const baseURL = 'http://192.168.217.130:9501';

const myAxios = axios.create({
    baseURL,
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
    },
});
let firstTimeLoadFingerprint = true;

function filterEmptyFields(data: Record<string, any>) {
    const result: Record<string, any> = {};

    Object.entries(data).forEach(([key, value]) => {
        if (value === "" || value === null || value === undefined) {
            return;
        }
        result[key] = value;
    });

    return result;
}

// 请求拦截器：附加 token
myAxios.interceptors.request.use(
    async (config) => {
        const token = localStorage.getItem('access_token');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        // 获取设备指纹：第一次强制生成，之后读取缓存
        config.headers['Fingerprint'] = await useFingerprint(firstTimeLoadFingerprint);
        firstTimeLoadFingerprint = false;

        // 过滤请求数据
        if (config.data && typeof config.data === "object") {
            config.data = filterEmptyFields(config.data);
        }

        // 如果有 GET 参数
        if (config.params && typeof config.params === "object") {
            config.params = filterEmptyFields(config.params);
        }
        return config;
    },
    (error) => Promise.reject(error)
);

interface MyAxiosExtraConfig extends AxiosRequestConfig {
    silent?: boolean
}

export interface ApiError {
    msg: string,
    data?: any
    code?: number
}

// 响应拦截器：不处理结构，保留完整 response，方便后面统一判断状态
myAxios.interceptors.response.use(
    (response) => response,
    async (error: AxiosError<ApiError>) => {
        const config = error.config as MyAxiosExtraConfig

        const msg = error.response?.data.msg || '请求失败';

        if (!config?.silent) {
            ElMessage.error(msg);
        }

        if (error.response?.status === 401) {
            await quitLogin()
        }

        return Promise.reject(error.response?.data);
    }
);

// 通用请求函数（小程序风格处理 code/status）
const myRequest = async (
    method: string,
    url: string,
    data: any = {},
    showLoading = true,
    extraConfig: MyAxiosExtraConfig = {} // ✅ 新增：接收额外配置
): Promise<any> => {
    if (showLoading) {
        loadingInstance = ElLoading.service({
            text: '加载中...',
            fullscreen: true,
        });
    }

    const config = {
        method,
        url,
        ...(method.toUpperCase() === 'GET' ? {params: data} : {data}),
        ...extraConfig, // ✅ 合并额外配置
    };

    return myAxios(config)
        .then((res) => {
            if (res.status === 200) {
                return res.data;
            } else {
                MyMessage.error('服务器异常，状态码：' + res.status);
                return Promise.reject(res);
            }
        })
        .finally(() => {
            if (showLoading && loadingInstance) {
                loadingInstance.close();
            }
        });
};


// 快捷请求方法
const myGet = (url: string, data = {}, showLoading = true) => myRequest('GET', url, data, showLoading);
const myPost = (url: string, data = {}, showLoading = true, extraConfig: MyAxiosExtraConfig = {}) => myRequest('POST', url, data, showLoading, extraConfig);
const myPut = (url: string, data = {}, showLoading = true) => myRequest('PUT', url, data, showLoading);
const myDel = (url: string, data = {}, showLoading = true) => myRequest('DELETE', url, data, showLoading);

// 默认导出 + 命名导出
export {myAxios, myRequest, myGet, myPost, myPut, myDel};
