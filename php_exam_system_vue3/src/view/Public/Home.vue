<template>
  <el-row>
    <el-col :span="12">
      <div id="main1" style="width: 600px; height: 400px;"></div>
    </el-col>
    <el-col :span="12">
      <div id="main2" style="width: 400px; height: 400px;"></div>
    </el-col>
  </el-row>

</template>

<script lang="ts" setup>
import type {EChartsOption} from "echarts";
import * as echarts from 'echarts';
import {onMounted} from 'vue';

const getLast7Days = (): string[] => {
  const dates: string[] = [];
  const today = new Date();
  for (let i = 6; i >= 0; i--) { // 从6开始，确保日期按升序排列
    const date = new Date(today);
    date.setDate(today.getDate() - i);
    const formattedDate = `${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getDate().toString().padStart(2, '0')}`;
    dates.push(formattedDate);
  }
  return dates;
}
onMounted(() => {
  let chartDom = document.getElementById('main1')!;
  let myChart = echarts.init(chartDom);
  let option: EChartsOption;
  const date = getLast7Days();
  option = {
    title: {
      text: '最近七天用户访问情况', // 设置图表标题
      left: 'center', // 标题水平居中
      top: 'top', // 标题放置在顶部
    },
    tooltip: {
      trigger: 'axis', // 触发方式设为 'axis'，以显示轴线提示
      axisPointer: {
        type: 'cross', // 显示十字线效果，可以选择 'shadow', 'line', 'cross'
        label: {
          backgroundColor: '#6a7985', // 背景色样式
        },
      },
    },
    grid: {
      right: '15%', // 增加右侧边距，确保 x 轴名称不被截断
    },
    xAxis: {
      name: '日期/月日',
      type: 'category',
      data: date,
      boundaryGap: false,
    },
    yAxis: {
      name: '访问量/次',
      type: 'value'
    },
    series: [
      {
        name: '访问量',
        data: [34, 77, 224, 218, 135, 147, 260],
        type: 'line'
      }
    ]
  };
  option && myChart.setOption(option);
  chartDom = document.getElementById('main2')!;
  myChart = echarts.init(chartDom);

  option = {
    title: {
      text: '管理员数量', // 设置图表标题
      left: 'center', // 标题水平居中
      top: 'top', // 标题放置在顶部
    },
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'shadow'
      }
    },
    xAxis: [
      {
        type: 'category',
        data: ['审核管理员', '运维管理员', '超级管理员'],
        axisTick: {
          alignWithLabel: true
        }
      }
    ],
    yAxis: [
      {
        name: '人数/个',
        type: 'value'
      }
    ],
    series: [
      {
        name: '人数',
        type: 'bar',
        barWidth: '60%',
        data: [9, 3, 1]
      }
    ]
  };

  option && myChart.setOption(option);
});
</script>

<style scoped>
/* 可根据需求添加样式 */
</style>
