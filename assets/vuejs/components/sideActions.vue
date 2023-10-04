<template>
  <div class="btn-group dropleft">
    <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    </button>
    <div class="dropdown-menu" style="">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "sideActions"
}
</script>

<style scoped>

</style>
