<template>
  <BaseTableManager
      :add-dialog-config="addDialogConfig"
      :table-column-edit-dialog-config="editDialogConfig"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import {TextTableColumn, TreeSelectTextColumn} from "@/components/public/table/tableTypes";
import {FormInputConfigFactory} from "@/utils/formInputConfig";
import {TableCrudFactory} from "@/utils/tableCrudFactory";
import {FormSelectConfigFactory} from "@/utils/formSelectConfig";
import {FormNumberInputConfigFactory} from "@/utils/formNumberInputConfig";
import {computed, onMounted, ref} from "vue";
import {myGet} from "@/api/utils/axios";
import BaseTableManager from "@/components/public/table/BaseTableManager.vue";

const menuCrud = TableCrudFactory.creatStandardCrud("menu");
const menuTree = ref();
const addDialogConfig = computed(() => ({
  title: "新增菜单",
  formConfig: [
    FormInputConfigFactory.createEditableTextInput("name", "菜单名称", "name"),
    FormInputConfigFactory.createEditableTextInput("code", "权限码", "code"),
    FormSelectConfigFactory.createTreeSingleSelect("parent_id", "父级菜单", menuTree.value, 'parent_id'),
    FormNumberInputConfigFactory.createEditableIntegerInput("sort", "排序", "sort")
  ],
  submitAction: menuCrud.addItem
}));

const editDialogConfig = computed(() => ({
  title: "修改菜单",
  formConfig: [
    FormInputConfigFactory.createReadOnlyTextInput("id", "ID"),
    FormInputConfigFactory.createEditableTextInput("name", "菜单名称", "name"),
    FormInputConfigFactory.createEditableTextInput("code", "权限码", "code"),
    FormSelectConfigFactory.createTreeSingleSelect("parent_id", "父级菜单", menuTree.value, 'parent_id'),
    FormNumberInputConfigFactory.createEditableIntegerInput("sort", "排序", "sort")
  ],
  submitAction: menuCrud.updateItem
}));

const tableConfig = computed(() => ({
  deleteRows: menuCrud.deleteRows,
  getOnePageData: menuCrud.getOnePageData,
  searchData: menuCrud.searchOnePageData,
  tableColumns: [
    new TextTableColumn(80, "id", "ID", true, true, "left"),
    new TextTableColumn(200, "name", "菜单名称", true, true),
    new TextTableColumn(200, "code", "权限码", true, true),
    new TextTableColumn(100, "sort", "排序", true, true),
    // new TextTableColumn(100, "parent_id", "父级ID", true, true),
    new TreeSelectTextColumn(100, "parent_id", "父级ID", menuTree.value ?? [], true, true),
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