<template>
  <el-row align="middle" style="height: 50px;">
    <el-col :span="2">
      <el-button @click="changeFolded">
        <el-icon :size="20">
          <Expand v-show="isFolded"/>
          <Fold v-show="!isFolded"/>
        </el-icon>
      </el-button>
    </el-col>
    <el-col :span="18">

    </el-col>
    <el-col :span="4" class="col">
      <span>{{ userNickName }}</span>

      <!-- 头像部分，鼠标悬浮显示个人信息 -->
      <el-popover placement="bottom" trigger="hover" width="200">
        <template #reference>
          <el-avatar :src="`${AvatarThumbBaseURL}${userAvatarUrl}`"/>
        </template>
        <div>
          <p><strong>账号：</strong>{{ username }}</p>
          <p><strong>昵称：</strong>{{ userNickName }}</p>
          <p><strong>角色：</strong>{{ '未知' }}</p>
          <el-row justify="center">
            <el-button type="primary" @click="goToProfile">修改</el-button>
          </el-row>
        </div>
      </el-popover>

      <el-button round type="danger" @click="quitLogin">退出登录</el-button>
    </el-col>
  </el-row>
</template>

<script lang="ts" setup>
import {Expand, Fold} from "@element-plus/icons-vue";
import router from "@/router/index";
import {useGlobalStore} from "@/stores/counter";
import {storeToRefs} from "pinia";
import {AvatarThumbBaseURL} from "@/utils/global";
import {quitLogin} from "@/api/Admin";

const {isFolded, username, userType, userNickName, userAvatarUrl} = storeToRefs(useGlobalStore());

function changeFolded() {
  isFolded.value = !isFolded.value;
}


function goToProfile() {
  router.push('/Manager/Profile');
}
</script>

<style scoped>
.col {
  display: flex;
  justify-content: center;
  align-items: center;
}

span {
  font-family: 楷体, serif;
  font-size: 17px;
  margin-right: 10px;
}
</style>
