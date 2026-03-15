<template>
  <div v-if="item instanceof DateTimeRangePickerConfig">
    <DateTimeRangePicker :config="item" v-model:date-time-range="formData[item.name]"/>
  </div>
</template>

<script lang="ts" setup>
import DateTimeRangePicker from "@/components/public/Form/ChildComponet/DatePickerComponent/DateTimeRangePicker.vue";
import {DateTimeRangePickerConfig, FormDatePickerConfig} from "@/utils/FormDatePickerConfig";


// Props 定义
defineProps<{
  item: FormDatePickerConfig;
  formData: Record<string, any>;
}>();
</script>
