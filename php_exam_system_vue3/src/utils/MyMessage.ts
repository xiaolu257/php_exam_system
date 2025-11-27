import {ElMessage} from 'element-plus'
class MyMessage {

    static success(message: string, showClose = true, duration = 3000, plain = false) {
        return ElMessage({
            type: 'success',
            message,
            showClose,
            duration,
            plain
        })
    }

    static error(message: string, showClose = true, duration = 5000, plain = false) {
        return ElMessage({
            type: 'error',
            message,
            showClose,
            duration,
            plain
        })
    }

    static warning(message: string, showClose = true, duration = 5000, plain = false) {
        return ElMessage({
            type: 'warning',
            message,
            showClose,
            duration,
            plain
        })
    }

    static info(message: string, showClose = true, duration = 3000, plain = false) {
        return ElMessage({
            type: 'info',
            message,
            showClose,
            duration,
            plain
        })
    }
}

export default MyMessage
