import {AbstractFormConfigItem} from "@/utils/formInputConfig";
import type {FormItemRule} from "element-plus";
import type {ComputedRef} from "vue";

/**
 * =========================
 * 统一 Option 类型（核心）
 * =========================
 */
export type SelectOption<T = any> = {
    label: string
    value: T
}

/**
 * =========================
 * 树形 Option（推荐升级）
 * =========================
 */
export type TreeSelectOption<T = any> = SelectOption<T> & {
    children?: TreeSelectOption<T>[]
}

/**
 * =========================
 * Base Select Config
 * =========================
 */
export class FormSelectConfig extends AbstractFormConfigItem {
    disabled: boolean;
    placeholder: string;
    clearable: boolean;

    constructor(
        name: string,
        label: string,
        formRules: FormItemRule[] = [],
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false
    ) {
        super(name, label, formRules);
        this.disabled = disabled;
        this.placeholder = placeholder;
        this.clearable = clearable;
    }
}

/**
 * =========================
 * 普通 Select
 * =========================
 */
export class CommonSelectConfig extends FormSelectConfig {
    multiple: boolean;
    options: SelectOption[];

    constructor(
        name: string,
        label: string,
        formRules: FormItemRule[] = [],
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false,
        multiple: boolean = false,
        options: SelectOption[] = [],
    ) {
        super(name, label, formRules, disabled, placeholder, clearable);
        this.multiple = multiple;
        this.options = options;
    }
}

/**
 * =========================
 * 关联 Select
 * =========================
 */
export class AssociateSelectConfig extends FormSelectConfig {
    multiple: boolean;
    dependKey: string;
    associateFunction: (dependValue: any) => ComputedRef<SelectOption[]>;

    constructor(
        name: string,
        label: string,
        formRules: FormItemRule[] = [],
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false,
        multiple: boolean = false,
        dependKey: string,
        associateFunction: (dependValue: any) => ComputedRef<SelectOption[]>,
    ) {
        super(name, label, formRules, disabled, placeholder, clearable);
        this.multiple = multiple;
        this.dependKey = dependKey;
        this.associateFunction = associateFunction;
    }
}

/**
 * =========================
 * Tree Select
 * =========================
 */
export class TreeSelectConfig extends FormSelectConfig {
    multiple: boolean;
    treeData: TreeSelectOption[];
    props: {
        label: string;
        value: string;
        children: string;
    };
    checkStrictly: boolean;

    constructor(
        name: string,
        label: string,
        formRules: FormItemRule[] = [],
        disabled: boolean = false,
        placeholder: string = '',
        clearable: boolean = false,
        multiple: boolean = false,
        treeData: TreeSelectOption[] = [],
        props: { label: string; value: string; children: string } = {
            label: 'label',
            value: 'value',
            children: 'children',
        },
        checkStrictly: boolean = false
    ) {
        super(name, label, formRules, disabled, placeholder, clearable);
        this.multiple = multiple;
        this.treeData = treeData;
        this.props = props;
        this.checkStrictly = checkStrictly;
    }
}

/**
 * =========================
 * Factory 工厂类
 * =========================
 */
export class FormSelectConfigFactory {

    // 普通单选
    static createCommonSingleSelect(
        name: string,
        label: string,
        options: SelectOption[],
        placeholder: string = '',
        formRules: FormItemRule[] = [],
    ): FormSelectConfig {
        return new CommonSelectConfig(
            name,
            label,
            formRules,
            false,
            placeholder,
            true,
            false,
            options
        );
    }

    // 关联单选
    static createAssociateSingleSelect(
        name: string,
        label: string,
        dependKey: string,
        associateFunction: (dependValue: any) => ComputedRef<SelectOption[]>,
        placeholder: string = '',
        formRules: FormItemRule[] = [],
    ): FormSelectConfig {
        return new AssociateSelectConfig(
            name,
            label,
            formRules,
            false,
            placeholder,
            true,
            false,
            dependKey,
            associateFunction
        );
    }

    // 关联多选
    static createAssociateMultipleSelect(
        name: string,
        label: string,
        dependKey: string,
        associateFunction: (dependValue: any) => ComputedRef<SelectOption[]>,
        placeholder: string = '',
        formRules: FormItemRule[] = [],
    ): FormSelectConfig {
        return new AssociateSelectConfig(
            name,
            label,
            formRules,
            false,
            placeholder,
            true,
            true,
            dependKey,
            associateFunction
        );
    }

    // 树形单选
    static createTreeSingleSelect(
        name: string,
        label: string,
        treeData: TreeSelectOption[],
        placeholder: string = '',
        formRules: FormItemRule[] = [],
    ): FormSelectConfig {
        return new TreeSelectConfig(
            name,
            label,
            formRules,
            false,
            placeholder,
            true,
            false,
            treeData
        );
    }

    // 树形多选
    static createTreeMultipleSelect(
        name: string,
        label: string,
        treeData: TreeSelectOption[],
        placeholder: string = '',
        formRules: FormItemRule[] = [],
    ): FormSelectConfig {
        return new TreeSelectConfig(
            name,
            label,
            formRules,
            false,
            placeholder,
            true,
            true,
            treeData
        );
    }
}