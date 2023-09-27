<template>
  <div>
    <ul class="list-group">
      <li class="list-group-item container" v-for="album in albumList">
        <album-list-item :album="album"></album-list-item>
      </li>
    </ul>
  </div>
</template>

<script>
import albumListItem from "./albumListItem";

export default {
  name: "albumList",
  components: { albumListItem },
  props: {
    albumList: {
      type: Array,
      required: true
    }
  }
}
</script>

<style scoped>

  .list-group-item {
    background-color: transparent;
    padding: .2rem 0.5rem;
    border: 1px solid var(--primary);
  }

</style>
