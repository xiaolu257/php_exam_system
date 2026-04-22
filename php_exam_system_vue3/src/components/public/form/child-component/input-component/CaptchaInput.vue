<template>
  <el-input
      v-model="textModel"
      :clearable="config.clearable"
      :disabled="config.disabled"
      :placeholder="config.placeholder"
  />
  <el-image style="height: 50px;width: 160px;margin-top: 5px" :src="captchaUrl" @click="loadCaptcha"/>
</template>

<script lang="ts" setup>
import {CaptchaInputConfig} from "@/utils/formInputConfig";
import {computed, onMounted, ref} from "vue";
import {myGet} from "@/api/utils/axios";


// Props 定义
const props = defineProps<{
  config: CaptchaInputConfig;
  text: string | number;
}>();
const emit = defineEmits<{
  (e: 'update:text', value: string | number): void
}>()
const textModel = computed({
  get: () => props.text,
  set: (val: string | number) => emit('update:text', val),
})
const captchaUrl = ref('')

const loadCaptcha = async () => {
  const res = await myGet('user/captcha', {}, true, {responseType: 'blob'})
  captchaUrl.value = URL.createObjectURL(res)
}
onMounted(async () => {
  await loadCaptcha()
})
</script>
