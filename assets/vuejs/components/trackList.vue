<template>
  <div>

    <ul class="list-group">
      <li class="list-group-item container" v-for="track in trackList">
        <track-list-item :track="track"></track-list-item>
      </li>
    </ul>
  </div>
</template>

<script>
import trackListItem from "./trackListItem";

export default {
  name: "trackList",
  components: { trackListItem },
  props: {
    trackList: {
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

  .track-list-empty {
    font-style: italic;
    font-size: 0.8rem;
  }

</style>
