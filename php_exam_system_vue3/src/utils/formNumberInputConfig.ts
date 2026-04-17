import type {FormItemRule} from 'element-plus';
import {AbstractFormConfigItem} from "@/utils/formInputConfig";

// FormInputConfig 类继承 AbstractFormConfigItem
export class FormNumberInputConfig extends AbstractFormConfigItem {
    disabled: boolean;
    placeholder: string;

    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '') {
        super(name, label, formRules);
        this.disabled = disabled;
        this.placeholder = placeholder;
    }
}

export class IntegerInputConfig extends FormNumberInputConfig {
    min: number;
    max: number;
    step: number;
    stepStrictly: boolean;

    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                placeholder: string = '',
                min: number = Number.MIN_SAFE_INTEGER,
                max: number = Number.MAX_SAFE_INTEGER,
                step: number = 1,
                stepStrictly: boolean = false) {
        super(name, label, formRules, disabled, placeholder);
        this.min = min;
        this.max = max;
        this.step = step;
        this.stepStrictly = stepStrictly;
    }
}

export class FormNumberInputConfigFactory {
    // 创建可读可写的文本输入框
    static createEditableIntegerInput(name: string,
                                      label: string,
                                      placeholder: string = '',
                                      min: number = 0,
                                      max: number = 9999,
                                      step: number = 1,
                                      stepStrictly: boolean = false,
                                      rules: FormItemRule[] = []): IntegerInputConfig {
        return new IntegerInputConfig(name, label, rules, false, placeholder, min, max, step, stepStrictly);
    }
}



