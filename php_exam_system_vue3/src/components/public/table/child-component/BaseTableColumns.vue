<template>
  <el-table-column v-for="item in tableColumns" :fixed="item.fixed" :label="item.label" :min-width="item.min_width"
                   :prop="item.prop" :sortable="item.sortable" align="center">
    <template #default="scope">
      <el-text v-if="item instanceof TextTableColumn" :type="item.textType">{{ scope.row[item.prop] }}</el-text>
      <el-text v-else-if="item instanceof SelectTextColumn">{{
          item.options.find((i) => i.value === scope.row[item.prop])?.label
        }}
      </el-text>
      <el-text v-else-if="item instanceof TreeSelectTextColumn">
        {{ findTreeLabel(item.treeData, scope.row[item.prop]) }}
      </el-text>
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
import {
  ImageTableColumn,
  SelectTextColumn,
  TableColumn,
  TextTableColumn,
  TreeSelectTextColumn
} from "@/components/public/table/tableTypes";
import type {TreeSelectOption} from "@/utils/formSelectConfig";

interface Props {
  tableColumns: TableColumn[];
}

defineProps<Props>();

function findTreeLabel(options: TreeSelectOption[], value: any): string | undefined {
  for (const item of options) {
    if (item.value === value) return item.label;

    if (item.children?.length) {
      const res = findTreeLabel(item.children, value);
      if (res) return res;
    }
  }
  return undefined;
}
</script>
<style>
</style>