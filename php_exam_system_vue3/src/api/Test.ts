/*
import axios from "axios";
import {ElMessageBox} from "element-plus";
import MyMessage from "@/utils/MyMessage";
import myMessage from "@/utils/MyMessage";
import myAxios from "@/api/utils/axios";

export async function deleteUserRow(tableData, index,deleteSuccess:Function) {
    ElMessageBox.confirm(
        '数据被删除后无法恢复，请谨慎操作，您确认要删除吗？',
        'Warning',
        {
            confirmButtonText: '确认',
            cancelButtonText: '取消',
            type: 'warning',
        }
    )
    .then( async () => {
        const userToDelete = tableData[index];
        // 前端乐观更新
        tableData.splice(index, 1);
        try {
            const response = await myAxios.delete('/api/Customer/deleteById', {
                data: {id: userToDelete.id}
            }); // 后端删除
            const {success = false} = response.data;
            if (success) {
                MyMessage.success('已成功删除所选数据！')
                deleteSuccess();
            } else {
                MyMessage.error('服务器未知故障，删除失败！')
                tableData.splice(index, 0, userToDelete);
            }
        } catch (error) {
            MyMessage.error('删除失败，请稍后重试！')
            tableData.splice(index, 0, userToDelete);
        }
    })
    .catch(() => {
        MyMessage.info('取消删除')
    })
}

export async function deleteUserRows(tableData, selectedRows,deleteRowsSuccess:Function) {
    ElMessageBox.confirm(
        '数据被删除后无法恢复，请谨慎操作，您确认要删除选中的数据吗？',
        'Warning',
        {
            confirmButtonText: '确认',
            cancelButtonText: '取消',
            type: 'warning',
        }
    )
        .then( async () => {
            // 备份当前表格数据
            const originalData = [...tableData.value];

            // 乐观删除：先在前端移除选中的行
            tableData.value = tableData.value.filter(
                item => !selectedRows.value.includes(item)
            );
            //获取要删除的ids
            const ids = selectedRows.value.map(item => item.id);
            try {
                const response = await myAxios.delete('/api/Customer/deleteByIds', {
                    data: {ids}
                }); // 后端删除
                console.log(response.data)
                const {success = false} = response.data;
                if (success) {
                    MyMessage.success('已成功删除所选数据！')
                    deleteRowsSuccess();
                } else {
                    MyMessage.error('服务器未知故障，删除失败！')
                    tableData.value = originalData;
                }
            } catch (error) {
                MyMessage.error('删除失败，请稍后重试！')
                tableData.value = originalData;
            }
        })
        .catch(() => {
            MyMessage.info('取消删除')
        })
}

export async function getOnePageCustomer(currentPage,callback:Function) {
    try {
        const response = await myAxios.get('/api/Customer/getOnePageCustomer', {
            params: { page: currentPage }
        });
        const {success = false} = response.data;
        const {data = []} = response.data;
        const {pageCount = 0} = response.data;
        const {total = 0} = response.data;
        if(!success){
            MyMessage.error('获取信息失败！原因：'+response.data.error)
        }else{
            callback(data,pageCount,total);
        }
    } catch (error) {
        console.error('获取信息失败', error);
        MyMessage.error('服务器未知故障，获取信息失败！原因：'+error)
    }
}

export async function addCustomer(data:Record<string,any>) {
    try {
        const response = await myAxios.post(
            '/api/Customer/addCustomer',
            {data}
        );
        const {success = false} = response.data;
        console.log(response.data)
        if(success){
            MyMessage.success('添加成功！')
        }else{
            MyMessage.error('添加失败！原因：'+response.data.error)
        }
    } catch (error) {
        MyMessage.error('服务器未知故障，添加失败！原因：'+error)
    }
}
export async function updateCustomer(data: Record<string, any>,callback: Function) {
    try {
        const response = await myAxios.post(
            '/api/Customer/updateCustomer',
            {data}
        );
        const {success = false} = response.data;
        if(success){
            MyMessage.success('修改成功！');
            callback()
        }else{
            MyMessage.error('修改失败！原因：'+response.data.error)
        }
    } catch (error) {
        console.error('修改数据失败', error);
        // 显示错误消息
        MyMessage.error('服务器未知故障，添加失败！原因：'+error);
    }
}
export async function searchCustomer(key:string,value:string,page:number,callback:Function) {
    if(!key){
        MyMessage.error('请选择搜索依据')
    }else if(!value){
        MyMessage.error('请输入要搜索的关键字')
    }else{
        try {
            const response = await myAxios.get(
                '/api/Customer/searchCustomer',{
                    params:{key,value,page}
                }
            );
            const {success = false} = response.data;
            const {data = []} = response.data;
            const {pageCount = 0} = response.data;
            const {total = 0} = response.data;
            if(!success){
                MyMessage.error('查询失败！原因：'+response.data.error)
            }else{
                callback(data,pageCount,total);
            }
        } catch (error) {
            console.error('查询数据失败', error);
            // 显示错误消息
            MyMessage.error('服务器未知故障，查询失败！原因：'+error);
        }
    }

}*/
