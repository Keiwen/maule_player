<template>
  <div class="row">

    <div class="container-fluid row">
      <div class="col-12">
        <span class="track-title">{{ getLimitedTitle(track.name, 26) }}</span>
      </div>
    </div>

    <div class="container-fluid row">

      <div class="col-2">
        <router-link :to="{ name: 'track', params: { id: track.id, track: track }}" class="btn btn-primary track-link">
          <track-icon />
        </router-link>
      </div>

      <div class="col-8">
        <div class="row">
          <div class="col-12">
            <span class="track-artist">{{ getLimitedTitle(track.artist.name, 18) }}</span>
            <span class="track-duration">{{ trackDuration }}</span>
          </div>

          <div class="col-2">
            <span class="track-number">#{{ track.trackNumber }}</span>
          </div>
          <div class="col-10">
            <span class="track-album">{{ getLimitedTitle(track.album.name, 13) }}</span>
            <span class="track-year">{{ track.year }}</span>
          </div>
        </div>
      </div>

      <div class="col-2">
        <button class="btn btn-secondary btn-playlist" @click="selectTrack">
          <i class="fa fa-folder-plus" />
        </button>
      </div>


    </div>
  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import trackIcon from "../icons/trackIcon";

export default {
  name: "trackListItem",
  components: { trackIcon },
  props: {
    track: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle', 'getDisplayTime']),
    trackDuration () {
      return this.getDisplayTime(this.track.duration)
    }
  },
  methods: {
    ...mapActions(['addTracksInPlaylist']),
    selectTrack() {
      this.addTracksInPlaylist([this.track])
    }
  }
}
</script>

<style lang="scss" scoped>

.track-title {
  font-size: 1.2rem;
  font-weight: bold;
}
.track-artist {
  font-weight: bold;
}

.btn-playlist {
  width: 40px;
  height: 40px;
  svg {
    height: 100%;
    margin-left: -6px;
  }
}

.track-duration {
  position: absolute;
  right: 0;
}

.track-year {
  position: absolute;
  right: 0;
}

.track-link {
  width: 40px;
  height: 40px;
  svg {
    height: 100%;
    margin-left: -6px;
  }
}


</style>
