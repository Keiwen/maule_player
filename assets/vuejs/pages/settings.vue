<template>
  <div>
    <h1>{{ this.$trans('settings.title', {}, null, true) }}</h1>


    <hr/>
    <button class="btn btn-warning" @click="resetState">{{ this.$trans('settings.reset', {}, null, true) }}</button>

  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  name: "settingsPage",
  data () {
    return {
    }
  },
  methods: {
    ...mapActions(['resetState'])
  }
}
</script>

<style lang="scss" scoped>

</style>
