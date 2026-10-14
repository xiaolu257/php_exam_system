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
          <el-avatar :src="avatarUrl"/>
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
import {useGlobalStore} from "@/stores/global";
import {storeToRefs} from "pinia";
import {AvatarThumbBaseURL} from "@/utils/global";
import {quitLogin} from "@/api/admin";
import {myGet} from "@/api/utils/axios";
import {onBeforeUnmount, onMounted, ref} from "vue";

const {isFolded, username, userNickName, userAvatarUrl} = storeToRefs(useGlobalStore());

function changeFolded() {
  isFolded.value = !isFolded.value;
}

function goToProfile() {
  router.push('/Manager/Profile');
}

const avatarUrl = ref('')

// 组件是否已经卸载
let disposed = false

// 请求缩略图：组件挂载时只请求缩略图
onMounted(async () => {
  try {
    const blob = await myGet(`${AvatarThumbBaseURL}${userAvatarUrl.value}`, {}, false, {
          responseType: 'blob',
          silent: true,
        },
    ) as Blob

    if (!(blob instanceof Blob) || blob.size === 0) {
      throw new Error('缩略图数据为空')
    }

    const url = URL.createObjectURL(blob)

    // 请求完成前组件可能已经卸载
    if (disposed) {
      URL.revokeObjectURL(url)
      return
    }

    avatarUrl.value = url
  } catch (error) {
    if (!disposed) {
      console.error('缩略图加载失败：', error)
    }
  }
})

// 组件卸载时释放已创建的 Blob URL
onBeforeUnmount(() => {
  disposed = true

  if (avatarUrl.value) {
    URL.revokeObjectURL(avatarUrl.value)
  }
});
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
