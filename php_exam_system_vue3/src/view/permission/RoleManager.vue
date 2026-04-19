<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :table-column-edit-dialog-config="editDialogConfig"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import {TextTableColumn} from "@/components/public/table/tableTypes";
import {FormInputConfigFactory} from "@/utils/formInputConfig";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {computed, onMounted, ref} from "vue";
import {myGet} from "@/api/utils/axios";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";

const menuCrud = TableCrudFactory.creatStandardCrud("role");
const menuTree = ref();
const addDialogConfig = computed(() => ({
  title: "新增角色",
  formConfig: [
    FormInputConfigFactory.createEditableTextInput("code", "角色标识", "code"),
    FormInputConfigFactory.createEditableTextInput("description", "描述", "description"),
  ],
  submitAction: menuCrud.addItem
}));

const editDialogConfig = computed(() => ({
  title: "修改角色",
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput("id", "ID"),
    FormInputConfigFactory.createEditableTextInput("code", "角色标识", "name"),
    FormInputConfigFactory.createEditableTextInput("description", "描述", "description"),
  ],
  submitAction: menuCrud.updateItem
}));

const tableConfig = computed(() => ({
  deleteRows: menuCrud.deleteRows,
  getOnePageData: menuCrud.getOnePageData,
  searchData: menuCrud.searchOnePageData,
  tableColumns: [
    new TextTableColumn(80, "id", "ID", true, true, "left"),
    new TextTableColumn(200, "code", "角色标识", true, true),
    new TextTableColumn(300, "description", "描述", true, true),
    new TextTableColumn(180, "created_at", "创建时间", true, false),
    new TextTableColumn(180, "updated_at", "更新时间", true, false)
  ]
}));
onMounted(() => {
  myGet('menu/menu-tree').then((res) => {
    menuTree.value = res;
    //testLog(res)
  });
})
</script>

<style scoped>
</style>