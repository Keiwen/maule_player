<template>
  <div>
    <div class="container-fluid" v-if="allowSearch">
      <div class="form-inline row">
        <input class="form-control artist-search" type="search" v-model="search"
               :placeholder="this.$trans('artist.list.search', {}, null, true)" aria-label="Search">
      </div>
      <hr/>
    </div>

    <ul class="list-group">
      <li class="list-group-item" v-for="artist in artistList" v-if="isItemMatchSearch(artist.name)">
        <list-item item-type="artist" :item="artist">
          <template v-slot:tag_top>
            <div class="trackCount badge badge-pill badge-secondary">{{ artist.tracksCount }}</div>
          </template>
        </list-item>
      </li>
    </ul>
  </div>
</template>

<script>
import listItem from "./listItem";

export default {
  name: "artistList",
  components: { listItem },
  props: {
    artistList: {
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

  .form-control.artist-search {
    width: 100%;
  }

</style>
