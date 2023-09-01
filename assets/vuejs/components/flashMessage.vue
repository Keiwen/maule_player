<template>

</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: "flashMessage",
  props: {
    messageType: {
      type: String,
      default: 'info'
    },
    messageText: {
      type: String
    },
    messageTitle: {
      type: String
    }
  },
  methods: {
    ...mapActions(['addMessage'])
  },
  mounted () {
    this.addMessage({type: this.messageType, message: this.messageText, title: this.messageTitle})
  }

}
</script>

<style scoped>

</style>
