import {addItem, deleteItemRows, getOnePageItem, type PageCallback, searchItem, updateItem} from "@/api/utils/BaseAPI";
import type {
    DeleteRowsFunction,
    GetOnePageDataFunction,
    SearchOnePageDataFunction
} from "@/components/public/Table/TableTypes";

type FormSubmitFunction = (data: Record<string, any>, onSuccess: () => void) => void

export class TableCrudFactory {
    // 创建可读可写的文本输入框
    static createGetOnePageData(url: string): GetOnePageDataFunction {
        return (currentPage: number,
                orderKey: string,
                orderDirection: string,
                callback: PageCallback) => {
            getOnePageItem(currentPage, orderKey, orderDirection, callback, url);
        }
    }

    static createSearchOnePageData(url: string): SearchOnePageDataFunction {
        return (key: string,
                value: string,
                page: number,
                orderKey: string,
                orderDirection: string,
                callback: PageCallback) => {
            searchItem(key, value, page, orderKey, orderDirection, callback, url);
        }
    }

    static creatDeleteRows(url: string): DeleteRowsFunction {
        return (selectedRows: number[], deleteRowsSuccess: () => void) => {
            deleteItemRows(selectedRows, deleteRowsSuccess, url)
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
