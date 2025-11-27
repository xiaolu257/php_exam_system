// src/router/permission.ts
import router from './index';
import {allAsyncRoutes} from './asyncRoutes';

export function extractAllowedPathsFromMenu(menu: any[]): string[] {
    const paths: string[] = [];

    function traverse(menuItems: any[]) {
        for (const item of menuItems) {
            if (item.path) {
                paths.push(item.path);
            }
            if (item.children) {
                traverse(item.children);
            }
        }
    }

    traverse(menu);
    return paths;
}

export function setupDynamicRoutes(allowedPaths: string[]) {
    const managerRoute = router.getRoutes().find(r => r.path === '/Manager');

    if (managerRoute) {
        allAsyncRoutes.forEach(route => {
            const fullPath = '/Manager/' + route.path;
            if (allowedPaths.includes(fullPath)) {
                router.addRoute('Manager', {
                    ...route,
                    path: route.path,
                });
            }
        });
    }
}
