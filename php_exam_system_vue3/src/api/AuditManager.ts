import {getOnePageItem, searchItem} from "@/api/utils/BaseAPI";


/**
 * 获取企业认证信息分页数据
 */
export async function getOnePageEnterpriseAuth(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'AuditManager/getOnePageEnterpriseAuthentications');
}

/**
 * 搜索企业认证信息
 */
export async function searchEnterpriseAuth(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'AuditManager/getOnePageSearchEnterpriseAuthentications');
}

/**
 * 获取工作分页数据
 */
export async function getOnePageJobs(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'AuditManager/getOnePageJobs');
}

/**
 * 搜索工作
 */
export async function searchJobs(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'AuditManager/getOnePageSearchJobs');
}

/**
 * 获取简历分页数据
 */
export async function getOnePageResumes(currentPage: number, orderKey: string, orderDirection: string, callback: Function) {
    await getOnePageItem(currentPage, orderKey, orderDirection, callback, 'AuditManager/getOnePageResumes');
}

/**
 * 搜索简历
 */
export async function searchResumes(key: string, value: string, page: number, orderKey: string, orderDirection: string, callback: Function) {
    await searchItem(key, value, page, orderKey, orderDirection, callback, 'AuditManager/getOnePageSearchResumes');
}