// MyMessageBox.ts
import {ElMessageBox} from 'element-plus';
import 'element-plus/es/components/message-box/style/css';

class MyMessageBox {
    // `alert` 返回 `Promise<void>`
    static async alert(
        message: string,
        title = '提示',
        options: Record<string, any> = {}
    ): Promise<void> {
        await ElMessageBox.alert(message, title, {
            confirmButtonText: '确定',
            ...options,
        });
    }

    // `confirm` 也返回 `Promise<void>`
    static async confirm(
        message: string,
        title = '确认操作',
        options: Record<string, any> = {}
    ): Promise<void> {
        await ElMessageBox.confirm(message, title, {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning',
            ...options,
        });
    }

    // `prompt` 返回用户输入的字符串值，类型为 `Promise<{ value: string }>`
    static async prompt(
        message: string,
        title = '请输入',
        inputPlaceholder = '请输入内容',
        options: Record<string, any> = {}
    ): Promise<{ value: string }> {
        const {value} = await ElMessageBox.prompt(message, title, {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            inputPlaceholder,
            ...options,
        });
        return {value};
    }
}

export default MyMessageBox;
