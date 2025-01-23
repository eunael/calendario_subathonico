<template>
    <div class="flex flex-col items-center justify-center h-screen bg-black text-white">
      <div class="flex justify-center items-center flex-grow">
        <div class="w-full max-w-md px-4 sm:px-0">
  
          <div class="mb-8 flex justify-between items-center gap-2">
            <div class="text-end text-4xl font-extrabold ">
              Calendário Subathônico
            </div>
  
            <img src="/img/PETTHEPEEPOMEIAUM.gif" alt="PET THE PEEPO MEIAUM">
          </div>
  
          <!-- Calendar navigation -->
          <div class="flex justify-between items-center mb-4">
            <button @click="previousMonth" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">
              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
            </button>
  
            <h2 class="text-xl font-bold">{{ currentMonthName }} {{ currentYear }}</h2>
            
            <button @click="nextMonth" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">
              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
            </button>
          </div>
  
          <button @click="goToToday" class="mb-4 px-4 py-2 bg-blue-700 hover:bg-blue-600 rounded">Hoje</button>
          <!-- Calendar navigation -->
  
          <div class="grid grid-cols-7 gap-2 text-center">
            
            <div v-for="day in daysOfWeek" :key="day" class="font-bold">{{ day }}</div>
  
            <div
              v-for="day in previousMonthDays"
              :key="`prev-${day}`"
              :class="{
                'bg-green-500 bg-opacity-20 text-white': isInRange(day, -1) === 'green',
                'bg-white bg-opacity-20 text-black': isInRange(day, -1) === 'white',
                'bg-sky-600 bg-opacity-20 text-white': isLastInRange(day, -1),
                'text-gray-500': isInRange(day, -1) === null,
              }"
              class="px-4 py-2 rounded cursor-pointer"
            >
              {{ day }}
            </div>
  
            <div
              v-for="day in daysInMonth"
              :key="`day-${day}`"
              :class="{
                'bg-red-500 text-white': isToday(day),
                'bg-green-500 text-white': !isToday(day) && isInRange(day, 0) === 'green',
                'bg-gray-50 text-black': !isToday(day) && isInRange(day, 0) === 'white',
                'bg-sky-600 text-white': isLastInRange(day, 0),
                'text-gray-500': !isToday(day) && isInRange(day, 0) === null,
              }"
              class="px-4 py-2 rounded cursor-pointer relative z-0"
            >
              <span class="z-20">
                {{ day }}
              </span>
  
              <img v-if="isLastInRange(day, 0)" src="/img/meiaA.gif" alt="WAJAJA" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
  
              <img v-if="!isToday(day) && isInRange(day, 0) === 'green'" src="/img/meiaJOIA.webp" alt="WAJAJA" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
              
              <img v-if="isToday(day)" src="/img/peepoMeiaTalk.gif" alt="WAJAJA" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
  
              <img v-if="!isToday(day) && isInRange(day, 0) === 'white' && !isLastInRange(day, 0)" src="/img/meiaBedge.png" alt="WAJAJA" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
  
            </div>
  
            <div
              v-for="day in nextMonthDays"
              :key="`next-${day}`"
              :class="{
                'bg-green-500 bg-opacity-20 text-white': isInRange(day, 1) === 'green',
                'bg-white bg-opacity-20 text-black': isInRange(day, 1) === 'white',
                'bg-sky-600 bg-opacity-20 text-white': isLastInRange(day, 1),
                'text-gray-500': isInRange(day, 1) === null,
              }"
              class="px-4 py-2 rounded cursor-pointer"
            >
              {{ day }}
            </div>
  
          </div>
  
          <div class="mt-4 font-extrabold text-end">
            {{ highlightedRange.length }} dias de live...
          </div>
        </div>
      </div>

      <div class="text-white text-center text-sm py-3">
          2025&copy;, Feito com 🙂 por <a href="https://github.com/eunael" class="link text-blue-500"
              target="_blank">eunael</a>.
      </div>
    </div>
</template>

<script setup lang="ts">
  import moment, { type Moment } from "moment";
  import 'moment/locale/pt-br'
  
  const currentYear = ref(moment().year())
  const currentMonth = ref(moment().month())
  const daysOfWeek = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"]
  const highlightedRange: Ref<Moment[]> = ref([])
  
  function isToday(day: number) {
    return moment({ year: currentYear.value, month: currentMonth.value, day }).isSame(moment(), "day");
  }
  function isInRange(day: number, offset: number) {
    const date = moment({ year: currentYear.value, month: currentMonth.value }).add(offset, "month").date(day);

    if (highlightedRange.value.some(highlightedDate => date.isSame(highlightedDate, "day"))) {
      return date.isAfter(moment()) ? "white" : "green";
    }
    return null;
  }
  function nextMonth() {
    const next = moment({ year: currentYear.value, month: currentMonth.value }).add(1, "month");
    currentYear.value = next.year();
    currentMonth.value = next.month();
  }
  function previousMonth() {
    const prev = moment({ year: currentYear.value, month: currentMonth.value }).subtract(1, "month");
    currentYear.value = prev.year();
    currentMonth.value = prev.month();
  }
  function goToToday() {
    const today = moment();
    currentYear.value = today.year();
    currentMonth.value = today.month();
  }
  function isLastInRange(day: number, offset: number) {
    const date = moment({ year: currentYear.value, month: currentMonth.value }).add(offset, "month").date(day);
    const lastDate = highlightedRange.value[highlightedRange.value.length - 1];
    return lastDate && date.isSame(lastDate, "day");
  }
  async function fetchTimestamp() {
    try {
      const { data: time } = await useFetch('/api/get-time')

      const timestamp = Number(time.value?.timestamp);
      const endDate = moment(timestamp);
      const startDate = moment("2024-04-26");

      const range = [];
      let currentDate = moment(startDate);

      while (currentDate.isSameOrBefore(endDate)) {
        range.push(moment(currentDate));
        currentDate.add(1, "day");
      }

      highlightedRange.value = range;
    } catch (error) {
      console.error("Erro ao buscar o timestamp:", error);
    }
  }

  const currentMonthName = computed(() => {
    return moment({ year: currentYear.value, month: currentMonth.value }).format("MMMM");
  })
  const daysInMonth = computed(() => {
    return moment({ year: currentYear.value, month: currentMonth.value }).daysInMonth();
  })
  const previousMonthDays = computed(() => {
    const firstDayOfMonth = moment({ year: currentYear.value, month: currentMonth.value, day: 1 }).day();
    const prevMonthDays = moment({ year: currentYear.value, month: currentMonth.value }).subtract(1, "month").daysInMonth();
    return Array.from({ length: firstDayOfMonth }, (_, i) => prevMonthDays - firstDayOfMonth + i + 1);
  })
  const nextMonthDays = computed(() => {
    const lastDayOfMonth = moment({ year: currentYear.value, month: currentMonth.value }).endOf("month").day();
    return Array.from({ length: 6 - lastDayOfMonth }, (_, i) => i + 1);
  })

  await fetchTimestamp()
</script>