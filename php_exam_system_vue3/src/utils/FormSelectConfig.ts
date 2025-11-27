// 定义基础类 BaseSelectOption
import {AbstractFormConfigItem} from "@/utils/FormInputConfig";
import type {FormItemRule} from "element-plus";

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

// 分组下拉框选项类
export class GroupedSelectOption extends BaseSelectOption {
    groupedOptions: Array<{ groupLabel: string; options: Array<{ label: string; value: any }> }>;

    constructor(
        groupedOptions: Array<{ groupLabel: string; options: Array<{ label: string; value: any }> }>,
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false
    ) {
        super(disabled, placeholder, clearable);
        this.groupedOptions = groupedOptions;
    }
}

// 分组多选选项类
export class GroupedMultipleSelectOption extends GroupedSelectOption {
    multiple: boolean;

    constructor(
        groupedOptions: { groupLabel: string; options: { label: string; value: any }[] }[],
        multiple: boolean = true,
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false
    ) {
        super(groupedOptions, disabled, placeholder, clearable);
        this.multiple = multiple;
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

    // 创建分组下拉框
    static createGroupedSelect(
        name: string,
        label: string,
        groupedOptions: Array<{ groupLabel: string; options: Array<{ label: string; value: any }> }>,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormSelectConfig {
        const selectOptions = new GroupedSelectOption(groupedOptions, false, placeholder);
        return new FormSelectConfig(name, label, selectOptions, rules);
    }

    // 创建分组多选
    static createGroupedMultipleSelect(
        name: string,
        label: string,
        groupedOptions: { groupLabel: string; options: { label: string; value: any }[] }[],
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormSelectConfig {
        const options = new GroupedMultipleSelectOption(groupedOptions, true, false, placeholder);
        return new FormSelectConfig(name, label, options, rules);
    }

}
