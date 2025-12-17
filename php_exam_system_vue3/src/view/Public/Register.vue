<template>
  <div class="main_panel">
    <div class="register-container">
      <!-- 大标题 -->
      <h1 class="main-title">骁鹿 PHP 笔试系统</h1>

      <el-card class="profile-card">
        <template #header>
          <div class="card-header">
            <span>注册账号（普通用户）</span>
          </div>
        </template>

        <el-row justify="center">
          <BaseForm :form-config="formConfig" :submitAction="updateProfile"/>
        </el-row>
      </el-card>
    </div>
  </div>

</template>

<script lang="ts" setup>
import BaseForm from "@/components/public/Form/BaseForm.vue";
import {AbstractFormConfigItem, FormInputConfigFactory} from "@/utils/FormInputConfig";
import {adminAccountRules} from "@/utils/FormCheckRules";
import {FormUploadConfigFactory} from "@/utils/FormUploadConfig";
import {myPost} from "@/api/utils/axios";
import MyMessage from "@/utils/MyMessage";
import {buildFormData} from "@/api/utils/FormData";
import router from "@/router";

const formConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditableTextInput("nickname", "昵称", "nickname", adminAccountRules.nickname),
  FormInputConfigFactory.createEditableTextInput("username", "账号", "username", adminAccountRules.username),
  FormInputConfigFactory.createEditableTextInput("password", "密码", "password", adminAccountRules.password),
  FormUploadConfigFactory.createSingleImageSelector("avatar", "头像"),
];

const updateProfile = async (data: Record<string, any>) => {
  console.log(data)
  const formData = buildFormData(data, "avatar");
  myPost("user/register", formData, true, {
    headers: {'Content-Type': 'multipart/form-data'}
  }).then(({msg}) => {
    MyMessage.success(msg);
    router.push({name: "Login"});
  });
};
</script>

<style scoped>
.main_panel {
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100vh;
}

.register-container {
  max-width: 600px;
  margin-top: 120px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.main-title {
  font-size: 36px;
  font-weight: bold;
  margin-bottom: 40px;
  color: #000;
  font-family: "楷体", serif;
}

.profile-card {
  width: 100%;
  padding: 20px;
}

.card-header {
  font-size: 20px;
  font-weight: bold;
}
</style>
