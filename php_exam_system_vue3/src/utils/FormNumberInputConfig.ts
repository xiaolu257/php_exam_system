// 定义数字输入框的配置类
import {AbstractFormConfigItem} from "@/utils/FormInputConfig";
import type {FormItemRule} from 'element-plus';

// 定义数字输入框的配置类
export class NumberInputOption {
    min: number;
    max: number;
    step: number;
    precision?: number; // 可选，控制小数位
    controlsPosition?: 'right' | ''; // 数字增减按钮位置（右侧 or 默认）
    disabled: boolean;
    placeholder: string;

    constructor(
        min: number = 0,
        max: number = Infinity,
        step: number = 1,
        precision?: number,
        controlsPosition?: 'right' | '',
        disabled: boolean = false,
        placeholder: string = ''
    ) {
        this.min = min;
        this.max = max;
        this.step = step;
        this.precision = precision;
        this.controlsPosition = controlsPosition;
        this.disabled = disabled;
        this.placeholder = placeholder;
    }
}

// FormInputConfig 继承 AbstractFormConfigItem
export class FormNumberInputConfig extends AbstractFormConfigItem {
    options: NumberInputOption;

    constructor(name: string, label: string, options: NumberInputOption, formRules: FormItemRule[] = []) {
        super(name, label, formRules);
        this.options = options;
    }
}


// 扩展 FormInputConfigFactory，加入数字输入框的工厂方法
export class FormNumberInputConfigFactory {
    // ✅ 创建普通的数字输入框
    static createNumberInput(
        name: string,
        label: string,
        min: number = 0,
        max: number = Infinity,
        step: number = 1,
        precision?: number,
        controlsPosition?: 'right' | '',
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormNumberInputConfig {
        const options = new NumberInputOption(min, max, step, precision, controlsPosition, false, placeholder);
        return new FormNumberInputConfig(name, label, options, rules);
    }

    // ✅ 创建整数输入框（不允许小数）
    static createIntegerInput(
        name: string,
        label: string,
        min: number = 0,
        max: number = Infinity,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormNumberInputConfig {
        const options = new NumberInputOption(min, max, 1, 0, '', false, placeholder);
        return new FormNumberInputConfig(name, label, options, rules);
    }

    // ✅ 创建带步进值的数字输入框（例如 0.5 递增）
    static createStepNumberInput(
        name: string,
        label: string,
        step: number = 0.5,
        min: number = 0,
        max: number = 100,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormNumberInputConfig {
        const options = new NumberInputOption(min, max, step, 2, '', false, placeholder);
        return new FormNumberInputConfig(name, label, options, rules);
    }

    // ✅ 创建右侧控制按钮的数字输入框
    static createRightControlNumberInput(
        name: string,
        label: string,
        min: number = 0,
        max: number = 100,
        step: number = 1,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormNumberInputConfig {
        const options = new NumberInputOption(min, max, step, 2, 'right', false, placeholder);
        return new FormNumberInputConfig(name, label, options, rules);
    }

    // ✅ 创建只读的数字输入框
    static createReadOnlyNumberInput(
        name: string,
        label: string,
        min: number = 0,
        max: number = Infinity,
        step: number = 1,
        precision?: number,
        placeholder: string = '',
        rules: FormItemRule[] = []
    ): FormNumberInputConfig {
        const options = new NumberInputOption(min, max, step, precision, '', true, placeholder);
        return new FormNumberInputConfig(name, label, options, rules);
    }
}

