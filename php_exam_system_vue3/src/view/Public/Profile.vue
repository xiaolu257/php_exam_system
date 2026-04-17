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
          <BaseEditFormDialog :edit-dialog-config="editDialogConfig"/>
        </template>
      </TableBaseForm>
    </el-row>

  </el-card>
</template>

<script lang="ts" setup>
import TableBaseForm from "@/components/public/form/BaseForm.vue";
import {AbstractFormConfigItem, FormInputConfigFactory} from "@/utils/formInputConfig";
import {adminAccountRules} from "@/utils/formCheckRules";
import {FormUploadConfigFactory} from "@/utils/formUploadConfig";
import {storeToRefs} from "pinia";
import {useGlobalStore} from "@/stores/global";
import {quitLogin} from "@/api/Admin";
import BaseEditFormDialog from "@/components/public/Dialog/BaseEditFormDialog.vue";
import {myPost} from "@/api/utils/axios";
import MyMessage from "@/utils/myMessage";
import {buildFormData} from "@/api/utils/FormData";
import {AvatarBaseURL, AvatarThumbBaseURL} from "@/utils/global";
import type {EditDialogConfig} from "@/components/public/form/formTypes";

const getOriginImageURL = (url: string): string => {
  return `${AvatarBaseURL}${url}`;
};

const getThumbImageURL = (url: string): string => {
  return `${AvatarThumbBaseURL}${url}`;
};
const {username: globalUsername, userType, userNickName, userAvatarUrl} = storeToRefs(useGlobalStore());
const initData: Record<string, any> = {
  username: globalUsername.value,
  type: '未知',
  nickname: userNickName.value,
  avatar: userAvatarUrl.value
};
const formConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createReadOnlyTextInput('username', '账号'),
  FormInputConfigFactory.createReadOnlyTextInput('type', '用户类型'),
  FormInputConfigFactory.createEditableTextInput('nickname', '昵称', 'name', adminAccountRules.name),
  FormUploadConfigFactory.createSingleImageSelector('avatar', '头像', getThumbImageURL, getOriginImageURL),
];
const updateProfile = async (data: Record<string, any>, callback: () => void) => {
  const formData = buildFormData(data, 'avatar');
  myPost('user/update-profile', formData, true, {
    headers: {'Content-Type': 'multipart/form-data'}
  }).then(({msg, userData}) => {

    const {nickname, avatar_url} = userData;
    const {userNickName, userAvatarUrl} = storeToRefs(useGlobalStore());
    if (nickname) {
      userNickName.value = nickname;
    }
    if (avatar_url) {
      userAvatarUrl.value = avatar_url;
    }
    MyMessage.success(msg);
    callback();
  })
};
const editFormConfig: AbstractFormConfigItem[] = [
  FormInputConfigFactory.createEditablePasswordInput('oldPassword', '原密码', 'oldPassword', true, adminAccountRules.password),
  FormInputConfigFactory.createEditablePasswordInput('newPassword', '新密码', 'newPassword', true, adminAccountRules.password),
  FormInputConfigFactory.createEditablePasswordInput('confirmPassword', '确认密码', 'confirmPassword', true, adminAccountRules.password),
];


const updatePassword = async (data: Record<string, any>, callback: () => void) => {
  myPost('user/change-password', data)
      .then(({msg}) => {
        MyMessage.success(msg);
        callback();
        quitLogin();
      })
};
const editDialogConfig: EditDialogConfig = {
  formConfig: editFormConfig,
  initData: [],
  submitAction: updatePassword,
  title: "修改密码",
  controlButtonName: "修改密码",
  buttonSize: "default"
}
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
