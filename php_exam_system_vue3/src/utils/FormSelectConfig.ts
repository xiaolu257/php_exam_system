// 定义基础类 BaseSelectOption
import {AbstractFormConfigItem} from "@/utils/FormInputConfig";
import type {FormItemRule} from "element-plus";
import type {ComputedRef} from "vue";
// FormSelectConfig 类继承 AbstractFormConfigItem
export class FormSelectConfig extends AbstractFormConfigItem {
    disabled: boolean;
    placeholder: string;
    clearable: boolean;
    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '',
                clearable: boolean = false) {
        super(name, label, formRules);
        this.disabled = disabled;
        this.placeholder = placeholder;
        this.clearable = clearable;
    }
}

export class AssociateSelectConfig extends FormSelectConfig {
    multiple: boolean;
    associateFunction: (formData: Record<string, any>) => ComputedRef<{ label: string; value: any }[]>;
    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '',
                clearable: boolean = false,
                multiple: boolean = false,
                associateFunction: (formData: Record<string, any>) => ComputedRef<{ label: string; value: any }[]>,) {
        super(name, label, formRules,disabled,placeholder,clearable);
        this.multiple = multiple;
        this.associateFunction = associateFunction;
    }
}
// 创建 FormSelectConfigFactory 工厂类
export class FormSelectConfigFactory {
    // 创建关联单选下拉框
    static createAssociateSingleSelect(
        name: string,
        label: string,
        associateFunction: (formData: Record<string, any>) => ComputedRef<{label: string, value: any}[]>,
        placeholder: string = '',
        formRules: FormItemRule[]= [],
    ): FormSelectConfig {
        return new AssociateSelectConfig(name, label,formRules,false,placeholder,true,false,associateFunction);
    }
    // 创建关联多选下拉框
    static createAssociateMultipleSelect(
        name: string,
        label: string,
        associateFunction: (formData: Record<string, any>) => ComputedRef<{label: string, value: any}[]>,
        placeholder: string = '',
        formRules: FormItemRule[]= [],
    ): FormSelectConfig {
        return new AssociateSelectConfig(name, label,formRules,false,placeholder,true,true,associateFunction);
    }
}
