<template>
  <div>
    <div class="container-fluid" v-if="allowSearch">
      <div class="form-inline row">
        <input class="form-control" type="search" v-model="search"
               :placeholder="this.$trans('track.list.search', {}, null, true)" aria-label="Search">
      </div>
      <hr/>
    </div>

    <ul class="list-group">
      <li class="list-group-item container" v-for="track in trackList" v-if="isItemMatchSearch(track.name)">
        <track-list-item :track="track" />
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
    },
    allowSearch: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {
      search: ''
    }
  },
  methods: {
    isItemMatchSearch (name) {
      if (this.search === '') return true
      return name.toLowerCase().includes(this.search.toLowerCase())
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
