<template>
  <div>
    <div class="container-fluid" v-if="allowSearch">
      <div class="form-inline row">
        <input class="form-control" type="search" v-model="search"
               :placeholder="this.$trans('album.list.search', {}, null, true)" aria-label="Search">
      </div>
      <hr/>
    </div>

    <ul class="list-group">
      <li class="list-group-item container" v-for="album in albumList" v-if="isItemMatchSearch(album.name)">
        <album-list-item :album="album" />
      </li>
    </ul>
  </div>
</template>

<script>
import albumListItem from "../albumListItem";

export default {
  name: "albumList",
  components: { albumListItem },
  props: {
    albumList: {
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

</style>
