import {deleteItemRows, getOnePageItem, searchItem} from "@/api/utils/BaseAPI";

//单选题管理相关
export function deleteSingleChoiceQuestionsRows(selectedRows: number[], deleteRowsSuccess: () => void) {
    deleteItemRows(selectedRows, deleteRowsSuccess, 'single-choice-question')
}

export async function getOnePageSingleChoiceQuestions(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    getOnePageItem(currentPage, orderKey, orderDirection, callback, 'single-choice-question');
}

export async function searchSingleChoiceQuestions(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    searchItem(key, value, page, orderKey, orderDirection, callback, 'single-choice-question');
}