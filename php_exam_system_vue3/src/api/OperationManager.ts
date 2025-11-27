import {addItem, deleteItemRows, getOnePageItem, searchItem, updateItem} from "@/api/utils/BaseAPI";
import {submitAction} from "@/api/utils/FormData";

//工作分类相关
export async function deleteJobCategoriesRows(tableData: Array<any>, selectedRows: number[], deleteRowsSuccess: Function) {
    await deleteItemRows(tableData, selectedRows, deleteRowsSuccess, 'OperationManager/deleteJobCategoriesByIds')
}

export async function getOnePageJobCategories(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'OperationManager/getOnePageJobCategories');
}

export async function searchJobCategories(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'OperationManager/getOnePageSearchJobCategories');
}

export async function addJobCategories(data: Record<string, any>, callback: () => void) {
    await addItem(data, callback, 'OperationManager/addJobCategories');
}

export async function updateJobCategories(data: Record<string, any>, callback: () => void) {
    await updateItem(data, callback, 'OperationManager/updateJobCategories');
}

//工作区域相关
export async function deleteJobAreasRows(tableData: Array<any>, selectedRows: number[], deleteRowsSuccess: Function) {
    await deleteItemRows(tableData, selectedRows, deleteRowsSuccess, 'OperationManager/deleteJobAreasByIds')
}

export async function getOnePageJobAreas(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'OperationManager/getOnePageJobAreas');
}

export async function searchJobAreas(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'OperationManager/getOnePageSearchJobAreas');
}

export async function addJobAreas(data: Record<string, any>, callback: () => void) {
    await addItem(data, callback, 'OperationManager/addJobAreas');
}

export async function updateJobAreas(data: Record<string, any>, callback: () => void) {
    await updateItem(data, callback, 'OperationManager/updateJobAreas');
}

//工作区域相关
export async function deleteBannersRows(tableData: Array<any>, selectedRows: number[], deleteRowsSuccess: Function) {
    await deleteItemRows(tableData, selectedRows, deleteRowsSuccess, 'OperationManager/deleteBannersByIds')
}

export async function getOnePageBanners(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'OperationManager/getOnePageBanners');
}

export async function searchBanners(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'OperationManager/getOnePageSearchBanners');
}

export async function addBanner(data: Record<string, any>, callback: () => void) {
    await submitAction('添加轮播图', 'OperationManager/addBanner', data, 'image', callback);
}

export async function updateBanner(data: Record<string, any>, callback: () => void) {
    await submitAction('修改轮播图信息', 'OperationManager/updateBanner', data, 'image_url', callback);
}