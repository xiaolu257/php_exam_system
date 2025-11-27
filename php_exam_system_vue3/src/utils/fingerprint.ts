// utils/fingerprint.ts

import FingerprintJS from '@fingerprintjs/fingerprintjs'


let cachedFingerprint: string | null = null;

/**
 * 获取设备指纹，可选择是否强制刷新（绕过缓存）
 * @param forceRefresh 是否强制重新生成指纹
 * @returns 指纹字符串
 */
export async function useFingerprint(forceRefresh = false): Promise<string> {
    if (!forceRefresh && cachedFingerprint) return cachedFingerprint;

    const fromStorage = localStorage.getItem('fingerprint');
    if (!forceRefresh && fromStorage) {
        cachedFingerprint = fromStorage;
        return fromStorage;
    }

    const fp = await FingerprintJS.load();
    const result = await fp.get();
    const visitorId = result.visitorId;

    cachedFingerprint = visitorId;
    localStorage.setItem('fingerprint', visitorId);
    return visitorId;
}
