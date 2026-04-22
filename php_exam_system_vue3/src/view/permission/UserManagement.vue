<template>
  <BaseTableManager
      :table-column-edit-dialog-config="editDialogConfig"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import {ImageTableColumn, SelectTextColumn, TextTableColumn} from "@/components/public/table/tableTypes";
import {FormInputConfigFactory} from "@/utils/formInputConfig";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {computed} from "vue";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";
import {AvatarBaseURL, AvatarThumbBaseURL} from "@/utils/global";
import {FormSelectConfigFactory} from "@/utils/formSelectConfig";

const menuCrud = TableCrudFactory.creatStandardCrud("user");

const userStatusSelector = [
  {
    label: '禁用',
    value: 0
  },
  {
    label: '正常',
    value: 1
  }
];
const editDialogConfig = computed(() => ({
  title: "修改角色",
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput("id", "ID"),
    FormSelectConfigFactory.createCommonSingleSelect("status", "状态", userStatusSelector, "status"),
  ],
  submitAction: menuCrud.updateItem
}));

const tableConfig = computed(() => ({
  getOnePageData: menuCrud.getOnePageData,
  searchData: menuCrud.searchOnePageData,
  tableColumns: [
    new TextTableColumn(80, "id", "ID", true, true, "left"),
    new TextTableColumn(200, "username", "账号", true, true),
    new TextTableColumn(200, "nickname", "昵称", true, true),
    new ImageTableColumn(100, 'avatar_url', '头像', (url: string) => {
          return `${AvatarBaseURL}${url}`;
        },
        (url: string) => {
          return `${AvatarThumbBaseURL}${url}`;
        }
    ),
    new SelectTextColumn(200, "status", "状态", userStatusSelector, true, true),
    new TextTableColumn(180, "created_at", "创建时间", true, false),
    new TextTableColumn(180, "updated_at", "更新时间", true, false)
  ]
}));
</script>

<style scoped>
</style>