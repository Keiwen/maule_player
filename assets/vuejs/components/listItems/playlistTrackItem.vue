<template>
  <div class="row" :class="{'item-active': isCurrent}">

    <div class="col-2">
      <router-link :to="{ name: 'track', params: { id: track.id, track: track }}" class="btn btn-primary track-link">
        <track-icon />
      </router-link>
    </div>

    <div class="col-8 row ddHandle">
      <div class="col-12">
        <span class="track-title">{{ getLimitedTitle(track.name, 18) }}</span>
      </div>

      <div class="col-12">
        #{{ trackIndex + 1 }}
        <span class="track-artist">{{ getLimitedTitle(track.artist.name, 17) }}</span>
      </div>
    </div>

    <div class="col-2 item-icon">
      <button class="btn btn-light btn-pause" disabled>
        <i class="fa fa-pause" />
      </button>
      <button class="btn btn-secondary btn-play" @click="selectTrack">
        <i class="fa fa-play" />
      </button>
    </div>

  </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import trackIcon from "../icons/trackIcon";
import TrackIcon from "../icons/trackIcon";

export default {
  name: "playlistTrackItem",
  components: {TrackIcon},
  component: { trackIcon },
  props: {
    track: {
      type: Object,
      required: true
    },
    trackIndex: {
      type: Number,
      required: true
    }
  },
  computed: {
    ...mapGetters(['getLimitedTitle', 'currentTrackIndex']),
    isCurrent () {
      return this.trackIndex === this.currentTrackIndex
    }
  },
  methods: {
    ...mapActions(['playTrackInPlaylist']),
    selectTrack() {
      this.playTrackInPlaylist(this.trackIndex)
    }
  }
}
</script>

<style lang="scss" scoped>

.track-title {
  font-weight: bold;
}

.btn-play,.btn-pause {
  width: 50px;
  height: 50px;
  svg {
    height: 100%;
  }
}

.btn-pause {
  display: none;
}

.item-active {
  .btn-play {
    display: none;
  }
  .btn-pause {
    display: block;
  }
}

.track-link {
  width: 50px;
  height: 50px;
  svg {
    height: 100%;
    margin-left: -6px;
  }
}

.track-order {
  margin-top: -5px;
}

</style>
