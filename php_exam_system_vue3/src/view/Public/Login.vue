<template>
  <template class="main_panel">
    <template class="login_panel">
      <el-text class="brand-logo">LuLu后台管理系统</el-text>
      <TableBaseForm :form-config="formConfig" :submit-action="login" :width="240" submit-action-title="登录"/>
    </template>
  </template>
</template>

<script lang="ts" setup>
import {login} from "@/api/Admin";
import {AbstractFormConfigItem, FormInputConfigFactory,} from "@/utils/FormInputConfig";
import {adminAccountRules} from "@/utils/FormCheckRules";
import TableBaseForm from "@/components/public/Form/BaseForm.vue";
// 导入 Element Plus 的 FormInstance 类型
const formConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditableTextInput('username', '账号', 'Username', adminAccountRules.username),
  FormInputConfigFactory.createEditablePasswordInput('password', '密码', 'Password', true, adminAccountRules.password),
];
</script>

<style lang="scss" scoped>
@use "@/assets/styles/basic_layout";

.main_panel {
  @extend .basic_vertical_layout;
  height: 100vh;
  background-color: rgb(239, 243, 253);
}

.login_panel {
  @extend .basic_vertical_layout;
  height: 260px;
  background-color: white;
  padding: 20px 20px 0 20px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.brand-logo {
  font-size: 28px;
  font-weight: bold;
  color: black;
  font-family: 楷体, serif;
}
</style>
