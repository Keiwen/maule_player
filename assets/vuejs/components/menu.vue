<template>
  <div class="row">
    <div class="col-4 text-center">
      <router-link :to="{name: 'artists'}" class="btn btn-primary btn-menu" aria-label="Navigate to artists">
        <band-icon class="band-icon" />
      </router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu btn-menu-middle" aria-label="Navigate to homepage">
        <i class="fa fa-home" />
      </router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'playlist'}" class="btn btn-primary btn-menu" aria-label="Navigate to playlist">
        <playlist-icon class="playlist-icon" />
      </router-link>
    </div>
  </div>
</template>

<script>
import {mapActions} from "vuex";
import bandIcon from "./icons/bandIcon";
import playlistIcon from "./icons/playlistIcon";

export default {
  name: "appMenu",
  components: { bandIcon, playlistIcon },
  methods: {
    ...mapActions(['resetState'])
  }
}
</script>

<style lang="scss" scoped>

svg {
  height: 35px;
  padding-top: 12px;
}
.btn-menu-middle {
  background-image: url("../../images/main_icon.png");
  background-size: 90px;
  svg {
    display:none;
  }
}

.band-icon {
  padding-top: 0;
}
.playlist-icon {
  padding-top: 0;
}

</style>
