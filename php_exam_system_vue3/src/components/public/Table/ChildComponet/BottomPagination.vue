<template>
  <el-pagination
      v-model:current-page="currentPageModel"
      :page-count="pageCountModel"
      background
      layout="prev, pager, next"
  />
</template>

<script lang="ts" setup>
import {computed} from "vue";

interface Props {
  currentPage: number
  pageCount: number
}

const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'update:currentPage', value: number): void
  (e: 'update:pageCount', value: number): void
  (e: 'search'): void
  (e: 'reset'): void
}>()
const currentPageModel = computed({
  get: () => props.currentPage,
  set: (val: number) => emit('update:currentPage', val),
})

const pageCountModel = computed({
  get: () => props.pageCount,
  set: (val: number) => emit('update:pageCount', val),
})
</script>
<style>
</style>