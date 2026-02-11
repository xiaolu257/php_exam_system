import {type PageCallback} from "@/api/utils/BaseAPI";

type myTextType = 'primary' | 'danger' | 'success' | 'info';
type ImageObjectFitType = '' | 'fill' | 'contain' | 'cover' | 'none' | 'scale-down';

export abstract class TableColumn {
    min_width: string | number;
    prop: string;
    label: string;
    sortable: boolean | 'custom';  // 修改为支持 Element Plus 的 "custom"
    searchable: boolean;
    fixed: 'left' | 'right' | false;  // 修改为支持 el-table 固定列的属性

    protected constructor(
        min_width: string | number = 100,
        prop: string,
        label: string,
        sortable: boolean | 'custom' = false,  // 默认不排序
        searchable: boolean = true,
        fixed: 'left' | 'right' | false = false  // 默认不固定
    ) {
        this.min_width = min_width;
        this.prop = prop;
        this.label = label;
        this.sortable = sortable;
        this.searchable = searchable;
        this.fixed = fixed;
    }
}


export class TextTableColumn extends TableColumn {
    textType: myTextType;  // 文本类型（例如 primary, danger 等）

    constructor(
        min_width: string | number = 100,  // 默认最小宽度为 100
        prop: string,                      // 列名
        label: string,                     // 列标签
        sortable: boolean | 'custom' = false,         // 默认不可排序
        searchable: boolean = true,        // 默认可搜索
        fixed: 'left' | 'right' | false = false,   // 默认不固定
        textType: myTextType = 'info'      // 默认文本类型为 'info'
    ) {
        super(min_width, prop, label, sortable, searchable, fixed);
        this.textType = textType;
    }
}


type ImageURLGetter = (url: string) => string;

export class ImageTableColumn extends TableColumn {
    fit: ImageObjectFitType;  // 图片适配方式（例如 cover, contain 等）
    getOriginImageURL: ImageURLGetter;  // 获取原始图片 URL
    getThumbImageURL: ImageURLGetter;   // 获取缩略图图片 URL

    constructor(
        min_width: string | number = 100,  // 默认最小宽度为 100
        prop: string,                      // 列名
        label: string,                     // 列标签
        getOriginImageURL: ImageURLGetter, // 获取原始图片 URL 的函数
        getThumbImageURL: ImageURLGetter,  // 获取缩略图图片 URL 的函数
        fixed: 'left' | 'right' | false = false,   // 默认不固定
        fit: ImageObjectFitType = 'cover'  // 默认图片适配方式为 'cover'
    ) {
        super(min_width, prop, label, false, false, fixed);
        this.fit = fit;
        this.getOriginImageURL = getOriginImageURL;
        this.getThumbImageURL = getThumbImageURL;
    }
}

export type GetOnePageDataFunction = (currentPage: number,
                                      orderKey: string,
                                      orderDirection: string,
                                      callback: PageCallback) => void
export type SearchOnePageDataFunction = (key: string,
                                         value: string,
                                         page: number,
                                         orderKey: string,
                                         orderDirection: string,
                                         callback: PageCallback) => void
export type DeleteRowsFunction = (selectedRows: number[], deleteRowsSuccess: () => void) => void

export interface TableConfig {
    tableColumns: TableColumn[],
    getOnePageData: GetOnePageDataFunction,
    searchData: SearchOnePageDataFunction,
    deleteRows?: DeleteRowsFunction,
}

