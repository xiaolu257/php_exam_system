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

    <el-input v-model="searchValueModel" :disabled="isSearch" clearable
              placeholder="请输入要搜索的关键字" style="width: 200px;margin: 0 15px"></el-input>
    <el-button type="primary" @click="onSearch">查询</el-button>
  </el-col>
</template>

<script lang="ts" setup>
import type {TableColumn} from "@/components/public/table/tableTypes";
import {computed} from "vue";
import MyMessage from "@/utils/myMessage";

interface Props {
  isSearch: boolean
  searchKey: string
  searchValue: string
  tableColumns: TableColumn[]
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'update:isSearch', value: boolean): void
  (e: 'update:searchKey', value: string): void
  (e: 'update:searchValue', value: string): void
}>()
const searchKeyModel = computed({
  get: () => props.searchKey,
  set: (val: string) => emit('update:searchKey', val),
})

const searchValueModel = computed({
  get: () => props.searchValue,
  set: (val: string) => emit('update:searchValue', val),
})

const onSearch = () => {
  if (!searchKeyModel.value) {
    MyMessage.error('请选择搜索依据')
  } else if (!searchValueModel.value) {
    MyMessage.error('请输入要搜索的关键字')
  } else {
    emit('update:isSearch', true)
  }
}

const onReset = () => {
  emit('update:searchKey', '')
  emit('update:searchValue', '')
  emit('update:isSearch', false)
}
</script>
<style>
</style>