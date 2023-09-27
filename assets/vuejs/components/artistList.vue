<template>
  <div>
    <ul class="list-group">
      <li class="list-group-item container" v-for="artist in artistList">
        <artist-list-item :artist="artist"></artist-list-item>
      </li>
    </ul>
  </div>
</template>

<script>
import artistListItem from "./artistListItem";

export default {
  name: "artistList",
  components: { artistListItem },
  props: {
    artistList: {
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
