<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportAdminToExcel"
      :table-config="tableConfig"
  >
  </BaseTableManager>
</template>

<script lang="ts" setup>

import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {ImageTableColumn, PasswordTableColumn, TableColumn, TextTableColumn} from "@/utils/MyTableTypeClass";
import {
  AbstractFormConfigItem,
  type AddDialogConfig,
  type EditDialogConfig,
  FormInputConfigFactory
} from "@/utils/FormInputConfig";
import {adminAccountRules} from "@/utils/FormCheckRules";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {FormUploadConfigFactory} from "@/utils/FormUploadConfig";
import {exportAdminToExcel} from "@/api/Export";
import type {TableConfig} from "@/utils/TableConfig";
import {
  addAdmin,
  deleteAdminsRows,
  getOnePageAdmins,
  searchAdmins,
  updateAdmin,
  updateAdminPassword
} from "@/api/SuperManager";

const getOriginImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/SuperManager/getAdminAvatar?avatarUrl=${url}`;
};

const getThumbImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/SuperManager/getAdminAvatarThumb?avatarUrl=${url}`;
};

const addFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditableTextInput('name', '昵称', 'name', adminAccountRules.name),
  FormInputConfigFactory.createEditableTextInput('username', '账号', 'username', adminAccountRules.username),
  FormInputConfigFactory.createEditablePasswordInput('password', '密码', 'password', true, adminAccountRules.password),
  FormSelectConfigFactory.createSingleSelect('type', '管理员类型', [
    {label: '审核管理员', value: 0},
    {label: '运营管理员', value: 1},
    {label: '超级管理员', value: 2},
  ], '管理员类型', adminAccountRules.type),
  FormUploadConfigFactory.createSingleImageSelector('avatar', '头像', getThumbImageURL, getOriginImageURL, adminAccountRules.avatar)
];
const editFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
  FormInputConfigFactory.createEditableTextInput('name', '昵称', 'name', adminAccountRules.name),
  FormInputConfigFactory.createReadOnlyTextInput('username', '账号'),
  FormSelectConfigFactory.createSingleSelect('type', '管理员类型', [
    {label: '审核管理员', value: 0},
    {label: '运营管理员', value: 1}
  ], '管理员类型', adminAccountRules.type),
  FormSelectConfigFactory.createSingleSelect('status', '状态', [
    {label: '正常', value: 1},
    {label: '封禁', value: 0},
  ]),
  FormUploadConfigFactory.createSingleImageSelector('avatar_url', '头像', getThumbImageURL, getOriginImageURL)
];
const updatePasswordFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('id', 'ID'),
  FormInputConfigFactory.createReadOnlyTextInput('username', '账号'),
  FormInputConfigFactory.createEditableTextInput('password', '密码', 'password', adminAccountRules.password)
];
const addDialogConfig: AddDialogConfig = {
  addFormTitle: '新增管理员',
  addFormConfig: addFormConfig,
  addSubmitAction: addAdmin
}
const editDialogConfig: EditDialogConfig = {
  editFormTitle: '编辑管理员信息',
  editFormConfig: editFormConfig,
  editSubmitAction: updateAdmin
}
const updatePasswordDialogConfig: EditDialogConfig = {
  editFormTitle: '重置密码',
  editFormConfig: updatePasswordFormConfig,
  editSubmitAction: updateAdminPassword,
  editButtonName: '重置密码',
  editButtonType: 'warning'
}

const tableColumns: TableColumn[] = [
  new TextTableColumn(75, 'id', 'ID', true, true, 'left'),
  new TextTableColumn(100, 'name', '昵称', true, true),
  new TextTableColumn(100, 'username', '账号', true, true),
  new PasswordTableColumn(120, 'password', '登录密码', updatePasswordDialogConfig),
  new TextTableColumn(110, 'type', '管理员类型', true, true),
  new ImageTableColumn(110, 'avatar_url', '头像', getOriginImageURL, getThumbImageURL),
  new TextTableColumn(110, 'login_ip', '登录IP', true, true),
  new TextTableColumn(100, 'status', '状态'),
  new TextTableColumn(150, 'create_time', '创建时间', true, false),
  new TextTableColumn(150, 'update_time', '更新时间', true, false)
];
const tableConfig: TableConfig = {
  deleteRows: deleteAdminsRows,
  getOnePageData: getOnePageAdmins,
  searchData: searchAdmins
  , tableColumns: tableColumns

}
</script>

<style scoped>

</style>