<script>
import { defineComponent } from "vue";
import {EVENTS_TYPES} from "../../../helpers/_constants"
export default defineComponent({

  props:{
    events:Array
  },
  data() {
    return{
      eventTypes:EVENTS_TYPES
    }
  }
  
});
</script>

<template>
  <div
    class="w-full border border-solid border-r-[#d3e2e8] border-y-0 border-l-0 bg-white p-12"
  >
    <div class="p-8">
      <h2>Event for this month ({{ events.length }})</h2>
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
</template>

<style lang='css'>
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

</style>