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
} from "@/utils/FormInputConfig";
import OptionListInput from "@/components/public/Form/ChildComponet/InputComponent/OptionListInput.vue";
import PasswordInput from "@/components/public/Form/ChildComponet/InputComponent/PasswordInput.vue";
import TextInput from "@/components/public/Form/ChildComponet/InputComponent/TextInput.vue";
import TextAreaInput from "@/components/public/Form/ChildComponet/InputComponent/TextAreaInput.vue";


// Props 定义
defineProps<{
  item: FormInputConfig;
  formData: Record<string, any>;
}>();
</script>
