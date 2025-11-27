<template>
  <div class="carousel-container">
    <el-carousel height="95%" indicator-position="outside" motion-blur>
      <el-carousel-item v-for="(url, index) in bannerUrls" :key="index">
        <el-image
            :src="getBannerImageUrl(url)"
            class="carousel-image"
            fit="contain"
        />
      </el-carousel-item>
    </el-carousel>
  </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import {myGet} from "@/api/utils/axios";

// 存储轮播图 URL
const bannerUrls = ref([]);

// 处理图片 URL
const getBannerImageUrl = (url) => `http://xiaolu.cn/api/OperationManager/getBanner?BannerUrl=${url}`;

// 获取数据
const fetchBanners = async () => {
  myGet('OperationManager/getAllBannersUrl')
      .then((res) => {
        bannerUrls.value = res;
      })
};

// 组件加载时获取数据
onMounted(fetchBanners);
</script>

<style scoped>
.carousel-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 90vh; /* 让整个页面居中 */
  background: #f5f5f5;
  width: 100%;
}

.el-carousel {
  width: 1200px; /* 适合 PC 端 */
  height: 600px; /* 16:9 或 2:1 的比例 */
}

/* 让图片适配 */
.carousel-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
}


</style>
