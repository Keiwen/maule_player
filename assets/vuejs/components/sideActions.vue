<template>
  <div class="btn-group dropleft side-actions">
    <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Open side actions">
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

<style lang="scss" scoped>

  .side-actions {
    float: right;
    margin-top: -43px;
  }

</style>
