// 定义基础类 BaseSelectOption
import {AbstractFormConfigItem} from "@/utils/FormInputConfig";
import type {FormItemRule} from "element-plus";
import type {ComputedRef} from "vue";

abstract class BaseSelectOption {
    disabled: boolean;
    placeholder: string;
    clearable: boolean;

    protected constructor(
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false
    ) {
        this.disabled = disabled;
        this.placeholder = placeholder;
        this.clearable = clearable;
    }
}

// 单选下拉框选项类
export class SingleSelectOption extends BaseSelectOption {
    options: Array<{ label: string; value: any }>;

    constructor(
        options: Array<{ label: string; value: any }>,
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false
    ) {
        super(disabled, placeholder, clearable);
        this.options = options;
    }
}

// 关联单选下拉框选项类
export class AssociateSingleSelectOption extends BaseSelectOption {
    associateFunction: (formData: Record<string, any>) => ComputedRef<{ label: string; value: any }[]>;

    constructor(
        associateFunction: (formData: Record<string, any>) => ComputedRef<{ label: string; value: any }[]>,
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false
    ) {
        super(disabled, placeholder, clearable);
        this.associateFunction = associateFunction;
    }
}

// 多选下拉框选项类
export class MultipleSelectOption extends BaseSelectOption {
    options: Array<{ label: string; value: any }>;
    multiple: boolean; // 标记为多选

    constructor(
        options: Array<{ label: string; value: any }>,
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false
    ) {
        super(disabled, placeholder, clearable);
        this.options = options;
        this.multiple = true;
    }
}

// FormSelectConfig 类继承 AbstractFormConfigItem
export class FormSelectConfig extends AbstractFormConfigItem {
    options: BaseSelectOption;

    constructor(name: string, label: string, options: BaseSelectOption, formRules: FormItemRule[] = []) {
        super(name, label, formRules);
        this.options = options;
    }
}

// 创建 FormSelectConfigFactory 工厂类
export class FormSelectConfigFactory {
    // 创建单选下拉框
    static createSingleSelect(
        name: string,
        label: string,
        options: Array<{ label: string; value: any }>,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormSelectConfig {
        const selectOptions = new SingleSelectOption(options, false, placeholder);
        return new FormSelectConfig(name, label, selectOptions, rules);
    }

    // 创建关联单选下拉框
    static createAssociateSingleSelect(
        name: string,
        label: string,
        associateFunction: (formData: Record<string, any>) => ComputedRef<{ label: string; value: any }[]>,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormSelectConfig {
        const selectOptions = new AssociateSingleSelectOption(associateFunction, false, placeholder);
        return new FormSelectConfig(name, label, selectOptions, rules);
    }

    // 创建多选下拉框
    static createMultipleSelect(
        name: string,
        label: string,
        options: Array<{ label: string; value: any }>,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormSelectConfig {
        const selectOptions = new MultipleSelectOption(options, false, placeholder);
        return new FormSelectConfig(name, label, selectOptions, rules);
    }
}
