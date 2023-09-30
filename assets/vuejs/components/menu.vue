<template>
  <div class="row">
    <div class="col-4 text-center">
      <router-link :to="{name: 'artists'}" class="btn btn-primary btn-menu">
        <band-icon class="band-icon" />
      </router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'main'}" class="btn btn-primary btn-menu btn-menu-middle">
        <i class="fa fa-home" />
      </router-link>
    </div>
    <div class="col-4 text-center">
      <router-link :to="{name: 'settings'}" class="btn btn-primary btn-menu">
        <i class="fa fa-gears" />
      </router-link>
    </div>
  </div>
</template>

<script>
import {mapActions} from "vuex";
import bandIcon from "./bandIcon";

export default {
  name: "appMenu",
  components: { bandIcon },
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

.band-icon {
  padding-top: 0;
}

</style>
