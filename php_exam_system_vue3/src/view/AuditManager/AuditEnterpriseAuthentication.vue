<template>
  <BaseTableManager
      :edit-dialog-config="editDialogConfig"
      :export-data-to-excel="exportEnterpriseAuthenticationsToExcel"
      :table-config="tableConfig"
  />
</template>

<script lang="ts" setup>
import BaseTableManager from "@/components/public/Table/BaseTableManager.vue";
import {ImageTableColumn, TextTableColumn} from "@/utils/MyTableTypeClass";
import {type EditDialogConfig, FormInputConfigFactory} from "@/utils/FormInputConfig";
import type {TableConfig} from "@/utils/TableConfig";

import {updateItem} from "@/api/utils/BaseAPI";
import {computed} from "vue";
import {FormSelectConfigFactory} from "@/utils/FormSelectConfig";
import {exportEnterpriseAuthenticationsToExcel} from "@/api/Export";
import {getOnePageEnterpriseAuth, searchEnterpriseAuth} from "@/api/AuditManager";

// 编辑企业认证配置
const editDialogConfig = computed<EditDialogConfig>(() => ({
  editButtonName: "审核",
  editFormTitle: "企业认证审核",
  editDialogWidth: 600,
  editFormConfig: [
    FormInputConfigFactory.createReadOnlyTextInput("id", "认证ID"),
    FormInputConfigFactory.createReadOnlyTextInput("user_nickname", "用户昵称"),
    FormInputConfigFactory.createReadOnlyTextInput("enterprise_name", "企业名称"),
    FormInputConfigFactory.createReadOnlyTextInput("business_license_number", "营业执照号码/统一社会信用代码"),
    FormInputConfigFactory.createReadOnlyTextInput("legal_representative", "法定代表人"),
    FormInputConfigFactory.createReadOnlyTextInput("legal_person_id_card", "法定代表人身份证号码"),
    FormInputConfigFactory.createReadOnlyTextInput("enterprise_type", "企业类型"),
    FormInputConfigFactory.createReadOnlyTextInput("enterprise_address", "企业注册地址"),
    FormInputConfigFactory.createReadOnlyTextInput("contact_number", "企业联系电话"),
    FormInputConfigFactory.createReadOnlyTextInput("contact_email", "企业电子邮箱"),
    FormInputConfigFactory.createReadOnlyTextInput("bank_account_info", "银行账户信息"),
    FormInputConfigFactory.createReadOnlyTextInput("business_license_image", "营业执照扫描件/图片路径"),
    FormSelectConfigFactory.createSingleSelect("status", "认证状态", [
      {label: "待审核", value: "待审核"},
      {label: "审核通过", value: "审核通过"},
      {label: "审核未通过", value: "审核未通过"}
    ], "选择认证状态"),
    FormInputConfigFactory.createReadOnlyTextInput("create_time", "申请时间"),
  ],
  editSubmitAction: async (data: Record<string, any>, callback: () => void) => {
    await updateItem(data, callback, "AuditManager/auditEnterpriseAuthentication");
  }
}));
const getOriginImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/AuditManager/getBusinessLicenseImage?imageUrl=${url}`;
};

const getThumbImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/AuditManager/getBusinessLicenseImage?imageUrl=${url}`;
};
// 表格配置
const tableConfig: TableConfig = {
  getOnePageData: getOnePageEnterpriseAuth,
  searchData: searchEnterpriseAuth,
  tableColumns: [
    new TextTableColumn(100, "id", "认证ID", true, true, "left"),
    new TextTableColumn(120, "user_nickname", "用户昵称", true, true, "left"),
    new TextTableColumn(200, "enterprise_name", "企业名称", true, true, "left"),
    new TextTableColumn(200, "business_license_number", "营业执照号码/统一社会信用代码"),
    new TextTableColumn(150, "legal_representative", "法定代表人"),
    new TextTableColumn(150, "legal_person_id_card", "法定代表人身份证号码"),
    new TextTableColumn(150, "enterprise_type", "企业类型", true, true),
    new TextTableColumn(200, "enterprise_address", "企业注册地址"),
    new TextTableColumn(200, "contact_number", "企业联系电话"),
    new TextTableColumn(200, "contact_email", "企业电子邮箱"),
    new TextTableColumn(200, "bank_account_info", "银行账户信息"),
    new ImageTableColumn(110, 'business_license_image_url', '营业执照', getOriginImageURL, getThumbImageURL, 'left'),
    new TextTableColumn(120, "status", "认证状态", true, true, "right"),
    new TextTableColumn(180, "create_time", "申请时间", true, false),
  ]

};
</script>
