<template>
  <BaseTableManager
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportResumesToExcel"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {TextTableColumn} from "@/utils/MyTableTypeClass";
import {type EditDialogConfig, FormInputConfigFactory} from "@/utils/FormInputConfig";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import type {TableConfig} from "@/utils/TableConfig";
import {updateItem} from "@/api/utils/BaseAPI";
import {computed} from "vue";

import {getOnePageResumes, searchResumes} from "@/api/AuditManager";
import {exportResumesToExcel} from "@/api/Export";

const editDialogConfig = computed<EditDialogConfig>(() => ({
  editButtonName: "审核",
  editFormTitle: "简历审核",
  editDialogWidth: 600,
  editFormConfig: [
    FormInputConfigFactory.createReadOnlyTextInput("id", "简历ID"),
    FormInputConfigFactory.createReadOnlyTextInput("user_nickname", "用户昵称"),
    FormInputConfigFactory.createReadOnlyTextInput("full_name", "姓名"),
    FormInputConfigFactory.createReadOnlyTextInput("phone", "联系电话"),
    FormInputConfigFactory.createReadOnlyTextInput("email", "邮箱"),
    FormInputConfigFactory.createReadOnlyTextInput("gender", "性别"),
    FormInputConfigFactory.createReadOnlyTextInput("birthday", "出生日期"),
    FormInputConfigFactory.createReadOnlyTextInput("education", "学历"),
    FormInputConfigFactory.createReadOnlyTextInput("expected_salary", "期望薪资"),
    FormInputConfigFactory.createReadOnlyTextInput("availability", "工作类型"),
    FormInputConfigFactory.createReadOnlyTextInput("location_name", "所在地区"),
    FormInputConfigFactory.createReadOnlyTextAreaWithAutosize("experience", "工作经验", true),
    FormInputConfigFactory.createReadOnlyTextAreaWithAutosize("skills", "技能", true),
    FormInputConfigFactory.createReadOnlyTextAreaWithAutosize("introduction", "自我介绍", true),
    FormSelectConfigFactory.createSingleSelect("status", "审核状态", [
      {label: "待审核", value: "待审核"},
      {label: "审核通过", value: "审核通过"},
      {label: "审核未通过", value: "审核未通过"}
    ], "选择审核状态"),
    FormInputConfigFactory.createReadOnlyTextInput("create_time", "创建时间"),
  ],
  editSubmitAction: async (data: Record<string, any>, callback: () => void) => {
    await updateItem(data, callback, "AuditManager/auditResume");
  }
}));

const tableConfig: TableConfig = {
  getOnePageData: getOnePageResumes,
  searchData: searchResumes,
  tableColumns: [
    new TextTableColumn(100, "id", "简历ID", true, true, "left"),
    new TextTableColumn(120, "user_nickname", "用户昵称", false, false, "left"), // 不在搜索/排序字段中
    new TextTableColumn(120, "full_name", "姓名", true, true, "left"),
    new TextTableColumn(80, "gender", "性别", true, true),
    new TextTableColumn(150, "phone", "联系电话", true, true),
    new TextTableColumn(200, "email", "邮箱", true, true),
    new TextTableColumn(120, "birthday", "出生日期", true, false),
    new TextTableColumn(150, "education", "学历", false, true),
    new TextTableColumn(120, "availability", "工作类型", false, true),
    new TextTableColumn(120, "expected_salary", "期望薪资", true, true),
    new TextTableColumn(120, "location_name", "所在地区", true, false),
    new TextTableColumn(120, "status", "审核状态", true, true, "right"),
    new TextTableColumn(180, "create_time", "创建时间", true, false, 'right'),

  ]
};
</script>
