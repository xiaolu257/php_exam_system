<template>
  <template v-if="config instanceof CommonSelectConfig">
    <template v-if="config.multiple">
      <CommonSingleSelect v-model:currentSelect="formData[config.name]"
                          :options="config.options"
                          :item="config"/>
    </template>
    <template v-else>
      <CommonSingleSelect v-model:currentSelect="formData[config.name]"
                          :options="config.options"
                          :item="config"/>
    </template>
  </template>
  <template v-else-if="config instanceof AssociateSelectConfig">
    <template v-if="config.multiple">
      <AssociateMultipleSelect v-model:currentSelect="formData[config.name]"
                               :associateOptions="config.associateFunction(formData[config.dependKey]).value"
                               :item="config"/>
    </template>
    <template v-else>
      <AssociateSingleSelect v-model:currentSelect="formData[config.name]"
                             :associateOptions="config.associateFunction(formData[config.dependKey]).value"
                             :item="config"/>
    </template>
  </template>
  <template v-else-if="config instanceof TreeSelectConfig">
    <TreeSingleSelect v-model:currentSelect="formData[config.name]" :item="config"/>
  </template>

</template>

<script lang="ts" setup>
import {
  AssociateSelectConfig,
  CommonSelectConfig,
  type FormSelectConfig,
  TreeSelectConfig
} from "@/utils/formSelectConfig";
import TreeSingleSelect from "@/components/public/form/child-component/select-component/TreeSingleSelect.vue";
import AssociateSingleSelect from "@/components/public/form/child-component/select-component/AssociateSingleSelect.vue";
import AssociateMultipleSelect
  from "@/components/public/form/child-component/select-component/AssociateMultipleSelect.vue";
import CommonSingleSelect from "@/components/public/form/child-component/select-component/CommonSingleSelect.vue";

// Props 定义
defineProps<{
  config: FormSelectConfig;
  formData: Record<string, any>;
}>();
</script>
