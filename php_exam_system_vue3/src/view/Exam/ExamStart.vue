<template>
  <div class="exam-container" v-loading="loading">
    <el-card v-if="exam">
      <!-- 标题 -->
      <template #header>
        <div class="header">
          <h2>{{ exam.title }}</h2>
          <div class="meta">
            <span>时长：{{ exam.duration }} 分钟</span>
            <span>总分：{{ exam.total_score }}</span>
          </div>
        </div>
      </template>

      <!-- 单选题 -->
      <div v-if="grouped.single?.length">
        <h3>一、单选题</h3>
        <div v-for="(q, index) in grouped.single" :key="index" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-radio-group v-model="answers.single_questions[index].answer">
            <el-radio
                v-for="(text, key) in q.question_snapshot.options"
                :key="key"
                :label="`${key}. ${text}`"
                :value="key"
            >
              {{ key }}. {{ text }}
            </el-radio>
          </el-radio-group>
        </div>
      </div>

      <!-- 多选题 -->
      <div v-if="grouped.multiple?.length">
        <h3>二、多选题</h3>
        <div v-for="(q, index) in grouped.multiple" :key="index" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-checkbox-group v-model="answers.multiple_questions[index].answer">
            <el-checkbox
                v-for="(text, key) in q.question_snapshot.options"
                :key="key"
                :label="key"
                :value="key"
            >
              {{ key }}. {{ text }}
            </el-checkbox>
          </el-checkbox-group>
        </div>
      </div>

      <!-- 判断题 -->
      <div v-if="grouped.true_false?.length">
        <h3>三、判断题</h3>
        <div v-for="(q, index) in grouped.true_false" :key="index" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-radio-group v-model="answers.true_false_questions[index].answer">
            <el-radio :value="1">正确</el-radio>
            <el-radio :value="0">错误</el-radio>
          </el-radio-group>
        </div>
      </div>

      <!-- 简答题 -->
      <div v-if="grouped.short_answer?.length">
        <h3>四、简答题</h3>
        <div v-for="(q, index) in grouped.short_answer" :key="index" class="question">
          <p>{{ index + 1 }}. {{ q.question_snapshot.content }}（{{ q.score }}分）</p>

          <el-input
              type="textarea"
              v-model="answers.short_answer_questions[index].answer"
              placeholder="请输入答案"
          />
        </div>
      </div>

      <!-- 提交 -->
      <div class="submit">
        <el-button type="primary" @click="submitExam">提交试卷</el-button>
      </div>
    </el-card>
  </div>
</template>

<script lang="ts" setup>
import {computed, onMounted, ref} from 'vue'
import {useRoute} from 'vue-router'
import {myGet, myPost} from "@/api/utils/axios";
import MyMessageBox from "@/api/MyMessageBox";
import MyMessage from "@/utils/MyMessage";

const route = useRoute()
const examId = route.params.id

const loading = ref(false)

type QuestionType = 'single' | 'multiple' | 'true_false' | 'short_answer'

interface Question {
  id: number
  question_type: QuestionType
  score: number
  question_snapshot: {
    content: string
    options?: Record<string, string>
  }
}

interface Exam {
  title: string
  description: string
  duration: number
  total_score: number
  start_time: string
  end_time: string
  max_attempts: number
  single_count: number
  multiple_count: number
  true_false_count: number
  short_answer_count: number
  questions: Question[]
}

const exam = ref<Exam>({
  title: '',
  description: '',
  duration: 0,
  total_score: 0,
  start_time: '',
  end_time: '',
  max_attempts: 0,
  single_count: 0,
  multiple_count: 0,
  true_false_count: 0,
  short_answer_count: 0,
  questions: []
})

interface Answers {
  single_questions: { id: number; answer: string }[],
  multiple_questions: { id: number; answer: string[] }[],
  true_false_questions: { id: number; answer: -1 | 0 | 1 }[],
  short_answer_questions: { id: number; answer: string }[],
}

// 用户答案
const answers = ref<Answers>({
  single_questions: [],
  multiple_questions: [],
  true_false_questions: [],
  short_answer_questions: []
})

// 获取考试
const fetchExam = async () => {
  loading.value = true
  try {
    exam.value = await myGet(`/exam-paper/${examId}/questions`)
    // 初始化答案结构
    initAnswers()
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

  exam.value?.questions?.forEach(q => {
    groups[q.question_type].push(q)
  })
  return groups
})

// 初始化答案
const initAnswers = () => {
  answers.value = {
    single_questions: [],
    multiple_questions: [],
    true_false_questions: [],
    short_answer_questions: []
  }

  exam.value.questions.forEach((q) => {
    if (q.question_type === 'single') {
      answers.value.single_questions.push({
        id: q.id,
        answer: ""
      })
    } else if (q.question_type === 'multiple') {
      answers.value.multiple_questions.push({
        id: q.id,
        answer: []
      })
    } else if (q.question_type === 'true_false') {
      answers.value.true_false_questions.push({
        id: q.id,
        answer: -1
      })
    } else if (q.question_type === 'short_answer') {
      answers.value.short_answer_questions.push({
        id: q.id,
        answer: ''
      })
    }
  })
}

const isEmptyAnswer = (q: any) => {
  if (typeof q.answer === 'string') return q.answer === ''
  if (Array.isArray(q.answer)) return q.answer.length === 0
  if (typeof q.answer === 'number') return q.answer === -1
  return true
}

const hasUnfinished = computed(() => {
  const allAnswers = [
    ...answers.value.single_questions,
    ...answers.value.multiple_questions,
    ...answers.value.true_false_questions,
    ...answers.value.short_answer_questions
  ]

  return allAnswers.some(isEmptyAnswer)
})
// 提交
const submitExam = async () => {
  if (hasUnfinished.value) {
    try {
      await MyMessageBox.confirm('有未作答的题目，确认要交卷吗？')
    } catch {
      MyMessage.info('取消交卷')
      return
    }
  }

  await myPost(`/exam-paper/${examId}/submit`, answers.value)
  MyMessage.success('交卷成功')

}

onMounted(fetchExam)
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

.meta span {
  margin-left: 15px;
  color: #666;
}

.question {
  margin-bottom: 20px;
}

.submit {
  margin-top: 30px;
  text-align: center;
}
</style>