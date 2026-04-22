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
import router from "@/router";
import BaseForm from "@/components/public/form/BaseForm.vue";
import {baseURL, myPost} from "@/api/utils/axios";
import MyMessage from "@/utils/myMessage";
// 导入 Element Plus 的 FormInstance 类型
const formConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditableTextInput('username', '账号', 'Username'),
  FormInputConfigFactory.createEditablePasswordInput('password', '密码', 'Password', true),
  FormInputConfigFactory.createEditableCaptchaInput('captcha', '验证码', baseURL + '/user/captcha'),
];

async function login(data: Record<string, any>) {
  myPost('user/login', data).then(async ({access_token, refresh_token}) => {
    localStorage.setItem('access_token', access_token);
    localStorage.setItem('refresh_token', refresh_token);
    MyMessage.success('登录成功！');
    await router.replace({name: 'Home'});
  })
}

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
  width: 300px;
  height: 360px;
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
