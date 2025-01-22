<template>
    <div class="flex flex-col items-center justify-center h-screen bg-black text-white">
      <div class="w-full max-w-md px-4 sm:px-0">
        <div class="flex justify-between items-center mb-4">
          <button @click="previousMonth" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.707 4.707a1 1 0 00-1.414-1.414l-5 5a1 1 0 000 1.414l5 5a1 1 0 001.414-1.414L8.414 10l-4.293-4.293z" clip-rule="evenodd" />
            </svg>
          </button>
          <h2 class="text-xl font-bold">{{ currentMonthName }} {{ currentYear }}</h2>
          <button @click="nextMonth" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M7.293 15.293a1 1 0 001.414 1.414l5-5a1 1 0 000-1.414l-5-5a1 1 0 00-1.414 1.414L11.586 10l-4.293 4.293z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
        <button @click="goToToday" class="mb-4 px-4 py-2 bg-blue-700 hover:bg-blue-600 rounded">Hoje</button>
        <div class="grid grid-cols-7 gap-2 text-center">
          <div v-for="day in daysOfWeek" :key="day" class="font-bold">{{ day }}</div>
          <div
            v-for="day in previousMonthDays"
            :key="`prev-${day}`"
            :class="{
              'bg-green-500 bg-opacity-20 text-white': isPreviousDayInRange(day),
              'text-gray-500': !isPreviousDayInRange(day)
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
              'bg-green-500 text-white': isInRange(day),
              'bg-gray-700 hover:bg-gray-600': !isToday(day) && !isInRange(day),
            }"
            class="px-4 py-2 rounded cursor-pointer"
          >
            {{ day }}
          </div>
          <div
            v-for="day in nextMonthDays"
            :key="`next-${day}`"
            :class="{
              'bg-green-500 bg-opacity-20 text-white': isNextDayInRange(day),
              'text-gray-500': !isNextDayInRange(day)
            }"
            class="px-4 py-2 rounded cursor-pointer"
          >
            {{ day }}
          </div>
        </div>
      </div>
      <form @submit.prevent="submitRange" class="mt-4 text-center">
        <div class="mb-2">
          <label for="inicio" class="block text-sm font-medium">Início</label>
          <input type="date" id="inicio" v-model="rangeStart" class="px-4 py-2 bg-gray-800 text-white rounded" required />
        </div>
        <div class="mb-4">
          <label for="fim" class="block text-sm font-medium">Fim</label>
          <input type="date" id="fim" v-model="rangeEnd" class="px-4 py-2 bg-gray-800 text-white rounded" required />
        </div>
        <button type="submit" class="px-4 py-2 bg-green-700 hover:bg-green-600 rounded mr-2">Submeter</button>
        <button type="button" @click="clearRange" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded">Limpar</button>
      </form>
    </div>
</template>
  
<script>
  import moment from "moment";
  
  export default {
    data() {
      return {
        currentYear: moment().year(),
        currentMonth: moment().month(),
        daysOfWeek: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
        rangeStart: "",
        rangeEnd: "",
        highlightedRange: [],
      };
    },
    computed: {
      currentMonthName() {
        return moment({ year: this.currentYear, month: this.currentMonth }).format("MMMM");
      },
      daysInMonth() {
        return moment({ year: this.currentYear, month: this.currentMonth }).daysInMonth();
      },
      previousMonthDays() {
        const firstDayOfMonth = moment({ year: this.currentYear, month: this.currentMonth, day: 1 }).day();
        const prevMonthDays = moment({ year: this.currentYear, month: this.currentMonth }).subtract(1, "month").daysInMonth();
        return Array.from({ length: firstDayOfMonth }, (_, i) => prevMonthDays - firstDayOfMonth + i + 1);
      },
      nextMonthDays() {
        const lastDayOfMonth = moment({ year: this.currentYear, month: this.currentMonth }).endOf("month").day();
        return Array.from({ length: 6 - lastDayOfMonth }, (_, i) => i + 1);
      },
    },
    methods: {
      isToday(day) {
        return moment({ year: this.currentYear, month: this.currentMonth, day }).isSame(moment(), "day");
      },
      isInRange(day) {
        const date = moment({ year: this.currentYear, month: this.currentMonth, day });
        return this.highlightedRange.some(highlightedDate => date.isSame(highlightedDate, "day"));
      },
      isPreviousDayInRange(day) {
        const date = moment({ year: this.currentYear, month: this.currentMonth }).subtract(1, "month").date(day);
        return this.highlightedRange.some(highlightedDate => date.isSame(highlightedDate, "day"));
      },
      isNextDayInRange(day) {
        const date = moment({ year: this.currentYear, month: this.currentMonth }).add(1, "month").date(day);
        return this.highlightedRange.some(highlightedDate => date.isSame(highlightedDate, "day"));
      },
      nextMonth() {
        const next = moment({ year: this.currentYear, month: this.currentMonth }).add(1, "month");
        this.currentYear = next.year();
        this.currentMonth = next.month();
      },
      previousMonth() {
        const prev = moment({ year: this.currentYear, month: this.currentMonth }).subtract(1, "month");
        this.currentYear = prev.year();
        this.currentMonth = prev.month();
      },
      goToToday() {
        const today = moment();
        this.currentYear = today.year();
        this.currentMonth = today.month();
      },
      submitRange() {
        const startDate = moment(this.rangeStart);
        const endDate = moment(this.rangeEnd);
  
        if (startDate.isAfter(endDate)) {
          alert("A data de início não pode ser maior que a data de fim.");
          return;
        }
  
        const range = [];
        let currentDate = moment(startDate);
  
        while (currentDate.isSameOrBefore(endDate)) {
          range.push(moment(currentDate));
          currentDate.add(1, "day");
        }
  
        this.highlightedRange = range;
      },
      clearRange() {
        this.rangeStart = "";
        this.rangeEnd = "";
        this.highlightedRange = [];
      },
    },
  };
</script>
  
<style scoped>
body {
margin: 0;
}
</style>
  