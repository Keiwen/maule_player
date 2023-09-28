<template>
  <div class="row">
    <div class="col-4 text-center">
      <router-link :to="{name: 'artists'}" class="btn btn-primary btn-menu menu-active">
        <artist-icon class="artist-icon" />
      </router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu btn-menu-middle">
        <i class="fa fa-home" />
      </router-link>
    </div>
    <div class="col-4 text-center">
      <button class="btn btn-primary btn-menu" @click="resetState">Reset</button>
    </div>
  </div>
</template>

<script>
import {mapActions} from "vuex";
import artistIcon from "./artistIcon";

export default {
  name: "appMenu",
  components: { artistIcon },
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
  background-image: url("../../../public/img/main_icon.png");
  background-size: 90px;
  svg {
    display:none;
  }
}

.artist-icon {
  padding-top: 0;
}

</style>
