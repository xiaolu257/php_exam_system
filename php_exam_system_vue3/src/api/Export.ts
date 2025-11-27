import * as XLSX from 'xlsx';
import MyMessage from "@/utils/MyMessage";
import {myGet} from "@/api/utils/axios";
import MyMessageBox from "@/api/MyMessageBox";
// 计算最合适的列宽
const setColumnWidths = (data: any[]) => {
    if (!data.length) return [];

    return Object.keys(data[0]).map((key) => {
        // 获取该列所有单元格的长度，包含标题
        const columnData = [key, ...data.map(row => row[key]?.toString() || '')];

        // 计算每个单元格内容的宽度，区分中英文字符
        const getWidth = (str: string) => {
            return str.split('').reduce((acc, char) => {
                // 中文字符宽度为2，英文及其他字符宽度为1
                return acc + (char.charCodeAt(0) > 255 ? 2 : 1);
            }, 0);
        };

        // 计算该列的最大宽度
        const maxLength = Math.max(...columnData.map(item => getWidth(item)));
        return {wch: maxLength + 2}; // 根据最大宽度设置列宽，并留出空余
    });
};

// 通用导出方法
const exportToExcel = (data: any[], filename: string) => {
    if (data.length === 0) {
        MyMessage.warning('数据为空，无法导出！');
        return;
    }

    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'TableData');
    XLSX.writeFile(workbook, filename);
};

// 通用的 API 导出函数
const exportDataToExcel = async (url: string, filename: string) => {
    await MyMessageBox.confirm('确认要导出表格的全部数据吗？').then(
        () => {
            myGet(url).then((res) => {
                exportToExcel(res, filename);
            })
        }
    );
};

// 具体的导出方法
export const exportAdminToExcel = () => exportDataToExcel('Export/exportAllAdministrators', 'Admins.xlsx');
export const exportUserToExcel = () => exportDataToExcel('Export/exportAllUsers', 'Users.xlsx');
export const exportMyLogsToExcel = () => exportDataToExcel('MyLogs/getAllSortedMyLogs', 'MyLogs.xlsx');
export const exportJobCategoriesToExcel = () => exportDataToExcel('Export/exportAllJobCategories', 'JobCategories.xlsx');
export const exportJobCategoryLogsToExcel = () => exportDataToExcel('JobCategoryLogs/getAllSortedJobCategoryLogs', 'JobCategoryLogs.xlsx');
export const exportJobsToExcel = () => exportDataToExcel('Export/exportAllJobs', 'Jobs.xlsx');
export const exportResumesToExcel = () => exportDataToExcel('Export/exportAllResumes', 'Resumes.xlsx');
export const exportJobLogsToExcel = () => exportDataToExcel('JobLogs/getAllSortedJobLogs', 'JobLogs.xlsx');
export const exportJobAreasToExcel = () => exportDataToExcel('Export/exportAllJobAreas', 'JobAreas.xlsx');
export const exportJobAreaLogsToExcel = () => exportDataToExcel('JobAreaLogs/getAllSortedJobAreaLogs', 'JobAreaLogs.xlsx');
export const exportJobApplicationsToExcel = () => exportDataToExcel('JobApplications/getAllSortedJobApplications', 'JobApplications.xlsx');
export const exportBannersToExcel = () => exportDataToExcel('Export/exportAllBanners', 'Banners.xlsx');
export const exportEnterpriseAuthenticationsToExcel = () => exportDataToExcel('Export/exportAllEnterpriseAuthentications', 'EnterpriseAuthentications.xlsx');


