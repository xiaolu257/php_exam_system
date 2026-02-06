import {deleteItemRows, getOnePageItem, searchItem} from "@/api/utils/BaseAPI";

//单选题管理相关
export async function deleteSingleChoiceQuestionsRows(tableData: Array<any>, selectedRows: number[], deleteRowsSuccess: Function) {
    await deleteItemRows(tableData, selectedRows, deleteRowsSuccess, 'OperationManager/deleteSingleChoiceQuestionsByIds')
}

export async function getOnePageSingleChoiceQuestions(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'single-choice-question');
}

export async function searchSingleChoiceQuestions(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'single-choice-question');
}