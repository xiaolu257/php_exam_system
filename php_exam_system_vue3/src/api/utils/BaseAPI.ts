import {ElMessageBox} from "element-plus";
import MyMessage from "@/utils/MyMessage";
import {myDel, myGet, myPost, myPut} from "@/api/utils/axios";

export type PageCallback = (
    data: any[],
    lastPage: number,
    total: number
) => void

function getOnePageItem(currentPage: number,
                        orderBy: string,
                        orderDirection: string,
                        callback: PageCallback,
                        url: string) {
    myGet(url, {page: currentPage, orderBy: orderBy, orderDirection: orderDirection})
        .then((res) => {
            const {data = [], last_page = 0, total = 0} = res;
            callback(data, last_page, total);
        })
}

function searchItem(searchField: string,
                    searchValue: string,
                    page: number,
                    orderBy: string,
                    orderDirection: string,
                    callback: PageCallback,
                    url: string) {
    myGet(url, {searchField, searchValue, page, orderBy, orderDirection})
        .then((res) => {
            const {data = [], last_page = 0, total = 0} = res;
            callback(data, last_page, total);
        })
}

function addItem(url: string, data: Record<string, any>, onSuccess: () => void = () => {}) {
    myPost(url, data).then(({msg = 'addItem操作成功'}) => {
        MyMessage.success(msg);
        onSuccess();
    })
}

function updateItem(url: string, data: Record<string, any>, onSuccess: () => void = () => {}) {
    myPut(url, data).then(({msg = 'updateItem操作成功'}) => {
        MyMessage.success(msg);
        onSuccess();
    })
}

function deleteItemRows(
    ids: number[],
    deleteSuccess: () => void,
    url: string
) {
    const message = ids.length > 1
        ? '数据被删除后可能无法恢复，请谨慎操作，您确认要删除选中的数据吗？'
        : '数据被删除后可能无法恢复，请谨慎操作，您确认要删除吗？';

    ElMessageBox.confirm(message, 'Warning', {confirmButtonText: '确认', cancelButtonText: '取消', type: 'warning',})
        .then(() => {
            myDel(url, {ids}).then(({msg}) => {
                MyMessage.success(msg);
                deleteSuccess();
            });
        })
        .catch(() => {
            MyMessage.info('取消删除');
        });
}


export {getOnePageItem, searchItem, addItem, updateItem, deleteItemRows};