<template>
  <el-card class="profile-card">
    <template #header>
      <div class="card-header">
        <span>个人资料</span>
      </div>
    </template>
    <el-row justify="center">
      <TableBaseForm :form-config="formConfig" :init-data="initData"
                     :submitAction="updateProfile">
        <template v-slot:otherButtons>
          <BaseEditFormDialog :form-config="editFormConfig"
                              :init-data="{username:globalUsername}"
                              :submitAction="updatePassword" :width="400" button-size="default"
                              control-name="修改密码" title="修改密码"></BaseEditFormDialog>
        </template>
      </TableBaseForm>
    </el-row>

  </el-card>
</template>

<script lang="ts" setup>
import TableBaseForm from "@/components/public/Form/BaseForm.vue";
import {AbstractFormConfigItem, FormInputConfigFactory} from "@/utils/FormInputConfig";
import {adminAccountRules} from "@/utils/FormCheckRules";
import {FormUploadConfigFactory} from "@/utils/FormUploadConfig";
import {storeToRefs} from "pinia";
import {useGlobalStore} from "@/stores/counter";
import {getAdminType, loadAdminData, quitLogin} from "@/api/Admin";
import BaseEditFormDialog from "@/components/public/Table/BaseEditFormDialog.vue";
import {myPost} from "@/api/utils/axios";
import MyMessage from "@/utils/MyMessage";
import {buildFormData} from "@/api/utils/FormData";

const getOriginImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/SuperManager/getAdminAvatar?avatarUrl=${url}`;
};

const getThumbImageURL = (url: string): string => {
  return `http://xiaolu.cn/api/SuperManager/getAdminAvatarThumb?avatarUrl=${url}`;
};
const {ipAddress, username: globalUsername, userType, userNickName, userAvatarUrl} = storeToRefs(useGlobalStore());
const initData: Record<string, any> = {
  username: globalUsername.value,
  type: getAdminType(userType.value),
  login_ip: ipAddress.value,
  name: userNickName.value,
  avatar_url: userAvatarUrl.value
};
const formConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('username', '账号'),
  FormInputConfigFactory.createReadOnlyTextInput('type', '管理员类型'),
  FormInputConfigFactory.createReadOnlyTextInput('login_ip', '登录IP'),
  FormInputConfigFactory.createEditableTextInput('name', '昵称', 'name', adminAccountRules.name),
  FormUploadConfigFactory.createSingleImageSelector('avatar_url', '头像', getThumbImageURL, getOriginImageURL),
];
const updateProfile = async (data: Record<string, any>, callback: () => void) => {
  const formData = buildFormData(data, 'avatar_url');
  myPost('ManagerPublicAPI/adminUpdateProfile', formData, true, {
    headers: {'Content-Type': 'multipart/form-data'}
  }).then(({msg, userData}) => {
    MyMessage.success(msg);
    loadAdminData(userData)
    callback();
  })
};
const editFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('username', '账号'),
  FormInputConfigFactory.createEditablePasswordInput('old_password', '原密码', 'password', true, adminAccountRules.password),
  FormInputConfigFactory.createEditablePasswordInput('new_password', '新密码', 'password', true, adminAccountRules.password),
  FormInputConfigFactory.createEditablePasswordInput('confirm_password', '确认密码', 'password', true, adminAccountRules.password),
];


const updatePassword = async (data: Record<string, any>, callback: () => void) => {
  myPost('ManagerPublicAPI/adminUpdatePassword', data)
      .then(({msg}) => {
        MyMessage.success(msg);
        callback();
        quitLogin();
      })
};

</script>

<style scoped>
.profile-card {
  max-width: 500px;
  margin: 200px auto;
  padding: 20px;
}

.card-header {
  font-size: 18px;
  font-weight: bold;
}
</style>
