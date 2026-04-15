export interface MenuItem {
    name: string;
    code: string;
    children?: MenuItem[];
}

export interface SidebarMenu {
    name: string;
    code: string;
    path?: string;
    children: SidebarMenu[];
}

export function buildMenu(menus: MenuItem[]): SidebarMenu[] {

    const build = (list: MenuItem[]): SidebarMenu[] => {
        return list.map(item => ({
            name: item.name,
            code: item.code,
            children: item.children?.length
                ? build(item.children)
                : []
        }));
    };

    return build(menus);
}