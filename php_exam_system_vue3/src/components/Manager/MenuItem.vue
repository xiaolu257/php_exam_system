<template>
  <template v-for="item in menus" :key="item.id">

    <!-- 有子菜单 -->
    <el-sub-menu v-if="item.children?.length">
      <template #title>
        <span>{{ item.name }}</span>
      </template>

      <MenuItem :menus="item.children"/>
    </el-sub-menu>

    <!-- 叶子节点 -->
    <el-menu-item v-else @click="go(item)">
      <span>{{ item.name }}</span>
    </el-menu-item>

  </template>
</template>

<script setup lang="ts">
import {useRouter} from "vue-router";

interface MenuItem {
  id: number;
  name: string;
  code: string;
  children: MenuItem[];
}

defineProps<{
  menus: MenuItem[]
}>();

const router = useRouter();

const go = (item: any) => {
  router.push({
    name: item.code
  });
};
</script>