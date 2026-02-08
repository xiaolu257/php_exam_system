import {AbstractFormConfigItem} from "@/utils/FormInputConfig";

export interface AddDialogConfig {
    title: string;
    formConfig: AbstractFormConfigItem[];
    submitAction: (data: Record<string, any>, callback: () => void) => void;
    closeDialogAfterSuccess?: boolean;
    width?: number;
}

export interface EditDialogConfig {
    title: string;
    formConfig: AbstractFormConfigItem[];
    submitAction: (data: Record<string, any>, callback: () => void) => void; // 保存回调函数
    width?: number;
    controlButtonName?: string;
    buttonSize?: 'large' | 'default' | 'small';
    buttonType?: 'primary' | 'success' | 'warning' | 'danger' | 'info' | 'text' | 'default';
    initData: Record<string, any>;
    updateIdentityFields?: string[];//针对修改时，必须包含的字段
}

export interface TableColumnEditDialogConfig {
    title: string;
    formConfig: AbstractFormConfigItem[];
    submitAction: (data: Record<string, any>, callback: () => void) => void; // 保存回调函数
    width?: number;
    controlButtonName?: string;
    buttonSize?: 'large' | 'default' | 'small';
    buttonType?: 'primary' | 'success' | 'warning' | 'danger' | 'info' | 'text' | 'default';
    updateIdentityFields?: string[];//针对修改时，必须包含的字段
}