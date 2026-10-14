import {
    addItem,
    batchAction,
    deleteItemRows,
    getOnePageItem,
    type PageCallback,
    searchItem,
    updateItem
} from "@/api/utils/baseAPI";
import type {
    BatchActionFunction,
    DeleteRowsFunction,
    GetOnePageDataFunction,
    SearchOnePageDataFunction
} from "@/components/public/table/tableTypes";

type FormSubmitFunction = (data: Record<string, any>, onSuccess: () => void) => void

export class TableCrudFactory {
    // 创建可读可写的文本输入框
    static createGetOnePageData(url: string, otherGetData: Record<string, any> = {}): GetOnePageDataFunction {
        return (currentPage: number,
                orderKey: string,
                orderDirection: string,
                callback: PageCallback,
        ) => {
            getOnePageItem(currentPage, orderKey, orderDirection, callback, url, otherGetData);
        }
    }

    static createSearchOnePageData(url: string, otherGetData: Record<string, any> = {}): SearchOnePageDataFunction {
        return (key: string,
                value: string,
                page: number,
                orderKey: string,
                orderDirection: string,
                callback: PageCallback,
        ) => {
            searchItem(key, value, page, orderKey, orderDirection, callback, url, otherGetData);
        }
    }

    static creatDeleteRows(url: string): DeleteRowsFunction {
        return (selectedRows: number[], deleteRowsSuccess: () => void) => {
            deleteItemRows(selectedRows, deleteRowsSuccess, url)
        }
    }

    static creatBatchAction(url: string, batchActionName: string = '批量操作',
                            otherPostData: Record<string, any> = {}): BatchActionFunction {
        return (selectedRows: number[], batchActionSuccess: () => void) => {
            batchAction(batchActionName, selectedRows, batchActionSuccess, url, otherPostData)
        }
    }

    static creatAddItem(url: string): FormSubmitFunction {
        return (data: Record<string, any>, onSuccess: () => void) => {
            addItem(url, data, onSuccess);
        }
    }

    static creatUpdateItem(url: string): FormSubmitFunction {
        return (data: Record<string, any>, onSuccess: () => void) => {
            updateItem(url, data, onSuccess);
        }
    }

    static creatStandardCrud(url: string) {
        return {
            addItem: TableCrudFactory.creatAddItem(url),
            updateItem: TableCrudFactory.creatUpdateItem(url),
            getOnePageData: TableCrudFactory.createGetOnePageData(url),
            searchOnePageData: TableCrudFactory.createSearchOnePageData(url),
            deleteRows: TableCrudFactory.creatDeleteRows(url),
        }
    }
}
