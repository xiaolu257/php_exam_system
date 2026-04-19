<template>
  <div v-if="item instanceof DateTimeRangePickerConfig">
    <DateTimeRangePicker :config="item" v-model:date-time-range="formData[item.name]"/>
  </div>
</template>

<script lang="ts" setup>
import {DateTimeRangePickerConfig, FormDatePickerConfig} from "@/utils/formDatePickerConfig";
import DateTimeRangePicker
  from "@/components/public/form/child-component/date-picker-component/DateTimeRangePicker.vue";


// Props 定义
defineProps<{
  item: FormDatePickerConfig;
  formData: Record<string, any>;
}>();
</script>
