import type {FormItemRule} from 'element-plus';

// 抽象类 AbstractFormConfigItem
export abstract class AbstractFormConfigItem {
    name: string;
    label: string;
    rules: FormItemRule[]; // 必选属性，校验规则

    protected constructor(name: string, label: string, rules: FormItemRule[] = []) {
        this.name = name;
        this.label = label;
        this.rules = rules; // 默认值为空对象
    }
}

// FormInputConfig 类继承 AbstractFormConfigItem
export class FormInputConfig extends AbstractFormConfigItem {
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

export class TextInputConfig extends FormInputConfig {
    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '',
                clearable: boolean = false) {
        super(name, label, formRules, disabled, placeholder, clearable);
    }
}

type TextAreaAutosize = boolean | { minRows?: number; maxRows?: number };

export class TextAreaInputConfig extends FormInputConfig {
    autosize: TextAreaAutosize

    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '',
                clearable: boolean = false,
                autosize: TextAreaAutosize) {
        super(name, label, formRules, disabled, placeholder, clearable);
        this.autosize = autosize;
    }
}

export class OptionsListInputConfig extends FormInputConfig {
    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '',
                clearable: boolean = false) {
        super(name, label, formRules, disabled, placeholder, clearable);
    }
}

export class PasswordInputConfig extends FormInputConfig {
    showPassword: boolean;

    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '',
                clearable: boolean = false,
                showPassword: boolean) {
        super(name, label, formRules, disabled, placeholder, clearable);
        this.showPassword = showPassword;
    }
}

// 创建 FormInputConfigFactory 工厂类
export class FormInputConfigFactory {
    // 创建可读可写的文本输入框
    static createEditableTextInput(name: string,
                                   label: string,
                                   placeholder: string = '',
                                   rules: FormItemRule[] = []): FormInputConfig {
        return new TextInputConfig(name, label, rules, false, placeholder, true);
    }

    // 创建只读的文本输入框
    static createReadOnlyTextInput(name: string, label: string, rules: FormItemRule[] = []): FormInputConfig {
        return new TextInputConfig(name, label, rules, true, '', true);
    }

    // 创建可读可写的文本域输入框
    static createEditableTextAreaInput(name: string,
                                       label: string,
                                       placeholder: string = '',
                                       autosize: TextAreaAutosize = false,
                                       rules: FormItemRule[] = []): FormInputConfig {
        return new TextAreaInputConfig(name, label, rules, false, placeholder, true, autosize);
    }

    // 创建可读可写的密码输入框
    static createEditablePasswordInput(name: string,
                                       label: string,
                                       placeholder: string = '',
                                       showPassword: boolean = false,
                                       rules: FormItemRule[] = []): FormInputConfig {
        return new PasswordInputConfig(name, label, rules, false, placeholder, true, showPassword);
    }

    // 创建动态多路文本输入框
    static createDynamicMultipleTextInput(name: string,
                                          label: string,
                                          placeholder: string = '',
                                          rules: FormItemRule[] = []): FormInputConfig {
        return new OptionsListInputConfig(name, label, rules, false, placeholder, true);
    }
}



