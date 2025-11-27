<script setup>
import Menu from "@/components/Manager/Menu.vue";
import Header from "@/components/Manager/Header.vue";
</script>

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
