<template>
  <div class="row">
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu">Home</router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu btn-menu-middle">Home</router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu">Home</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: "appMenu"
}
</script>

<style scoped>

</style>
