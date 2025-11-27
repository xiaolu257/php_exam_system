import {TableColumn} from "@/utils/MyTableTypeClass";

export interface TableConfig {
    tableColumns: TableColumn[],
    getOnePageData: (page: number, orderKey: string, orderDirection: string, callback: Function) => void,
    searchData: (key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) => void
    deleteRows?: (tableData: Array<any>, selectedRows: number[], deleteRowsSuccess: Function) => void,
}
