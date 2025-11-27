<template style="background-color: red">
  <el-menu
      :collapse="isFolded"
      :default-active="activeMenu"
      class="menu"
      router
      unique-opened
  >
    <template v-for="(item, index) in menu_routes" :key="item.path">
      <el-menu-item v-if="!item.children" :index="item.path">
        <el-icon>
          <HomeFilled/>
        </el-icon>
        <span>{{ item.name }}</span>
      </el-menu-item>
      <el-sub-menu v-else :index="String(index)">
        <template #title>
          <el-icon>
            <Location/>
          </el-icon>
          <span>{{ item.name }}</span>
        </template>
        <el-menu-item v-for="it in item.children" :key="it.path" :index="it.path">
          <el-icon>
            <IconMenu/>
          </el-icon>
          <span>{{ it.name }}</span>
        </el-menu-item>
      </el-sub-menu>
    </template>
  </el-menu>
</template>

<script lang="ts" setup>
import {HomeFilled, Location, Menu as IconMenu} from '@element-plus/icons-vue'
import {storeToRefs} from "pinia";
import {useGlobalStore} from "@/stores/counter";
import {useRoute} from "vue-router";
import {computed} from "vue";
import {getMenuByUserType} from "@/utils/AuditManagerMenu";

const {userType, isFolded} = storeToRefs(useGlobalStore());
const route = useRoute();

// 让 activeMenu 响应式地绑定当前路由路径
const activeMenu = computed(() => {
  return route.path
});
const menu_routes = computed(() => getMenuByUserType(userType.value));
</script>

<style scoped>
span {
  font-family: 楷体, serif;
  font-size: 16px;
}

.menu:not(.el-menu--collapse) {
  width: 200px;
}
</style>
