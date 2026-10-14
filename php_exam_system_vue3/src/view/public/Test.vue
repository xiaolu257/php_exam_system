<template>
  <el-image
      :src="thumbSrc"
      :preview-src-list="originSrc ? [originSrc] : []"
      :fit="fit"
      lazy
      preview-teleported
      style="width: 50px; height: 50px"
  >
    <template #error>
      <el-icon>
        <Picture/>
      </el-icon>
    </template>
  </el-image>
</template>

<script setup lang="ts">
import {onBeforeUnmount, ref, watch} from 'vue'
import {Picture} from '@element-plus/icons-vue'
import {myGet} from "@/api/utils/axios";

const props = withDefaults(defineProps<{
  thumbUrl: string
  originUrl: string
  fit?: 'fill' | 'contain' | 'cover' | 'none' | 'scale-down'
}>(), {
  fit: 'cover',
})

const thumbSrc = ref('')
const originSrc = ref('')

const objectUrls: string[] = []

function revokeObjectUrls() {
  objectUrls.forEach(url => URL.revokeObjectURL(url))
  objectUrls.length = 0
}

async function fetchImage(url: string): Promise<string> {
  const response = await myGet('http://192.168.217.10:9501/user/get-user-avatar-thumb?avatarUrl=2026-03-14/a123456_e8e497a3.jpg', {}, true, {
    responseType: 'blob',
  })

  // 如果 request 是标准 Axios 实例，response.data 就是 Blob
  const blob = response as Blob
  const objectUrl = URL.createObjectURL(blob)

  objectUrls.push(objectUrl)
  return objectUrl
}

watch(
    () => [props.thumbUrl, props.originUrl] as const,
    async ([thumbUrl, originUrl], _, onCleanup) => {
      let cancelled = false

      onCleanup(() => {
        cancelled = true
      })

      revokeObjectUrls()
      thumbSrc.value = ''
      originSrc.value = ''

      try {
        const thumb = await fetchImage(thumbUrl)

        if (cancelled) {
          URL.revokeObjectURL(thumb)
          return
        }

        thumbSrc.value = thumb
      } catch (error) {
        if (!cancelled) {
          console.error('缩略图加载失败', error)
        }
      }

      try {
        const origin = await fetchImage(originUrl)

        if (cancelled) {
          URL.revokeObjectURL(origin)
          return
        }

        originSrc.value = origin
      } catch (error) {
        if (!cancelled) {
          console.error('原图加载失败', error)
        }
      }
    },
    {immediate: true},
)

onBeforeUnmount(() => {
  revokeObjectUrls()
})
</script>