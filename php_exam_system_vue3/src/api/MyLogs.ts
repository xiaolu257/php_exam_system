import {deleteItemRows, getOnePageItem, searchItem} from "@/api/utils/BaseAPI";
import {submitAction} from "@/api/utils/FormData";

export async function deleteMyLogsRows(tableData: Array<any>, selectedRows: number[], deleteRowsSuccess: Function) {
    await deleteItemRows(tableData, selectedRows, deleteRowsSuccess, 'MyLogs/deleteMyLogsByIds')
}

export async function getOnePageMyLogs(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'MyLogs/getOnePageMyLogs');
}

export async function searchMyLogs(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'MyLogs/getOnePageSearchMyLogs');
}

export async function addMyLogs(data: Record<string, any>, callback: () => void) {
    await submitAction('添加开发记录', 'MyLogs/addMyLog', data, 'avatar', callback);
}

export async function updateMyLogs(data: Record<string, any>, callback: () => void) {
    await submitAction('修改轮开发记录', 'MyLogs/updateMyLog', data, 'log_image_url', callback);
}

/*

 */