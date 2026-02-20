<template>
  <template v-if="config instanceof AssociateSelectConfig">
    <template v-if="config.multiple">
      <AssociateMultipleSelect :formData="formData" :item="config"/>
    </template>
    <template v-else>
      <AssociateSingleSelect :formData="formData" :item="config"/>
    </template>
  </template>
</template>

<script lang="ts" setup>
import {
  AssociateSelectConfig,
  type FormSelectConfig
} from "@/utils/FormSelectConfig";
import AssociateSingleSelect from "@/components/public/Form/ChildComponet/SelectComponent/AssociateSingleSelect.vue";
import AssociateMultipleSelect from "@/components/public/Form/ChildComponet/SelectComponent/AssociateMultipleSelect.vue";

// Props 定义
defineProps<{
  config: FormSelectConfig;
  formData: Record<string, any>;
}>();
</script>

<style scoped>
/* 自定义样式（如果需要） */
</style>
