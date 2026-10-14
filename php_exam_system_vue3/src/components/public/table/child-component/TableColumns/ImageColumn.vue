<template>
  <el-image
      :fit="item.fit"
      :preview-src-list="[originUrl || thumbUrl]"
      :src="thumbUrl"
      lazy
      preview-teleported
      style="width: 50px; height: 50px"
      @show="handlePreviewShow"
  />
</template>

<script lang="ts" setup>
import {ImageTableColumn} from '@/components/public/table/tableTypes'
import {myGet} from '@/api/utils/axios'
import {onBeforeUnmount, onMounted, ref} from 'vue'

interface Props {
  item: ImageTableColumn
  imageUrl: string
}

const props = defineProps<Props>()

const thumbUrl = ref('')
const originUrl = ref('')

// 组件是否已经卸载
let disposed = false

// 请求缩略图：组件挂载时只请求缩略图
onMounted(async () => {
  try {
    const blob = await myGet(props.item.getThumbImageURL(props.imageUrl), {}, false, {
          responseType: 'blob',
          silent: true,
        },
    ) as Blob

    if (!(blob instanceof Blob) || blob.size === 0) {
      throw new Error('缩略图数据为空')
    }

    const url = URL.createObjectURL(blob)

    // 请求完成前组件可能已经卸载
    if (disposed) {
      URL.revokeObjectURL(url)
      return
    }

    thumbUrl.value = url
  } catch (error) {
    if (!disposed) {
      console.error('缩略图加载失败：', error)
    }
  }
})

// 点击图片打开预览时，才请求原图
async function handlePreviewShow() {
  // 原图已经加载过，不重复请求
  if (originUrl.value || disposed) {
    return
  }

  try {
    const blob = await myGet(props.item.getOriginImageURL(props.imageUrl), {}, false,
        {
          responseType: 'blob',
          silent: true,
        },
    ) as Blob

    if (!(blob instanceof Blob) || blob.size === 0) {
      throw new Error('原图数据为空')
    }

    const url = URL.createObjectURL(blob)

    if (disposed) {
      URL.revokeObjectURL(url)
      return
    }

    originUrl.value = url
  } catch (error) {
    if (!disposed) {
      console.error('原图加载失败：', error)
    }
  }
}

// 组件卸载时释放已创建的 Blob URL
onBeforeUnmount(() => {
  disposed = true

  if (thumbUrl.value) {
    URL.revokeObjectURL(thumbUrl.value)
  }

  if (originUrl.value) {
    URL.revokeObjectURL(originUrl.value)
  }
})
</script>

<style>
</style>