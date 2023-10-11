<template>
  <div>
    <div class="container-fluid" v-if="allowSearch">
      <div class="form-inline row">
        <input class="form-control album-search" type="search" v-model="search"
               :placeholder="this.$trans('album.list.search', {}, null, true)" aria-label="Search">
      </div>
      <hr/>
    </div>

    <ul class="list-group">
      <li class="list-group-item" v-for="album in albumList" v-if="isItemMatchSearch(album.name)">
        <list-item item-type="album" :item="album"
                   :link-route-param="{ name: 'album', params: { id: album.id, album: album }}">
          <template v-slot:tag_top>
            <div class="trackCount badge badge-pill badge-secondary">{{ album.tracksCount }}</div>
          </template>
          <template v-slot:tag_bottom>
            <div class="">{{ getDisplayTime(album.totalDuration) }}</div>
          </template>
        </list-item>
      </li>
    </ul>
  </div>
</template>

<script>
import listItem from "../listItems/listItem";
import {mapGetters} from "vuex";

export default {
  name: "albumList",
  components: { listItem },
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
  computed: {
    ...mapGetters(['getDisplayTime']),
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

  .form-control.album-search {
    width: 100%;
  }

</style>
