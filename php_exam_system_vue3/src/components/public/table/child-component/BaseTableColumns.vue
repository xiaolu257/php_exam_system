<template>
  <el-table-column v-for="item in tableColumns" :fixed="item.fixed" :label="item.label" :min-width="item.min_width"
                   :prop="item.prop" :sortable="item.sortable" align="center">
    <template #default="scope">
      <el-text v-if="item instanceof TextTableColumn" :type="item.textType">{{ scope.row[item.prop] }}</el-text>
      <el-image
          v-else-if="item instanceof ImageTableColumn"
          :fit="item.fit"
          :preview-src-list="[item.getOriginImageURL(scope.row[item.prop])]"
          :src="item.getThumbImageURL(scope.row[item.prop])"
          lazy
          preview-teleported
          style="width: 50px; height: 50px"
      />
    </template>
  </el-table-column>
</template>
<script lang="ts" setup>
// 定义 Props 的接口
import {ImageTableColumn, TableColumn, TextTableColumn} from "@/components/public/table/tableTypes";

interface Props {
  tableColumns: TableColumn[];
}

defineProps<Props>();
</script>
<style>
</style>