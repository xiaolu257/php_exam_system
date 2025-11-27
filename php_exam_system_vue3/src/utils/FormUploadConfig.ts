// 定义基础类 BaseUploadOption
abstract class BaseUploadOption {
    action: string;
    filename: string;
    method: string;
    accept: string;
    showFileList: boolean;
    drag: boolean;
    maxSize: number; // maxSize 设置为必填项
    httpRequest?: (options: { file: File; onSuccess: () => void }) => void;

    protected constructor(
        action: string,
        filename: string = "file",
        method: string = "POST",
        accept: string = "image/*",
        showFileList: boolean = true,
        drag: boolean = false,
        maxSize: number = 2 * 1024 * 1024, // 默认为 2MB (2 * 1024 * 1024 字节)
        httpRequest?: (options: { file: File; onSuccess: () => void }) => void
    ) {
        this.action = action;
        this.filename = filename;
        this.method = method;
        this.accept = accept;
        this.showFileList = showFileList;
        this.drag = drag;
        this.maxSize = maxSize;
        this.httpRequest = httpRequest;
    }
}


// 单张图片上传选项类
export class SingleImageUploadOption extends BaseUploadOption {
    limit: 1;
    // 属性声明
    getThumbImageURL: (url: string) => string;
    getOriginImageURL: (url: string) => string;

    constructor(
        getThumbImageURL: (url: string) => string, // 获取缩略图 URL 的函数
        getOriginImageURL: (url: string) => string, // 获取原始图片 URL 的函数
        action: string,
        filename: string = "file",
        method: string = "POST",
        accept: string = "image/*",
        showFileList: boolean = true,
        drag: boolean = false,
        maxSize: number = 2 * 1024 * 1024, // 默认为 2MB
        httpRequest?: (options: { file: File; onSuccess: () => void }) => void
    ) {
        super(action, filename, method, accept, showFileList, drag, maxSize, httpRequest);
        this.limit = 1;
        this.getThumbImageURL = getThumbImageURL;
        this.getOriginImageURL = getOriginImageURL;
    }
}

// 多张图片上传（照片墙）选项类
export class MultipleImageUploadOption extends BaseUploadOption {
    limit: number;

    constructor(
        action: string,
        limit: number = 5,
        filename: string = "file",
        method: string = "POST",
        accept: string = "image/*",
        showFileList: boolean = true,
        drag: boolean = false,
        maxSize: number = 2 * 1024 * 1024, // 默认为 2MB
        httpRequest?: (options: { file: File; onSuccess: () => void }) => void
    ) {
        super(action, filename, method, accept, showFileList, drag, maxSize, httpRequest);
        this.limit = limit;
    }
}

import {AbstractFormConfigItem} from "@/utils/FormInputConfig";
import type {FormItemRule} from "element-plus";

export class FormUploadConfig extends AbstractFormConfigItem {
    options: BaseUploadOption;

    constructor(
        name: string,
        label: string,
        options: BaseUploadOption,
        formRules: FormItemRule[] = []
    ) {
        super(name, label, formRules);
        this.options = options;
    }
}

export class FormUploadConfigFactory {
    // 创建单张图片上传配置
    static createSingleImageUpload(
        name: string,
        label: string,
        action: string,
        getThumbImageURL: (url: string) => string, // 获取缩略图 URL 的函数
        getOriginImageURL: (url: string) => string, // 获取原始图片 URL 的函数
        accept: string = "image/*",
        showFileList: boolean = true,
        drag: boolean = false,
        maxSize: number = 2 * 1024 * 1024, // 默认为 2MB
        rules: FormItemRule[] = [],
        httpRequest?: (options: { file: File; onSuccess: () => void }) => void
    ): FormUploadConfig {
        const uploadOption = new SingleImageUploadOption(getThumbImageURL, getOriginImageURL, action, "file", "POST", accept, showFileList, drag, maxSize, httpRequest);
        return new FormUploadConfig(name, label, uploadOption, rules);
    }


    // 创建多张图片上传（照片墙）配置
    static createMultipleImageUpload(
        name: string,
        label: string,
        action: string,
        limit: number = 5,
        accept: string = "image/*",
        showFileList: boolean = true,
        drag: boolean = false,
        maxSize: number = 2 * 1024 * 1024, // 默认为 2MB
        rules: FormItemRule[] = [],
        httpRequest?: (options: { file: File; onSuccess: () => void }) => void
    ): FormUploadConfig {
        const uploadOption = new MultipleImageUploadOption(action, limit, "file", "POST", accept, showFileList, drag, maxSize, httpRequest);
        return new FormUploadConfig(name, label, uploadOption, rules);
    }

    // 创建单张图片选择（不上传）
    static createSingleImageSelector(
        name: string,
        label: string,
        getThumbImageURL: (url: string) => string, // 获取缩略图 URL 的函数
        getOriginImageURL: (url: string) => string, // 获取原始图片 URL 的函数
        rules: FormItemRule[] = [],
        showFileList: boolean = false,
        drag: boolean = false,
        maxSize: number = 2 * 1024 * 1024, // 默认为 2MB
    ): FormUploadConfig {
        const uploadOption = new SingleImageUploadOption(getThumbImageURL, getOriginImageURL, "", "file", "POST", "image/*", showFileList, drag, maxSize);
        uploadOption.httpRequest = undefined; // 不上传
        return new FormUploadConfig(name, label, uploadOption, rules);
    }


    // 创建多张图片选择（不上传）
    static createMultipleImageSelector(
        name: string,
        label: string,
        limit: number = 5,
        accept: string = "image/*",
        showFileList: boolean = true,
        drag: boolean = false,
        maxSize: number = 2 * 1024 * 1024, // 默认为 2MB
        rules: FormItemRule[] = []
    ): FormUploadConfig {
        const uploadOption = new MultipleImageUploadOption("", limit, "file", "POST", accept, showFileList, drag, maxSize);
        uploadOption.httpRequest = undefined; // 不上传
        return new FormUploadConfig(name, label, uploadOption, rules);
    }
}

