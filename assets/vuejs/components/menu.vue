<template>
  <div class="row">
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu">Home</router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu btn-menu-middle">Home</router-link>
    </div>
    <div class="col-4 text-center">
      <button class="btn btn-primary btn-menu" @click="resetState">Reset</button>
    </div>
  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  name: "appMenu",
  methods: {
    ...mapActions(['resetState'])
  }
}
</script>

<style scoped>

</style>
