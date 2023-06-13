<script>
import { defineComponent, ref } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import { INITIAL_EVENTS, createEventId } from "@/Pages/Dashboard/utils";
import { Switch } from "@headlessui/vue";
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from "@headlessui/vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SuccessButton from "@/Components/SuccessButton.vue";
import {EVENTS_TYPES} from "../helpers/_constants"
import { Inertia } from "@inertiajs/inertia";
import {EventService} from "../services"



export default defineComponent({
  props:{
    events: Array
  },
  components: {
    FullCalendar,
    Dialog,
    DialogTitle,
    TransitionRoot,
    DialogPanel,
    Switch,
    TransitionChild,
    SuccessButton,
    SecondaryButton,
  },
  created() {
    // fetch on init
    //this.fetchData()
    console.log({ events: this.events });
  },
  data() {
    return {
      calendarOptions: {
        plugins: [
          dayGridPlugin,
          timeGridPlugin,
          interactionPlugin, // needed for dateClick
        ],
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay",
        },
        initialView: "dayGridMonth",
        initialEvents: this.events, 
        editable: true,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: true,
        weekends: true,
        select: this.handleDateSelect,
        eventClick: this.handleEventClick,
       // eventsSet: this.handleEvents,
        eventData:{
          title:"",
          description:"",
          start:null,
          end:null,
          allDay:false,
          type:""
        },
        /* you can update a remote database when these fire:
        eventAdd:
        eventChange:
        eventRemove:
        */
        eventDidMount(info) {
          const eventType = info.event.extendedProps.type;
          const eventTypeData = EVENTS_TYPES.find(type => type.key === eventType);
  
          if (eventTypeData) {
            info.el.style.backgroundColor = eventTypeData.color;
          }
        },
      },
      currentEvents: [],
      openCreateEvent: false,
      selectInfo:{},
      eventTypes:EVENTS_TYPES
    };
  },
  methods: {
    handleWeekendsToggle() {
      this.calendarOptions.weekends = !this.calendarOptions.weekends; // update a property
    },
    handleDateSelect(selectInfo) {
      this.openCreateEvent = true;
      this.selectInfo = selectInfo
      this.eventData ={
        ...this.eventData,
        start: selectInfo.startStr,
        end: selectInfo.endStr,
        allDay: selectInfo.allDay
      }
      // let title = prompt('Please enter a new title for your event')
      // let calendarApi = selectInfo.view.calendar
      // calendarApi.unselect() // clear date selection
      // if (title) {
      //   calendarApi.addEvent({
      //     id: createEventId(),
      //     title,
      //     start: selectInfo.startStr,
      //     end: selectInfo.endStr,
      //     allDay: selectInfo.allDay
      //   })
      // }
    },
    closeModal() {
      this.openCreateEvent = false;
    },
    async addEvent(){
      const {title} = this.eventData
      this.selectInfo.start = this.eventData.start
      this.selectInfo.end = this.eventData.end 
      this.selectInfo.allDay = this.eventData.allDay
      let calendarApi = this.selectInfo.view.calendar
      calendarApi.unselect() // clear date selection
      if (title) {
        EventService.create(this.eventData)
        .then(async (rep)=>{
          await calendarApi.addEvent({
          id: createEventId(),
          ...this.eventData
        })
        this.eventData = {}
        })
        .catch((err)=>console.log({err}))
        
      }
      this.openCreateEvent = false;
    },
    handleEventClick(clickInfo) {
      if (
        confirm(
          `Are you sure you want to delete the event '${clickInfo.event.title}'`
        )
      ) {
        clickInfo.event.remove();
      }
    },
    // handleEvents(events) {
    //   this.currentEvents = events;
    // },
   
  },
});
</script>

<template>
  <div class="app">
    <div class="app-sidebar max-h-screen overflow-y-auto">
      <div class="app-sidebar-section">
        <h2>Instructions</h2>
        <ul>
          <li>Select dates and you will be prompted to create a new event</li>
          <li>Drag, drop, and resize events</li>
          <li>Click an event to delete it</li>
        </ul>
      </div>
      <div class="app-sidebar-section flex items-center space-x-4">
        <!-- <label>
          <input
            type='checkbox'
            :checked='calendarOptions.weekends'
            @change='handleWeekendsToggle'
          />
          toggle weekends
        </label> -->
        <span class="text-sm font-medium">Toggle weekends</span>
        <Switch
          v-model="calendarOptions.weekends"
          :class="calendarOptions.weekends ? 'bg-[#6c757e]' : 'bg-gray-200'"
          class="relative inline-flex h-6 w-11 items-center rounded-full"
          @click="handleWeekendsToggle"
        >
          <span class="sr-only">toggle weekends</span>
          <span
            :class="
              calendarOptions.weekends ? 'translate-x-6' : 'translate-x-1'
            "
            class="inline-block h-4 w-4 transform rounded-full bg-white transition"
          />
        </Switch>
      </div>
      <div class="app-sidebar-section">
        <h2>All Events ({{ events.length }})</h2>
        <!-- <ul>
          <li v-for="event in events" :key="event.id">
            <b>{{ event.start }}</b>
            <i>{{ event.title }}</i>
          </li>
        </ul> -->
        <div class="p-2 mb-2 bg-[#f3f5fb]" v-for="event in events" :key="event.id">
              <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <div :class="notify-icon" :style="`color: ${eventTypes.find(type => type.key === event.type)?.color};`">
                          <i
                              :class="`mdi ${eventTypes.find(type => type.key === event.type)?.icon}`"
                          ></i>
                      </div>
                  </div>
                  <div
                      class="flex-grow-1 text-truncate ms-2"
                  >
                      <h5
                          class="noti-item-title fw-semibold font-14"
                          :style="`color: ${eventTypes.find(type => type.key === event.type)?.color};`"
                      >
                          {{ event?.title}}
                          <!-- <small
                              class="fw-normal text-muted ms-1"
                              >{{ formatDistanceToNow(new Date(_notification?.created_at),{ addSuffix: true, locale: enGB }) }}</small
                          > -->
                      </h5>
                      <small
                          class="noti-item-subtitle text-muted "
                          >{{event?.description}}</small
                      >
                  </div>
              </div>
          </div>
      </div>
    </div>
    <div class="app-main">
      <FullCalendar class="app-calendar" :options="calendarOptions" >
        <template v-slot:eventContent="arg" >
          <div class="flex space-x-2 items-center">
            <i :class="`mdi ${eventTypes.find(type => type.key === arg.event.extendedProps.type)?.icon || 'mdi-calendar-clock'}`"></i>
            <div class="flex flex-col flex-start">
              <b class="uppercase">{{ arg.event.extendedProps.type }}</b>
              <span class="text-start">{{ arg.event.title }}</span>
              <!-- <div class="flex items-center justify-end">
                
                <div id="tooltip-container" v-if="arg.event.extendedProps.description">
                  <i class="mdi mdi-info" data-bs-container="#tooltip-container"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    :title="arg.event.extendedProps.description"></i>
                  </div>
              </div> -->
              
          </div>
          </div>
          
          
        </template>
      </FullCalendar>
    </div>
    <!--Modal create event -->
    <TransitionRoot appear :show="openCreateEvent" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-10">
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-black bg-opacity-25" />
        </TransitionChild>

        <div class="fixed inset-0 overflow-y-auto">
          <div
            class="flex min-h-full items-center justify-center p-4 text-center"
          >
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
            >
              <DialogPanel
                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg font-medium leading-6 text-gray-900 text-center"
                >
                  Add event
                </DialogTitle>
                <div class="mt-2 space-y-4">
                  <form>
                    <div class="mb-4">
                      <label
                        for="title"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Title
                      </label>
                      <input
                        type="text"
                        id="title"
                        v-model="eventData.title"
                        name="title"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      />
                    </div>

                    <div class="">
                      <label
                        for="description"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Description
                      </label>
                      <textarea
                        id="description"
                        name="description"
                        rows="3"
                        v-model="eventData.description"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      ></textarea>
                    </div>

                    <div class="flex space-x-4">
                      <div class="flex-1">
                        <label
                          for="startDate"
                          class="block text-sm font-medium text-gray-700"
                        >
                          Start Date
                        </label>
                        <input
                          type="date"
                          id="startDate"
                          name="startDate"
                          v-model="eventData.start"
                          class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>

                      <div class="flex-1">
                        <label
                          for="endDate"
                          class="block text-sm font-medium text-gray-700"
                        >
                          End Date
                        </label>
                        <input
                          type="date"
                          id="endDate"
                          name="endDate"
                          v-model="eventData.end"
                          class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                        />
                      </div>
                    </div>

                    <div class="mt-4">
                      <label
                        for="type"
                        class="block text-sm font-medium text-gray-700"
                      >
                        Type
                      </label>
                      <select
                        id="type"
                        name="type"
                        v-model="eventData.type"
                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                      >
                        <option value="">Select a type</option>
                        <option v-for="(_event,i) in eventTypes" :key="'event-'+i" :value="_event.key">{{ _event.title }}</option>
                      </select>
                    </div>

                    <!-- <div class="flex items-center mt-4">
                      <label for="allDay" class="flex items-center">
                        <input
                          type="checkbox"
                          id="allDay"
                          name="allDay"
                          class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                        />
                        <span class="ml-2 text-sm text-gray-700">All Day</span>
                      </label>
                    </div> -->
                  </form>
                </div>
                <div class="mt-4 flex items-center justify-center">
                  <SecondaryButton @click="closeModal">
                    Cancel
                  </SecondaryButton>

                  <SuccessButton class="ml-3" @click="addEvent">
                    Add
                  </SuccessButton>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<style lang="css">
h2 {
  margin: 0;
  font-size: 16px;
}
ul {
  margin: 0;
  padding: 0 0 0 1.5em;
}
li {
  margin: 1.5em 0;
  padding: 0;
}
b {
  /* used for event dates/times */
  margin-right: 3px;
}
.app {
  display: flex;
  min-height: 100%;
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}
.app-sidebar {
  width: 300px;
  line-height: 1.5;
  background: white;
}
.app-sidebar-section {
  padding: 2em;
}
.app-main {
  flex-grow: 1;
  padding: 3em;
}
.fc {
  /* the calendar root */
  max-width: 1100px;
  margin: 0 auto;
}
.fc .fc-button-primary{
  background-color: white !important ;
  border-color: #f3f5fb!important;
  margin-right: 2px;
  color: #6c757e !important ;
}
</style>
