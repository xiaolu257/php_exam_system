<template>
  <BaseTableManager
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportUserToExcel"
      :table-config="tableConfig"
  >
  </BaseTableManager>
</template>

<script lang="ts" setup>

import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {ImageTableColumn, TableColumn, TextTableColumn} from "@/utils/MyTableTypeClass";
import {AbstractFormConfigItem, type EditDialogConfig, FormInputConfigFactory} from "@/utils/FormInputConfig";
import type {TableConfig} from "@/utils/TableConfig";
import {exportUserToExcel} from "@/api/Export";
import {getOnePageUsers, searchUsers, updateUserStatus} from "@/api/SuperManager";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";

const editFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('id', '用户ID'),
  FormInputConfigFactory.createReadOnlyTextInput('nickname', '昵称'),
  FormInputConfigFactory.createReadOnlyTextInput('openid', '账号'),
  FormSelectConfigFactory.createSingleSelect('status', '状态', [
    {label: '正常', value: 1},
    {label: '封禁', value: 0},
  ]),
];

const editDialogConfig: EditDialogConfig = {
  editFormTitle: '编辑用户信息',
  editFormConfig: editFormConfig,
  editSubmitAction: updateUserStatus
}
const getOriginImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/SuperManager/getUserAvatar?avatarUrl=${url}`;
};

const getThumbImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/SuperManager/getUserAvatarThumb?avatarUrl=${url}`;
};
const tableColumns: TableColumn[] = [
  new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
  new TextTableColumn(100, 'nickname', '昵称'),
  new TextTableColumn(120, 'openid', '账号（openid）'),
  new ImageTableColumn(110, 'avatar_url', '头像', getOriginImageURL, getThumbImageURL),
  new TextTableColumn(110, 'status', '账号状态'),
  new TextTableColumn(150, 'create_time', '创建时间', true, false),
  new TextTableColumn(150, 'update_time', '更新时间', true, false)
];
const tableConfig: TableConfig = {
  getOnePageData: getOnePageUsers,
  searchData: searchUsers,
  tableColumns: tableColumns
}
</script>

<style scoped>

</style>