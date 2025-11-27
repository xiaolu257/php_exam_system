import {deleteItemRows, getOnePageItem, searchItem, updateItem} from "@/api/utils/BaseAPI";
import {submitAction} from "@/api/utils/FormData";


//管理员账号相关
export async function deleteAdminsRows(tableData: Array<any>, selectedRows: number[], deleteRowsSuccess: Function) {
    await deleteItemRows(tableData, selectedRows, deleteRowsSuccess, 'SuperManager/deleteAdminsByIds')
}

export async function getOnePageAdmins(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'SuperManager/getOnePageAdmins');
}

export async function searchAdmins(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'SuperManager/getOnePageSearchAdmins');
}

export async function addAdmin(data: Record<string, any>, callback: () => void) {
    await submitAction('添加轮播图', 'SuperManager/addAdmin', data, 'avatar', callback);
}

export async function updateAdmin(data: Record<string, any>, callback: () => void) {
    await submitAction('修改轮播图信息', 'SuperManager/updateAdmin', data, 'avatar_url', callback);
}

export async function updateAdminPassword(data: Record<string, any>, callback: () => void) {
    await updateItem(data, callback, 'SuperManager/updateAdminPassword');
}

//小程序账号相关

export async function getOnePageUsers(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'SuperManager/getOnePageUsers');
}

export async function searchUsers(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'SuperManager/getOnePageSearchUsers');
}

export async function updateUserStatus(data: Record<string, any>, callback: () => void) {
    await updateItem(data, callback, 'SuperManager/updateUserStatus');
}