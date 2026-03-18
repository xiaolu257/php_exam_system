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

          <el-radio-group v-model="answers[qKey(q, index)]">
            <el-radio
                v-for="(text, key) in q.question_snapshot.options"
                :key="key"
                :label="key"
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

          <el-checkbox-group v-model="answers[qKey(q, index)]">
            <el-checkbox
                v-for="(text, key) in q.question_snapshot.options"
                :key="key"
                :label="key"
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

          <el-radio-group v-model="answers[qKey(q, index)]">
            <el-radio label="true">正确</el-radio>
            <el-radio label="false">错误</el-radio>
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
              v-model="answers[qKey(q, index)]"
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

<script setup>
import {computed, onMounted, ref} from 'vue'
import {useRoute} from 'vue-router'
import {myGet} from "@/api/utils/axios.ts";

const route = useRoute()
const examId = route.params.id

const loading = ref(false)
const exam = ref(null)

// 用户答案
const answers = ref({})

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
  const groups = {
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

// 生成唯一 key
const qKey = (q, index) => {
  return `${q.question_type}_${index}`
}

// 初始化答案
const initAnswers = () => {
  exam.value.questions.forEach((q, index) => {
    const key = qKey(q, index)

    if (q.question_type === 'multiple') {
      answers.value[key] = []
    } else {
      answers.value[key] = ''
    }
  })
}

// 提交
const submitExam = async () => {
  console.log('提交答案：', answers.value)

  // TODO: 调接口
  // await axios.post(`/exam-papers/${examId}/submit`, {
  //   answers: answers.value
  // })
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