import {ElMessageBox} from "element-plus";
import MyMessage from "@/utils/MyMessage";
import {myAxios, myGet, myPost, myPut} from "@/api/utils/axios";
import {h} from "vue";

export async function deleteItemRows(
    tableData: Array<any>,
    ids: number[], // An array of IDs to delete
    deleteSuccess: Function,
    url: string
) {

    const message = ids.length > 1
        ? '数据被删除后无法恢复，请谨慎操作，您确认要删除选中的数据吗？'
        : '数据被删除后无法恢复，请谨慎操作，您确认要删除吗？';

    ElMessageBox.confirm(
        message,
        'Warning',
        {
            confirmButtonText: '确认',
            cancelButtonText: '取消',
            type: 'warning',
        }
    )
        .then(async () => {
            myPost(url, {ids}).then(({msg}) => {
                MyMessage.success(msg);
                deleteSuccess()
            })
        })
        .catch(() => {
            MyMessage.info('取消删除');
        });
}


export async function getOnePageItem(currentPage: number, orderBy: string, orderDirection: string, callback: Function, url: string) {
    myGet(url, {page: currentPage, orderBy: orderBy, orderDirection: orderDirection})
        .then((res) => {
            const {data = [], last_page = 0, total = 0} = res;
            callback(data, last_page, total);
        })
}

export async function getToBeReviewedItemCount(url: string, name: string) {
    try {
        const response = await myAxios.get(url);
        const {success = false} = response.data;
        const {total = 0} = response.data;
        if (!success) {
            MyMessage.error('获取信息失败！原因：' + response.data.error)
        } else {
            if (total != 0) {
                //MyMessage.warning('待审核通知：'+total+'项'+name+'信息待审核。');
                await ElMessageBox(
                    {
                        title: '待审核通知',
                        message: h('div', null, [
                            '当前共 ',
                            h('strong', {style: 'color: red;'}, total.toString()), // Bold red total
                            ' 项 ',
                            h('strong', {style: 'color: red;'}, name), // Bold red name
                            ' 待审核。'
                        ]),
                        confirmButtonText: '确认',
                        center: true,
                        callback: () => {
                        },
                    }
                );
            } else {
                //MyMessage.success('通知：当前无待审核数据。');
                await ElMessageBox({
                    title: '通知',
                    message: h('div', {style: 'font-weight: bold; color: green;'}, '当前无待审核数据'), // Bold and green text
                    confirmButtonText: '确认',
                    center: true,
                    callback: () => {
                    },
                });
            }
        }
    } catch (error) {
        console.error('获取信息失败', error);
        // 显示错误消息
        MyMessage.error('服务器未知故障，获取信息失败！原因：' + error);
    }
}

export function addItem(url: string, data: Record<string, any>, onSuccess: () => void = () => {}) {
    myPost(url, data).then(({msg = 'addItem操作成功'}) => {
        MyMessage.success(msg);
        onSuccess();
    })
}

export function updateItem(url: string, data: Record<string, any>, onSuccess: () => void = () => {}) {
    myPut(url, data).then(({msg = 'updateItem操作成功'}) => {
        MyMessage.success(msg);
        onSuccess();
    })
}

export async function searchItem(searchField: string, searchValue: string, page: number, orderBy: string, orderDirection: string, callback: Function, url: string) {
    if (!searchField) {
        MyMessage.error('请选择搜索依据')
    } else if (!searchValue) {
        MyMessage.error('请输入要搜索的关键字')
    } else {
        myGet(url, {
            searchField,
            searchValue,
            page,
            orderBy,
            orderDirection
        })
            .then((res) => {
                const {data = [], last_page = 0, total = 0} = res;
                callback(data, last_page, total);
            })
    }

}