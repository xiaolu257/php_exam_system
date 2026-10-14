<template>
  <el-table-column v-for="item in tableColumns" :fixed="item.fixed" :label="item.label" :min-width="item.min_width"
                   :prop="item.prop" :sortable="item.sortable" align="center">
    <template #default="{row}">
      <el-text v-if="item instanceof TextTableColumn" :type="item.textType">{{ row[item.prop] }}</el-text>
      <el-text v-else-if="item instanceof SelectTextColumn">{{
          item.options.find((i) => i.value === row[item.prop])?.label
                                                            }}
      </el-text>
      <el-text v-else-if="item instanceof TreeSelectTextColumn">
        {{ findTreeLabel(item.treeData, row[item.prop]) }}
      </el-text>
      <ImageColumn v-else-if="item instanceof ImageTableColumn" :item="item" :image-url="row[item.prop]"></ImageColumn>
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
import ImageColumn from "@/components/public/table/child-component/TableColumns/ImageColumn.vue";

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