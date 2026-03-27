<template>
  <div class="exam-container" v-loading="loading">
    <el-card v-if="examPaper">
      <!-- 标题 -->
      <template #header>
        <div class="header">
          <h2>{{ examPaper.title }}</h2>
          <div>
            <el-tag style="margin-right: 10px" type="primary" effect="dark">
              时长：{{ examPaper.duration }} 分钟
            </el-tag>
            <el-tag style="margin-right: 10px" type="primary" effect="dark">
              总分：{{ examPaper.total_score }}
            </el-tag>
          </div>
        </div>
      </template>

      <!-- 单选题 -->
      <div v-if="grouped.single?.length">
        <h3>一、单选题</h3>
        <div v-for="(q, index) in grouped.single" :key="q.id" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-tag v-for="(text, key) in q.question_snapshot.options" :key="key" size="large"
                  style="margin-right: 10px" :type="isCorrect(q,key) ? 'success' : 'info'">
            {{ `${key}. ${text}` }}
          </el-tag>
        </div>
      </div>

      <!-- 多选题 -->
      <div v-if="grouped.multiple?.length">
        <h3>二、多选题</h3>
        <div v-for="(q, index) in grouped.multiple" :key="q.id" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-tag v-for="(text, key) in q.question_snapshot.options" :key="key" size="large" style="margin-right: 10px"
                  :type="isCorrect(q,key) ? 'success' : 'info'">
            {{ `${key}. ${text}` }}
          </el-tag>
        </div>
      </div>

      <!-- 判断题 -->
      <div v-if="grouped.true_false?.length">
        <h3>三、判断题</h3>
        <div v-for="(q, index) in grouped.true_false" :key="q.id" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-tag size="large" style="margin-right: 10px"
                  :type="q.question_snapshot.correct_answer ? 'success' : 'info'">
            正确
          </el-tag>
          <el-tag size="large" style="margin-right: 10px"
                  :type="q.question_snapshot.correct_answer ? 'info' : 'success'">
            错误
          </el-tag>
        </div>
      </div>

      <!-- 简答题 -->
      <div v-if="grouped.short_answer?.length">
        <h3>四、简答题</h3>
        <div v-for="(q, index) in grouped.short_answer" :key="q.id" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-input
              type="textarea"
              :model-value="q.question_snapshot.reference_answer"
              readonly
              autosize
          />
        </div>
      </div>
    </el-card>
  </div>
</template>

<script lang="ts" setup>
import {computed, onMounted, ref} from 'vue'
import {useRoute} from 'vue-router'
import {myGet} from "@/api/utils/axios";

const route = useRoute()
const examPaperId = Number(route.params.id)

const loading = ref(false)

type QuestionType = 'single' | 'multiple' | 'true_false' | 'short_answer'

interface Question {
  id: number
  question_type: QuestionType
  score: number
  question_snapshot: {
    content: string
    options?: Record<string, string>
    correct_answer?: string | string[]
    reference_answer?: string
  }
}

interface ExamPaper {
  title: string
  duration: number
  total_score: number
  questions: Question[]
}

const examPaper = ref<ExamPaper | null>(null)

// 获取考试
const fetchExamPaper = async () => {
  loading.value = true
  try {
    examPaper.value = await myGet(`/exam-paper/${examPaperId}/preview`)
  } finally {
    loading.value = false
  }
}

// 分组题目
const grouped = computed(() => {
  const groups: Record<QuestionType, Question[]> = {
    single: [],
    multiple: [],
    true_false: [],
    short_answer: []
  }
  examPaper.value?.questions?.forEach(q => {
    if (groups[q.question_type]) {
      groups[q.question_type].push(q)
    }
  })
  return groups
})
const isCorrect = (q: Question, key: string) => {
  const ans = q.question_snapshot.correct_answer
  if (Array.isArray(ans)) return ans.includes(key)
  return ans === key
}
onMounted(fetchExamPaper)
</script>

<style scoped>
.exam-container {
  max-width: 70vw;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.question {
  margin-bottom: 20px;
}
</style>