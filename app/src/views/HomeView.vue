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

        <div class="flex justify-between items-center mb-4">
          <button @click="previousMonth" class="cursor-pointer px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">
            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 6l-6 6l6 6" /></svg>
          </button>

          <h2 class="text-xl font-bold">{{ currentMonthName }} / {{ currentYear }}</h2>
          
          <button @click="nextMonth" class="cursor-pointer px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">
            <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
          </button>
        </div>

        <button @click="goToToday" class="cursor-pointer mb-4 px-4 py-2 bg-blue-700 hover:bg-blue-600 rounded">Hoje</button>

        <div class="grid grid-cols-7 gap-2 text-center">

          <div v-for="day in daysOfWeek" :key="day" class="font-bold">{{ day }}</div>

          <div
            v-for="day in previousMonthDays"
            :key="`prev-${day}`"
            :class="{
              'bg-green-500/30': isInRange(day, -1) === 'green',
              'bg-gray-50/70': isInRange(day, -1) === 'white',
              'bg-sky-600/30 text-white': isLastInRange(day, -1),
            }"
            class="text-gray-500 px-4 py-2 rounded"
          >
            {{ day }}
          </div>
          
          <div
            v-for="day in daysInMonth"
            :key="`day-${day}`"
          >
            <div v-if="isToday(day)" class="bg-red-500 text-white px-4 py-2 rounded cursor-pointer relative z-0">
              <a href="https://twitch.tv/omeiaum" target="_blank">
                <span>
                  {{ day }}
                </span>
                <img v-if="isToday(day)" src="/img/peepoMeiaTalk.gif" alt="WAJAJA" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
              </a>
            </div>

            <div v-else
              :class="{
                'bg-green-500 text-white': isInRange(day, 0) === 'green',
                'bg-gray-50 text-black': isInRange(day, 0) === 'white',
                'bg-sky-600 text-white cursor-pointer': isLastInRange(day, 0),
              }"
              class="px-4 py-2 rounded relative z-0"
              @mousedown="() => isLastInRange(day, 0) && showToast()"
            >
              <span>
                {{ day }}
              </span>

              <img v-if="isLastInRange(day, 0)" src="/img/meiaA.gif" alt="Animação do meia um desaparecendo" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
  
              <img v-if="!isToday(day) && isInRange(day, 0) === 'green'" src="/img/meiaJOIA.webp" alt="Meia um fazendo joia com o polegar" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
  
              <img v-if="!isToday(day) && isInRange(day, 0) === 'white' && !isLastInRange(day, 0)" src="/img/meiaBedge.png" alt="Meia um dormindo confi" class="absolute top-1/2 -translate-y-1/2 z-10 w-7 hover:opacity-0">
            </div>
          </div>

          <div
            v-for="day in nextMonthDays"
            :key="`next-${day}`"
            :class="{
              'bg-green-500/30': isInRange(day, 1) === 'green',
              'bg-gray-50/70': isInRange(day, 1) === 'white',
              'bg-sky-600/30 text-white': isLastInRange(day, 1),
            }"
            class="text-gray-500 px-4 py-2 rounded"
          >
            {{ day }}
          </div>

        </div>

        <div class="mt-4 font-extrabold text-end">
          {{ totalDays }} dias de live...<br>
          {{ timeUntilUpdate }} atualiza
        </div>

      </div>
    </div>

    <div class="text-white text-center text-sm py-3">
        2025&copy;, Feito com 🙂 por <a href="https://github.com/eunael" class="link text-blue-500"
            target="_blank">eunael</a>.
    </div>

    <Transition name="fade">
      <div
        v-if="showToastState"
        class="bg-black text-gray-50 text-nowrap absolute w-full h-screen flex justify-center items-center"
      >
        <div class="text-center font-bold">
          <p>Termina, talvez...</p>
          <p class="sm:text-4xl text-2xl mt-3">
            {{ lastTime }}
          </p>
          <img class="mx-auto mt-8" src="/img/MeiaTimer.gif" alt="Meia um olhando para cima vendo o timer da subathon comicamente alto">
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup lang="ts">
  import axios from 'axios';
  import moment, { type Moment } from 'moment'
  import 'moment/locale/pt-br'
  import { computed, type Ref, ref } from 'vue';
  
  const momentbr = (...args: any) => moment(...args).locale('pt-br')

  const currentYear = ref(momentbr().year())
  const currentMonth = ref(momentbr().month())
  const daysOfWeek = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"]
  const highlightedRange: Ref<Moment[]> = ref([])
  const timeToUpdate = ref("00:00:00")
  const timeUntilUpdate = ref("00:00")
  const totalDays = ref(0)
  const lastTime = ref('')
  const showToastState = ref(false)
  let timerInterval: any = null
 
  function nextMonth() {
    const next = momentbr({ year: currentYear.value, month: currentMonth.value }).add(1, "month");
    currentYear.value = next.year();
    currentMonth.value = next.month();
  }
  function previousMonth() {
    const prev = momentbr({ year: currentYear.value, month: currentMonth.value }).subtract(1, "month");
    currentYear.value = prev.year();
    currentMonth.value = prev.month();
  }
  function goToToday() {
    const today = momentbr();
    currentYear.value = today.year();
    currentMonth.value = today.month();
  }
  function isToday(day: number) {
    return momentbr({ year: currentYear.value, month: currentMonth.value, day }).isSame(momentbr(), "day");
  }
  function isInRange(day: number, offset: number) {
    const date = momentbr({ year: currentYear.value, month: currentMonth.value }).add(offset, "month").date(day);

    if (highlightedRange.value.some(highlightedDate => date.isSame(highlightedDate, "day"))) {
      return date.isAfter(momentbr()) ? "white" : "green";
    }
    return null;
  }
  function isLastInRange(day: number, offset: number) {
    const date = momentbr({ year: currentYear.value, month: currentMonth.value }).add(offset, "month").date(day);
    const lastDate = highlightedRange.value[highlightedRange.value.length - 1];
    return lastDate && date.isSame(lastDate, "day");
  }
  function updateTimer() {
    const now = momentbr();
    const toUpdate = momentbr(timeToUpdate.value);
    const duration = moment.duration(toUpdate.diff(now));

    const minutes = String(duration.minutes()).padStart(2, "0");
    const seconds = String(duration.seconds()).padStart(2, "0");

    timeUntilUpdate.value = `${minutes}:${seconds}`;

    if (duration.asSeconds() <= 0) {
      clearInterval(timerInterval);
      fetchTimestamp(); // Atualiza o calendário quando der meia-noite
    }
  }
  function startCountdown() {
    clearInterval(timerInterval); // Evita múltiplos intervalos
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
  }
  function showToast() {
    showToastState.value = true;
    setTimeout(() => {
      showToastState.value = false;
    }, 3000);
  }

  const currentMonthName = computed(() => {
    return ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'][currentMonth.value]
  })
  const daysInMonth = computed(() => {
    return momentbr({ year: currentYear.value, month: currentMonth.value }).daysInMonth();
  })
  const previousMonthDays = computed(() => {
    const firstDayOfMonth = momentbr({ year: currentYear.value, month: currentMonth.value, day: 1 }).day();
    const prevMonthDays = momentbr({ year: currentYear.value, month: currentMonth.value }).subtract(1, "month").daysInMonth();
    return Array.from({ length: firstDayOfMonth }, (_, i) => prevMonthDays - firstDayOfMonth + i + 1);
  })
  const nextMonthDays = computed(() => {
    const lastDayOfMonth = momentbr({ year: currentYear.value, month: currentMonth.value }).endOf("month").day();
    return Array.from({ length: 6 - lastDayOfMonth }, (_, i) => i + 1);
  })

  async function fetchTimestamp() {
    try {
      const { data } = await axios.get('https://api-calendario-subathonico.nziim.com/api/time').then(res => res)
      // const { data } = await axios.get('http://localhost:8000/api/time').then(res => res)

      const finalTime = momentbr(data?.finalTime);

      timeToUpdate.value = data.timeToUpdate
      totalDays.value = data.totalDays
      lastTime.value = finalTime.format('DD/MM/YYYY à[s] HH:mm:ss')
      
      const endDate = finalTime;
      const startDate = momentbr("2024-04-26");

      const range = [];
      let currentDate = momentbr(startDate);
      
      while (currentDate.isSameOrBefore(endDate)) {
        range.push(momentbr(currentDate));
        currentDate.add(1, "day");
      }

      highlightedRange.value = range;

      startCountdown()
    } catch (error) {
      console.error("Erro ao buscar o timestamp:", error);
    }
  }

  fetchTimestamp()
</script>

<style>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.5s ease;
  }

  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
</style>
