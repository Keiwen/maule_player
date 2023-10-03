<template>
  <div class="custom-player-timeline" :style="cssVars">
    <input type="range" max="100" :value="percentProgress" @change="changeTime">
  </div>
</template>

<script>
export default {
  name: "timeline",
  props: {
    percentProgress: {
      type: Number,
      required: true
    }
  },
  computed: {
    cssVars () {
      return {
        '--custom-player-percent-progress': this.percentProgress + '%'
      }
    }
  },
  methods: {
    changeTime (e) {
      this.$emit('change-progress', e.srcElement.value)
    }
  }
}
</script>

<style lang="scss" scoped>

.custom-player-timeline {
  width: 98%;
  input[type="range"] {
    width: 100%;
    cursor: pointer;
    -webkit-appearance: none;
    height: 30px;
    background-color: var(--background);
    padding: 5px 0;
    border-radius: 25px;
    position: relative;


    //CHROME
    &::-webkit-slider-runnable-track {
      height: 5px;
      background: rgba(0, 0, 0, 0.3);
    }
    &::-webkit-slider-thumb {
      position: relative;
      -webkit-appearance: none;
      box-sizing: content-box;
      height: 18px;
      width: 18px;
      border-radius: 50%;
      background-color: var(--secondary);
      border: 1px solid var(--light);
      margin: -8px 0 0 0;
    }
    &:active::-webkit-slider-thumb {
      transform: scale(1.2);
    }
    // next element is used to display timeline before thumb
    // however, we need to compute its width and dynamically adjust it
    // not shown on firefox, replaced by range-progress pseudo element
    &::before {
      position: absolute;
      content: "";
      top: 12px;
      left: 0;
      width: var(--custom-player-percent-progress);
      height: 5px;
      background-color: var(--dark);
    }


    //FIREFOX
    &::-moz-range-track {
      height: 5px;
      background: rgba(0, 0, 0, 0.3);
    }
    &::-moz-range-progress {
      background-color: var(--dark);
    }
    &::-moz-range-thumb {
      position: relative;
      -webkit-appearance: none;
      box-sizing: content-box;
      height: 18px;
      width: 18px;
      border-radius: 50%;
      background-color: var(--secondary);
      border: 1px solid var(--light);
      margin: -8px 0 0 0;
    }
    &:active::-moz-range-thumb {
      transform: scale(1.2);
    }


    //EDGE
    &::-ms-track {
      width: 100%;
      height: 5px;
      background: transparent;
      border: solid transparent;
      color: transparent;
    }
    &::-ms-fill-lower {
      background-color: var(--dark);
    }
    &::-ms-fill-upper {
      background: rgba(0, 0, 0, 0.3);
    }
    &::-ms-thumb {
      position: relative;
      -webkit-appearance: none;
      box-sizing: content-box;
      height: 18px;
      width: 18px;
      border-radius: 50%;
      background-color: var(--secondary);
      border: 1px solid var(--light);
      margin: -8px 0 0 0;
    }
    &:active::-ms-thumb {
      transform: scale(1.2);
    }
  }
}

</style>
