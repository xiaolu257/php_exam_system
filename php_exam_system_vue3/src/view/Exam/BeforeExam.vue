<template>
  <div class="main_panel">
    <div class="exam-container">
      <el-card class="exam-card" v-loading="loading" v-if="exam">
        <template #header>
          <div class="card-header">
            <span class="title">{{ exam.title }}</span>
          </div>
        </template>

        <div class="exam-content">
          <el-descriptions :column="1" border>
            <el-descriptions-item label="考试名称">{{ exam.title }}</el-descriptions-item>

            <el-descriptions-item label="考试说明">{{ exam.description || '暂无说明' }}</el-descriptions-item>

            <el-descriptions-item label="单选题">{{ exam.single_count }} 道</el-descriptions-item>

            <el-descriptions-item label="多选题">{{ exam.multiple_count }} 道</el-descriptions-item>

            <el-descriptions-item label="判断题">{{ exam.true_false_count }} 道</el-descriptions-item>

            <el-descriptions-item label="简答题">{{ exam.short_answer_count }} 道</el-descriptions-item>

            <el-descriptions-item label="考试时长">{{ exam.duration }} 分钟</el-descriptions-item>

            <el-descriptions-item label="总分">{{ exam.total_score }} 分</el-descriptions-item>

            <el-descriptions-item label="开始时间">{{ exam.start_time }}</el-descriptions-item>

            <el-descriptions-item label="结束时间">{{ exam.end_time }}</el-descriptions-item>
          </el-descriptions>

          <!-- 状态提示 -->
          <div class="countdown" v-if="examStatus === 'not_started'">
            距离开始还有：
            <span class="time">{{ formatTime(countdown) }}</span>
          </div>

          <div class="countdown ongoing" v-else-if="examStatus === 'ongoing'">
            🟢 考试进行中，可进入考试
          </div>

          <div class="countdown ended" v-else-if="examStatus === 'ended'">
            🔴 考试已结束，无法进入
          </div>

          <!-- 按钮 -->
          <div class="actions">
            <el-button type="primary" size="large" :disabled="examStatus !== 'ongoing'" @click="startExam">
              开始考试
            </el-button>
          </div>
        </div>
      </el-card>
    </div>
  </div>
</template>

<script setup>
import {computed, onBeforeUnmount, onMounted, ref} from 'vue'
import {dayjs, ElMessage} from 'element-plus'
import {useRoute, useRouter} from 'vue-router'
import {myGet} from '@/api/utils/axios.ts'

const route = useRoute()
const router = useRouter()
const examId = route.params.id

// 考试数据
const exam = ref(null)

// 加载状态
const loading = ref(false)

// 倒计时（秒）
const countdown = ref(0)
let timer = null

// ✅ 考试状态（核心）
const examStatus = computed(() => {
  if (!exam.value) return 'loading'

  const now = dayjs()
  const start = dayjs(exam.value.start_time)
  const end = dayjs(exam.value.end_time)

  if (now.isBefore(start)) return 'not_started'
  if (now.isAfter(end)) return 'ended'
  return 'ongoing'
})

// 初始化倒计时
const initCountdown = () => {
  if (!exam.value) return

  const start = dayjs(exam.value.start_time)
  const now = dayjs()

  if (now.isBefore(start)) {
    countdown.value = start.diff(now, 'second')
  } else {
    countdown.value = 0
  }
}

// 倒计时格式化
const formatTime = (seconds) => {
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  const s = seconds % 60
  return `${h}小时 ${m}分 ${s}秒`
}

// 获取考试信息
const fetchExam = async () => {
  try {
    loading.value = true

    exam.value = await myGet(`exam-paper/${examId}`)

    initCountdown()
  } catch (e) {
    ElMessage.error('获取考试信息失败')
  } finally {
    loading.value = false
  }
}

// 开始考试
const startExam = () => {
  if (examStatus.value === 'not_started') {
    ElMessage.warning('考试尚未开始')
    return
  }

  if (examStatus.value === 'ended') {
    ElMessage.error('考试已结束')
    return
  }

  router.push(`/exam/${examId}/start`)
}

// 生命周期
onMounted(() => {
  fetchExam()

  timer = setInterval(() => {
    if (countdown.value > 0) {
      countdown.value--
    }
  }, 1000)
})

onBeforeUnmount(() => {
  clearInterval(timer)
})
</script>

<style lang="scss" scoped>
@use "@/assets/styles/basic_layout";

.main_panel {
  @extend .basic_vertical_layout;
  height: 100vh;
}

.exam-container {
  display: flex;
  justify-content: center;
}

.exam-card {
  width: 600px;
}

.card-header {
  text-align: center;
}

.title {
  font-size: 20px;
  font-weight: bold;
}

.exam-content {
  margin-top: 10px;
}

.countdown {
  margin-top: 20px;
  text-align: center;
  font-size: 16px;
}

.time {
  color: #409eff;
  font-weight: bold;
}

.countdown.ongoing {
  color: #67c23a;
  font-weight: bold;
}

.countdown.ended {
  color: #f56c6c;
  font-weight: bold;
}

.actions {
  margin-top: 30px;
  text-align: center;
}
</style>