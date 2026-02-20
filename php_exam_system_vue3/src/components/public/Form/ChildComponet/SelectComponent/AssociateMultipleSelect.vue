<template>
  <el-select
      v-model="formData[item.name]"
      :disabled="item.disabled"
      :clearable="item.clearable"
      :placeholder="item.placeholder"
      multiple
  >
    <el-option
        v-for="option in associateOptions"
        :key="option.value"
        :label="option.label"
        :value="option.value"
    />
  </el-select>
</template>

<script lang="ts" setup>
import {AssociateSelectConfig,} from "@/utils/FormSelectConfig";
import {computed, watch} from "vue";

// Props 定义
const props = defineProps<{
  item: AssociateSelectConfig;
  formData: Record<string, any>;
}>();


const associateOptions = computed(() => {
  return props.item.associateFunction(props.formData).value
})
watch(
    associateOptions,
    (newOptions) => {
      //规则 1：如果选择的选项不在关联选项数组中 → 清空当前选择的选项
      const key = props.item.name
      const validValues = newOptions.map(o => o.value)
      const current = props.formData[key] ?? []
        props.formData[key] = current.filter((v: string) =>
            validValues.includes(v)
        )
    },
    {immediate: true}
)

</script>

<style scoped>
/* 自定义样式（如果需要） */
</style>
