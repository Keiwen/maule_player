<template>
  <div>
    <h1>{{ this.$trans('playlist.title', {}, null, true) }}</h1>

    <button class="btn btn-dark" @click="emptyPlaylist">
      <i class="fa fa-broom" />
      {{ this.$trans('playlist.empty', {}, null, true) }}
    </button>
    <hr/>
  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  name: "playlistPage",
  data () {
    return {
    }
  },
  methods: {
    ...mapActions(['emptyPlaylist'])
  }
}
</script>

<style scoped>

</style>
