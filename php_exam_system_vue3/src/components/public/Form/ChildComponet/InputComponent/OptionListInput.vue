<template>
  <el-col
      style="margin-bottom: 5px"
      v-for="(_, index) in optionListModel"
      :key="index"
  >
    <el-row :gutter="20" align="middle">
      <el-text style="margin-left: 20px">{{ String.fromCharCode(65 + index) }}</el-text>
      <el-col :span="16">
        <el-input v-model="optionListModel[index]" placeholder="请输入选项内容"/>
      </el-col>
      <el-button type="danger" link @click="removeOption(index)">删除</el-button>
    </el-row>

  </el-col>
  <el-button style="margin-top: 2px" type="primary" plain :disabled="optionListModel.length >= 10"
             @click="addOption">+ 添加选项
  </el-button>
</template>

<script lang="ts" setup>
// Props 定义
import {computed} from "vue";

const props = defineProps<{
  optionList: string[];
}>();
const emit = defineEmits<{
  (e: 'update:optionList', value: string[]): void
}>()
const optionListModel = computed({
  get: () => props.optionList,
  set: (val: string[]) => emit('update:optionList', val)
})

function addOption() {
  //如果用push则不会触发emit，破坏单向数据流
  optionListModel.value = [...optionListModel.value, '']
}

function removeOption(index: number) {
  optionListModel.value = optionListModel.value.filter((_, i) => i !== index)
}
</script>
