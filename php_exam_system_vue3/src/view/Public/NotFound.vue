<script setup>
import router from "@/router/index";
</script>

<template>
  <div id="wrapper">
    <img alt="图片加载失败！" class="fade" src="../../assets/Image/transparent-404page.png">
    <div>
      <h1 class="fade">温馨提示：您访问的地址不存在！</h1>
      <p class="fade">你正在寻找的页面无法找到。</p>
      <p class="fade">
        <el-button @click="router.back">返回上一个页面</el-button>
      </p>
    </div>
  </div>
</template>

<style scoped>
a, fieldset, img {
  border: 0;
}

a {
  color: #221919;
  text-decoration: none;
  outline: none;
  opacity: 1;
  display: block;
}

a.link {
  text-shadow: 0 1px 2px white;
  font-weight: 600;
  color: #3366cc;
  opacity: 1;
}

a:hover {
  color: #3366cc;
  text-decoration: underline;
}

body {
  font-size: 24px;
  color: #B7AEB4;
}

body a.link, body h1, body p {
  -webkit-transition: opacity 0.5s ease-in-out;
  -moz-transition: opacity 0.5s ease-in-out;
  transition: opacity 0.5s ease-in-out;
}

#wrapper {
  text-align: center;
  margin: 100px auto;
  width: 594px;
}

h1 {
  text-shadow: 0 1px 2px white;
  font-size: 24px;
  opacity: 0;
}

img {
  -webkit-transition: opacity 1s ease-in-out;
  -moz-transition: opacity 1s ease-in-out;
  transition: opacity 1s ease-in-out;
  height: 202px;
  width: 199px;
  opacity: 0;
}

p {
  text-shadow: 0 1px 2px white;
  font-weight: normal;
  font-weight: 200;
  opacity: 0;
}

.fade {
  opacity: 1;
}

@media only screen and (min-device-width: 320px) and
(max-device-width: 480px) {
  #wrapper {
    margin: 40px auto;
    text-align: center;
    width: 280px;
  }
}
</style>
