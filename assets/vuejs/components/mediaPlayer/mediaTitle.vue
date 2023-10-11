<template>
  <div class="custom-player-text-wrapper">
    <span class="row custom-player-text" :class="{'custom-player-text-animated': titleWidthTooLarge}" id="audio_player_title">{{ mediaText }}</span>
  </div>
</template>

<script>

export default {
  name: "mediaTitle",
  props: {
    mediaText: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
      audioTitleElement: null,
    }
  },
  mounted () {
    this.audioTitleElement = document.getElementById('audio_player_title')
  },
  computed: {
    titleWidthTooLarge () {
      if (this.audioTitleElement === null) return false
      // try to get default method getComputedStyle
      if (!getComputedStyle) return true
      const elementStyle = getComputedStyle(this.audioTitleElement)
      // compute text width: offsetWidth property include padding
      const textWidth = this.audioTitleElement.offsetWidth - parseFloat(elementStyle.paddingLeft) - parseFloat(elementStyle.paddingRight)
      return textWidth >= this.$root.windowSize.width
    }
  }
}
</script>

<style lang="scss" scoped>

  @keyframes movingText {
    0% {
      transform: translate(0, 0);
    }
    100% {
      transform: translate(-100%, 0);
    }
  }

  .custom-player-text-wrapper {
    max-width: 100%;
    overflow: hidden;
    .custom-player-text {
      font-size: x-large;
      color: var(--light);
      text-shadow: var(--secondary) 0 0 10px;
      white-space: nowrap;
      display: inline-block;
      &.custom-player-text-animated {
        animation: movingText 10s infinite linear;
        padding-left: 100%;
        &:hover {
          animation-play-state: paused;
        }
      }
    }
  }

</style>
