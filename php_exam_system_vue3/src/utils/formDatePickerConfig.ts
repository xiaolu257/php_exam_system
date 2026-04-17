import type {FormItemRule} from 'element-plus';
import {AbstractFormConfigItem} from "@/utils/formInputConfig";

// FormInputConfig 类继承 AbstractFormConfigItem
export class FormDatePickerConfig extends AbstractFormConfigItem {
    disabled: boolean;
    clearable: boolean;

    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                clearable: boolean = false,) {
        super(name, label, formRules);
        this.disabled = disabled;
        this.clearable = clearable;
    }
}

export class DateTimeRangePickerConfig extends FormDatePickerConfig {
    rangeSeparator: string;
    startPlaceholder: string;
    endPlaceholder: string;

    constructor(name: string,
                label: string,
                formRules: FormItemRule[] = [],
                disabled: boolean = false,
                clearable: boolean = false,
                rangeSeparator: string,
                startPlaceholder: string,
                endPlaceholder: string,
    ) {
        super(name, label, formRules, disabled, clearable);
        this.rangeSeparator = rangeSeparator;
        this.startPlaceholder = startPlaceholder;
        this.endPlaceholder = endPlaceholder;
    }
}

export class FormDatePickerConfigFactory {
    // 创建可读可写的文本输入框
    static createEditableDateTimeRangerPicker(name: string,
                                              label: string,
                                              rangeSeparator: string,
                                              startPlaceholder: string,
                                              endPlaceholder: string,
                                              rules: FormItemRule[] = []): DateTimeRangePickerConfig {
        return new DateTimeRangePickerConfig(name, label, rules, false, true, rangeSeparator, startPlaceholder, endPlaceholder);
    }
}



