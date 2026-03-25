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
            <el-descriptions-item label="最大考试次数">{{ exam.max_attempts }}</el-descriptions-item>
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
            <el-button
                type="primary"
                size="large"
                :disabled="examStatus !== 'ongoing'"
                @click="startExam"
            >
              开始考试
            </el-button>
          </div>
        </div>
      </el-card>
    </div>
  </div>
</template>

<script lang="ts" setup>
import {computed, onBeforeUnmount, onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import dayjs from 'dayjs'
import {ElMessage} from 'element-plus'
import {myGet, myPost} from '@/api/utils/axios'
import myMessage from "@/utils/MyMessage";

interface Exam {
  id: number
  title: string
  description: string | null
  single_count: number
  multiple_count: number
  true_false_count: number
  short_answer_count: number
  duration: number
  total_score: number
  start_time: string
  end_time: string
  max_attempts: number
}

type ExamStatus = 'loading' | 'not_started' | 'ongoing' | 'ended'

const route = useRoute()
const router = useRouter()
const examPaperId = route.params.id as string

const exam = ref<Exam>({
  id: 0,
  title: '',
  description: '',
  single_count: 0,
  multiple_count: 0,
  true_false_count: 0,
  short_answer_count: 0,
  duration: 0,
  total_score: 0,
  start_time: '',
  end_time: '',
  max_attempts: 0,
})

const loading = ref(false)
const countdown = ref(0)
let timer: ReturnType<typeof setInterval> | null = null

const examStatus = computed<ExamStatus>(() => {
  if (!exam.value) return 'loading'
  const now = dayjs()
  const start = dayjs(exam.value.start_time)
  const end = dayjs(exam.value.end_time)
  if (now.isBefore(start)) return 'not_started'
  if (now.isAfter(end)) return 'ended'
  return 'ongoing'
})

const initCountdown = (): void => {
  const start = dayjs(exam.value.start_time)
  const now = dayjs()
  countdown.value = now.isBefore(start) ? start.diff(now, 'second') : 0
}

const formatTime = (seconds: number): string => {
  const h = Math.floor(seconds / 3600)
  const m = Math.floor((seconds % 3600) / 60)
  const s = seconds % 60
  return `${h}小时 ${m}分 ${s}秒`
}

const fetchExam = async (): Promise<void> => {
  try {
    loading.value = true
    const res = await myGet(`exam-paper/${examPaperId}`)
    exam.value = res as Exam
    initCountdown()
  } catch {
    ElMessage.error('获取考试信息失败')
  } finally {
    loading.value = false
  }
}

const startExam = (): void => {
  if (examStatus.value === 'not_started') myMessage.warning('考试尚未开始')
  if (examStatus.value === 'ended') myMessage.error('考试已结束')
  myPost(`exam-paper/${examPaperId}/start`).then(
      ({exam_id, msg}) => {
        myMessage.success(msg)
        router.push(`/exam/${exam_id}/start`)
      }
  )
}

onMounted(() => {
  fetchExam()
  timer = setInterval(() => {
    if (countdown.value > 0) countdown.value--
  }, 1000)
})

onBeforeUnmount(() => {
  if (timer) clearInterval(timer)
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