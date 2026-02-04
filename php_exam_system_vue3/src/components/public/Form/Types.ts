import {AbstractFormConfigItem} from "@/utils/FormInputConfig";

export interface AddDialogConfig {
    title: string;
    formConfig: AbstractFormConfigItem[];
    submitAction: (data: Record<string, any>, callback: () => void) => void;
    closeDialogAfterSuccess?: boolean;
    width?: number;
}