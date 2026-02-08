<template>
  <el-tag effect="dark" size="default" style="margin: 0 10px" type="success">{{ totalDataLength }}</el-tag>
  条数据，
  当前页共
  <el-tag effect="dark" size="default" style="margin: 0 10px" type="success">{{ tableDataLength }}</el-tag>
  条数据
  <el-button v-if="exportDataToExcel" style="margin-left: 10px;" type="primary" @click="exportDataToExcel">导出
  </el-button>
</template>

<script lang="ts" setup>

interface Props {
  totalDataLength: number;
  tableDataLength: number;
  exportDataToExcel?: () => void;
}

defineProps<Props>();
</script>
<style>

</style>