import type {FormItemRule} from 'element-plus';

export type Runnable = () => void;

// 定义基础类，所有输入框类型的公共类
export abstract class BaseInputOption {
    disabled: boolean;
    placeholder: string;
    clearable: boolean;

    protected constructor(
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = true
    ) {
        this.disabled = disabled;
        this.placeholder = placeholder;
        this.clearable = clearable;
    }
}

// 定义 Text 输入框类
export class TextInputOption extends BaseInputOption {
    constructor(
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = true
    ) {
        super(disabled, placeholder, clearable);
    }
}

// 定义 Password 输入框类
export class PasswordInputOption extends BaseInputOption {
    showPassword: boolean;

    constructor(
        showPassword: boolean = false,
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = true
    ) {
        super(disabled, placeholder, clearable);
        this.showPassword = showPassword;
    }
}

// 定义 TextArea 输入框类
export class TextAreaWithRowsInputOption extends BaseInputOption {
    rows: number;

    constructor(
        rows: number,
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = true
    ) {
        super(disabled, placeholder, clearable);
        this.rows = rows;
    }
}

// 定义处理 autosize 的 TextAreaInputOption 类
export class TextAreaWithAutosizeInputOption extends BaseInputOption {
    autosize: boolean | { minRows?: number; maxRows?: number };

    constructor(
        autosize: boolean | { minRows?: number; maxRows?: number },
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = true
    ) {
        super(disabled, placeholder, clearable);
        this.autosize = autosize;
    }
}

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
    options: BaseInputOption;

    constructor(name: string, label: string, options: BaseInputOption, formRules: FormItemRule[] = []) {
        super(name, label, formRules);
        this.options = options;
    }
}

// 创建 FormInputConfigFactory 工厂类
export class FormInputConfigFactory {
    // 创建可读可写的文本输入框
    static createEditableTextInput(name: string, label: string, placeholder: string = '', rules: FormItemRule[] = []): FormInputConfig {
        const options = new TextInputOption(false, placeholder); // disabled默认为false
        return new FormInputConfig(name, label, options, rules);
    }

    // 创建只读的文本输入框
    static createReadOnlyTextInput(name: string, label: string, rules: FormItemRule[] = []): FormInputConfig {
        const options = new TextInputOption(true, ''); // disabled为true
        return new FormInputConfig(name, label, options, rules);
    }

    // 创建可读可写的密码输入框
    static createEditablePasswordInput(name: string, label: string, placeholder: string = '', showPassword: boolean = false, rules: FormItemRule[] = []): FormInputConfig {
        const options = new PasswordInputOption(showPassword, false, placeholder); // disabled默认为false
        return new FormInputConfig(name, label, options, rules);
    }

    // 创建只读的密码输入框
    static createReadOnlyPasswordInput(name: string, label: string, showPassword: boolean = false, rules: FormItemRule[] = []): FormInputConfig {
        const options = new PasswordInputOption(showPassword, true, ''); // disabled为true
        return new FormInputConfig(name, label, options, rules);
    }

    // 创建可读可写的带行数的文本区域
    static createEditableTextAreaWithRows(name: string, label: string, rows: number, placeholder: string = '', rules: FormItemRule[] = []): FormInputConfig {
        const options = new TextAreaWithRowsInputOption(rows, false, placeholder); // disabled默认为false
        return new FormInputConfig(name, label, options, rules);
    }

    // 创建只读的带行数的文本区域
    static createReadOnlyTextAreaWithRows(name: string, label: string, rows: number, rules: FormItemRule[] = []): FormInputConfig {
        const options = new TextAreaWithRowsInputOption(rows, true, ''); // disabled为true
        return new FormInputConfig(name, label, options, rules);
    }

    // 创建可读可写的自适应高度文本区域
    static createEditableTextAreaWithAutosize(name: string, label: string, autosize: boolean | {
        minRows?: number;
        maxRows?: number
    }, placeholder: string = '', rules: FormItemRule[] = []): FormInputConfig {
        const options = new TextAreaWithAutosizeInputOption(autosize, false, placeholder); // disabled默认为false
        return new FormInputConfig(name, label, options, rules);
    }

    // 创建只读的自适应高度文本区域
    static createReadOnlyTextAreaWithAutosize(name: string, label: string, autosize: boolean | {
        minRows?: number;
        maxRows?: number
    }, rules: FormItemRule[] = []): FormInputConfig {
        const options = new TextAreaWithAutosizeInputOption(autosize, true, ''); // disabled为true
        return new FormInputConfig(name, label, options, rules);
    }
}

export interface AddDialogConfig {
    addFormConfig: AbstractFormConfigItem[];
    addFormTitle?: string;
    addSubmitAction: (data: Record<string, any>, callback: () => void) => void; // 保存回调函数
}

export interface EditDialogConfig {
    editButtonName?: string;
    editButtonType?: 'primary' | 'success' | 'warning' | 'danger' | 'info' | 'text' | 'default';
    editDialogWidth?: number;
    editFormConfig: AbstractFormConfigItem[];
    editFormTitle: string;
    editSubmitAction: (data: Record<string, any>, callback: () => void) => void; // 保存回调函数
    requiredUpdateFields?: string[];//针对修改时，必须包含的字段
}



