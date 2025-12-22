<template>
  <el-upload
      :accept="item.options.accept"
      :action="item.options.action"
      :before-upload="beforeUploadCheck"
      :drag="item.options.drag"
      :file-list="fileList"
      :http-request="item.options.httpRequest?? dummyUpload"
      :limit="uploadLimit"
      :method="item.options.method"
      :name="item.options.filename"
      :on-error="handleUploadError"
      :on-success="handleUploadSuccess"
      :show-file-list="item.options.showFileList"
      class="avatar-uploader"
  >
    <!-- 图片预览和上传按钮 -->
    <div v-if="item.options instanceof SingleImageUploadOption">
      <div
          v-if="thumbImageUrl"
          class="avatar-container"
          @click.stop.prevent
      >
        <el-image
            :preview-src-list="[originImageUrl]"
            :src="thumbImageUrl"
            class="avatar"
            fit="cover"
        />
        <el-icon
            class="delete-icon"
            @click="removeImage($event)"
        >
          <Delete/>
        </el-icon>
      </div>
      <el-icon v-else class="upload-icon">
        <span>上传图片</span>
        <Plus/>
      </el-icon>
    </div>

    <template v-else-if="item.options instanceof MultipleImageUploadOption">
      <el-button type="primary">上传图片</el-button>
      <div slot="tip" class="el-upload__tip">
        可上传 {{ item.options.limit }} 张图片
      </div>
    </template>
  </el-upload>
</template>

<script lang="ts" setup>
import {type FormUploadConfig, MultipleImageUploadOption, SingleImageUploadOption,} from "@/utils/FormUploadConfig";
import {computed, ref, watch} from "vue";
import type {UploadFile} from "element-plus";
import {ElMessage} from "element-plus";
import {Delete, Plus} from "@element-plus/icons-vue";

// Props 定义
const props = defineProps<{
  item: FormUploadConfig; // 上传配置
  formData: Record<string, any>; // 表单数据
}>();

// Emits 定义
const emit = defineEmits(["success", "error"]);

// 响应式数据
const thumbImageUrl = ref<string>(""); // 单图片预览地址
const originImageUrl = ref<string>(""); // 单图片预览地址
const fileList = ref<UploadFile[]>([]); // 文件列表

// 监听 fileList，动态更新 formData[item.name]
watch(fileList, (newList) => {
  // 提取 raw 文件并同步到 formData[item.name]
  props.formData[props.item.name] = newList.map((file) => file.raw!).filter(Boolean);
});

// 模拟上传逻辑
const dummyUpload = (options: { file: File; onSuccess: () => void }) => {
  const reader = new FileReader();
  reader.onload = () => {
    thumbImageUrl.value = reader.result as string; // 设置预览地址
    thumbImageUrl.value = reader.result as string;
    options.onSuccess(); // 通知上传成功
  };
  reader.readAsDataURL(options.file); // 读取文件
};

// 上传限制
const uploadLimit = computed(() => {
  if (props.item.options instanceof MultipleImageUploadOption) {
    return props.item.options.limit || 1;
  }
  return 1;
});

// 处理上传成功
const handleUploadSuccess = (response: any, uploadFile: UploadFile) => {
  fileList.value = [...fileList.value, uploadFile]; // 添加到文件列表
  emit("success", response); // 触发上传成功事件
};

// 处理上传失败
const handleUploadError = () => {
  ElMessage.error("上传失败！");
  emit("error"); // 触发上传失败事件
};

// 上传前检查
const beforeUploadCheck = (file: File) => {
  const {accept, maxSize} = props.item.options;

  // 检查文件类型
  if (accept) {
    const acceptedTypes = accept.split(',').map(type => type.trim());
    const isTypeValid =
        acceptedTypes.includes(file.type) || // MIME 类型完全匹配
        (acceptedTypes.includes("image/*") && file.type.startsWith("image/")); // 泛类型匹配

    if (!isTypeValid) {
      ElMessage.error(`仅支持上传 ${accept} 类型的文件！`);
      return false;
    }
  }

  // 检查文件大小
  if (maxSize && file.size > maxSize) {
    ElMessage.error(`文件大小不能超过 ${maxSize / 1024 / 1024} MB！`);
    return false;
  }

  return true;
};

// 删除图片
const removeImage = (event: Event) => {
  event.stopPropagation();
  fileList.value = [];
};
watch(
    () => props.formData[props.item.name],
    (val) => {
      if (Array.isArray(val) && val.length == 0) {
        thumbImageUrl.value = "";
        originImageUrl.value = "";
      } else if (props.item.options instanceof SingleImageUploadOption && typeof val === "string") {
        thumbImageUrl.value = props.item.options.getThumbImageURL?.(val) ?? "";
        originImageUrl.value = props.item.options.getOriginImageURL?.(val) ?? "";
      } else if (props.item.options instanceof SingleImageUploadOption && Array.isArray(val) && val[0] instanceof File) {
        const url = URL.createObjectURL(val[0]);
        thumbImageUrl.value = url;
        originImageUrl.value = url;
      }
    },
    {immediate: true}
);

</script>


<style scoped>
.avatar-uploader .avatar-container {
  position: relative;
  width: 100px;
  height: 100px;
}

.avatar-uploader .avatar {
  width: 100px;
  height: 100px;
  display: block;
  border-radius: 6px;
}

.delete-icon {
  position: absolute;
  top: 5%;
  right: 5%;
  font-size: 12px;
  color: white;
  cursor: pointer;
  background: rgba(255, 0, 0, 0.8);
  border-radius: 50%;
  padding: 5%;
}

.upload-icon {
  font-size: 18px;
  color: #8c939d;
  width: 100px;
  height: 100px;
  text-align: center;
  background-color: rgb(247, 247, 247);
  border: 1px dashed rgb(64, 158, 255);
}
</style>
