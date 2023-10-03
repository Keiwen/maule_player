<template>
  <div>
    <vue-draggable v-model="trashList"
                   v-bind="{group: 'playlist'}">
      <transition-group :class="{show: displayPlaylistTrash}" tag="div" id="dropzone-trashlist">
        <div class="trash-item" v-for="(track, trackIndex) in trashList" :key="'key-'+trackIndex">
        </div>
      </transition-group>
    </vue-draggable>

  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  name: "playlistTrash",
  data () {
    return {
      trashList: []
    }
  },
  computed: {
    ...mapGetters(['displayPlaylistTrash'])
  }
}
</script>

<style lang="scss" scoped>

#dropzone-trashlist {
  background-color: var(--danger);
  opacity: 0.8;
  z-index: 10;
  position: absolute;
  bottom: -105px;
  left: 0;
  width: 100%;
  height: 105px;
  border: 5px dashed var(--dark);
  text-align: center;
  transition: bottom 0.5s ease-out;

  &.show {
    bottom: 0;
    transition: bottom 0.5s ease-out;
  }

  .trash-item {
    display: none;
  }
}

</style>
