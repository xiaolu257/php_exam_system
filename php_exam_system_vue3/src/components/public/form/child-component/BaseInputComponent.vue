<template>
  <template v-if="item instanceof OptionsListInputConfig">
    <OptionListInput v-model:option-list="formData[item.name]"/>
  </template>
  <template v-else-if="item instanceof PasswordInputConfig">
    <PasswordInput :config="item" v-model:password="formData[item.name]"/>
  </template>
  <template v-else-if="item instanceof TextInputConfig">
    <TextInput :config="item" v-model:text="formData[item.name]"/>
  </template>
  <template v-else-if="item instanceof TextAreaInputConfig">
    <TextAreaInput :config="item" v-model:text="formData[item.name]"/>
  </template>
</template>

<script lang="ts" setup>
import {
  type FormInputConfig,
  OptionsListInputConfig,
  PasswordInputConfig,
  TextAreaInputConfig,
  TextInputConfig
} from "@/utils/formInputConfig";
import OptionListInput from "@/components/public/form/child-component/input-component/OptionListInput.vue";
import PasswordInput from "@/components/public/form/child-component/input-component/PasswordInput.vue";
import TextInput from "@/components/public/form/child-component/input-component/TextInput.vue";
import TextAreaInput from "@/components/public/form/child-component/input-component/TextAreaInput.vue";


// Props 定义
defineProps<{
  item: FormInputConfig;
  formData: Record<string, any>;
}>();
</script>
