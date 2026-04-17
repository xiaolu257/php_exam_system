<template>
  <el-date-picker
      v-model="dateTimeRangeModel"
      type="datetimerange"
      value-format="YYYY-MM-DD HH:mm:ss"
      :range-separator="config.rangeSeparator"
      :start-placeholder="config.startPlaceholder"
      :end-placeholder="config.endPlaceholder"
  />
</template>

<script lang="ts" setup>
import {computed} from "vue";
import type {DateTimeRangePickerConfig} from "@/utils/formDatePickerConfig";


// Props 定义
const props = defineProps<{
  config: DateTimeRangePickerConfig;
  dateTimeRange: string[];
}>();
const emit = defineEmits<{
  (e: 'update:dateTimeRange', value: string[]): void
}>()
const dateTimeRangeModel = computed({
  get: () => props.dateTimeRange,
  set: (val: string[]) => emit('update:dateTimeRange', val),
})
</script>
