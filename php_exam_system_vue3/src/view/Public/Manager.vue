<template>
  <el-container style="height: 100vh;">
    <el-aside class="aside">
      <Menu/>
    </el-aside>
    <el-container>
      <el-header class="header">
        <Header/>
      </el-header>
      <el-main class="main">
        <el-scrollbar>
          <router-view/>
        </el-scrollbar>
      </el-main>
    </el-container>
  </el-container>
</template>

<script lang="ts" setup>

import Header from "@/components/manager/Header.vue";
import Menu from "@/components/manager/Menu.vue";
</script>
<style scoped>
.aside {
  width: auto;
}

.header {
  height: 50px;
  background-color: white;
}

.main {
  background-color: rgb(247, 247, 247);
  padding: 0;
}
</style>
