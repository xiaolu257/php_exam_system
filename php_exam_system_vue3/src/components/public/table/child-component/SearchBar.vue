<template>
  <el-col :offset="1" :span="1">
    <el-tag v-show="isSearch" closable effect="dark" round size="large" type="success" @close="onReset">
      正在显示搜索结果
    </el-tag>
  </el-col>
  <el-col :offset="3" :span="9">
    <el-select v-model="searchKeyModel" :disabled="isSearch" clearable placeholder="请选择搜索依据"
               style="width: 150px">
      <template v-for="item in tableColumns">
        <el-option
            v-if="item.searchable"
            :key="item.prop"
            :label="item.label"
            :value="item.prop"
        />
      </template>
    </el-select>
    <el-select v-if="currentSearchColumn instanceof SelectTextColumn"
               v-model="searchValueModel"
               :disabled="isSearch"
               style="width: 200px;margin: 0 15px">
      <el-option
          v-for="opt in currentSearchColumn.options"
          :key="opt.value"
          :label="opt.label"
          :value="opt.value"
      />
    </el-select>
    <el-tree-select
        v-else-if="currentSearchColumn instanceof TreeSelectTextColumn"
        v-model="searchValueModel"
        :data="currentSearchColumn.treeData"
        show-checkbox
        check-strictly
        :render-after-expand="false"
        :disabled="isSearch"
        style="width: 200px;margin: 0 15px"
    />
    <el-input v-else v-model="searchValueModel" clearable
              :disabled="isSearch"
              placeholder="请输入要搜索的关键字" style="width: 200px;margin: 0 15px"></el-input>
    <el-button type="primary" @click="onSearch">查询</el-button>
  </el-col>
</template>

<script lang="ts" setup>
import {SelectTextColumn, type TableColumn, TreeSelectTextColumn} from "@/components/public/table/tableTypes";
import {computed, watch} from "vue";
import MyMessage from "@/utils/myMessage";
import {ElTreeSelect} from "element-plus";

interface Props {
  isSearch: boolean
  searchKey: string
  searchValue: string | number
  tableColumns: TableColumn[]
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'update:isSearch', value: boolean): void
  (e: 'update:searchKey', value: string): void
  (e: 'update:searchValue', value: string | number): void
}>()
const searchKeyModel = computed({
  get: () => props.searchKey,
  set: (val: string) => emit('update:searchKey', val),
})

const searchValueModel = computed({
  get: () => props.searchValue,
  set: (val: string | number) => emit('update:searchValue', val),
})
const currentSearchColumn = computed(() => {
  return props.tableColumns.find(t => t.prop === searchKeyModel.value)
})
const onSearch = () => {
  if (!searchKeyModel.value) {
    MyMessage.error('请选择搜索依据')
  } else if (!searchValueModel.value && searchValueModel.value !== 0) {
    MyMessage.error('请输入要搜索的关键字')
  } else {
    emit('update:isSearch', true)
  }
}

const onReset = () => {
  emit('update:isSearch', false)
}
watch(searchKeyModel, () => {
  emit('update:searchValue', '')
})
</script>
<style>
</style>