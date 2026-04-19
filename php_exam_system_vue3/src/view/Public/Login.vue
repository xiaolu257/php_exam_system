<template>
  <div class="main_panel">
    <div class="login_panel">
      <el-text class="brand-logo">骁鹿PHP笔试系统</el-text>
      <BaseForm :form-config="formConfig" :submit-action="login" :width="240" submit-action-title="登录"/>
      <div class="register-link">
        还没有账号？
        <el-link type="primary" underline @click="goRegister">注册</el-link>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>

import {AbstractFormConfigItem, FormInputConfigFactory,} from "@/utils/formInputConfig";
import {adminAccountRules} from "@/utils/formCheckRules";
import router from "@/router";
import {login} from "@/api/admin";
import BaseForm from "@/components/public/form/BaseForm.vue";
// 导入 Element Plus 的 FormInstance 类型
const formConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditableTextInput('username', '账号', 'Username', adminAccountRules.username),
  FormInputConfigFactory.createEditablePasswordInput('password', '密码', 'Password', true, adminAccountRules.password),
];
const goRegister = () => {
  router.push({name: 'Register'});
};
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

.register-link {
  font-size: 14px;
  color: #666;

  /* 下划线链接样式 */
  a {
    text-decoration: underline;
    cursor: pointer;
  }
}
</style>
