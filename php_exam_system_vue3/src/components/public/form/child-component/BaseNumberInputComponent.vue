<template>
  <template v-if="item instanceof IntegerInputConfig">
    <IntegerInput :config="item" v-model:num="formData[item.name]"/>
  </template>
</template>

<script lang="ts" setup>
import {type FormNumberInputConfig, IntegerInputConfig} from "@/utils/formNumberInputConfig";
import IntegerInput from "@/components/public/form/child-component/number-input-component/IntegerInput.vue";


// Props 定义
defineProps<{
  item: FormNumberInputConfig;
  formData: Record<string, any>;
}>();
</script>
