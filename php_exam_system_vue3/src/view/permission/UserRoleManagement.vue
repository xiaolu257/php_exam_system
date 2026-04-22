<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import {TextTableColumn} from "@/components/public/table/tableTypes";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {computed, onMounted, ref} from "vue";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";
import {myGet} from "@/api/utils/axios";
import {FormSelectConfigFactory} from "@/utils/formSelectConfig";
import type {AddDialogConfig} from "@/components/public/form/formTypes";
import {FormNumberInputConfigFactory} from "@/utils/formNumberInputConfig";

const menuCrud = TableCrudFactory.creatStandardCrud("user-role");
const rolesSelector = ref();
const addDialogConfig = computed((): AddDialogConfig => ({
  title: '角色分配',
  formConfig: [
    FormNumberInputConfigFactory.createEditableIntegerInput("user_id", "用户ID", "user_id"),
    FormSelectConfigFactory.createCommonSingleSelect("role_id", "角色", rolesSelector.value, "role_id"),
  ],
  submitAction: menuCrud.addItem
}));

const tableConfig = computed(() => ({
  deleteRows: menuCrud.deleteRows,
  getOnePageData: menuCrud.getOnePageData,
  searchData: menuCrud.searchOnePageData,
  tableColumns: [
    new TextTableColumn(80, "id", "ID", true, true, "left"),
    new TextTableColumn(150, "user_id", "用户ID", true, true),
    new TextTableColumn(150, "username", "账号", true, true),
    new TextTableColumn(150, "nickname", "昵称", true, true),
    new TextTableColumn(100, "role_id", "角色ID", true, true),
    new TextTableColumn(150, "role_code", "角色标识", true, true),
    new TextTableColumn(150, "role_description", "角色描述", true, true),
    new TextTableColumn(180, "created_at", "创建时间", true, false)
  ]
}));
onMounted(() => {
  myGet('role/selector').then((res) => {
    rolesSelector.value = res;
  });
})
</script>

<style scoped>
</style>