<template>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
    <circle cx="50" cy="50" r="32" stroke-width="8" stroke="#963546" stroke-dasharray="50.26548245743669 50.26548245743669" fill="none" stroke-linecap="round">
      <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;360 50 50"></animateTransform>
    </circle>
    <circle cx="50" cy="50" r="23" stroke-width="8" stroke="#fdad72" stroke-dasharray="36.12831551628262 36.12831551628262" stroke-dashoffset="36.12831551628262" fill="none" stroke-linecap="round">
      <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite" keyTimes="0;1" values="0 50 50;-360 50 50"></animateTransform>
    </circle>
  </svg>
</template>

<script>
export default {
  name: "artistIcon"
}
</script>

<style lang="scss" scoped>
svg {
  margin: auto;
  display: block;
  height: 150px;
  width: 150px;
}

</style>
