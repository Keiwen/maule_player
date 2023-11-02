<template>
  <div>
    <h1>{{ this.$trans('settings.title', {}, null, true) }}</h1>

    <h2>{{ this.$trans('settings.locale', {}, null, true) }}</h2>
    <div>
      <a class="btn btn-dark mr-2" :href="getLocaleUrl(locale)" v-for="locale in locales"
         :disabled="locale === currentLocale" :class="{'disabled': locale === currentLocale}">
        <img :src="'/build/images/flags/'+locale+'.png'" class="mr-2 img-icon-locale" :alt="'flag-' + locale"/>
        {{ getLocaleName(locale) }}
      </a>
    </div>

    <h2>{{ this.$trans('settings.about', {}, null, true) }}</h2>
    <p>{{ this.$trans('settings.version', {}, null, true, true) }} {{ currentVersion }}</p>
    <button class="btn btn-warning" @click="resetState">{{ this.$trans('settings.reset', {}, null, true) }}</button>

  </div>
</template>

<script>
import {mapActions} from "vuex";

export default {
  name: "settingsPage",
  data () {
    return {
      locales: [],
      currentLocale: '',
      currentVersion: ''
    }
  },
  mounted () {
    this.locales = Object.keys(URL_LOCALE)
    this.currentLocale = APP_LOCALE
    this.currentVersion = APP_VERSION
  },
  methods: {
    ...mapActions(['resetState']),
    getLocaleName (locale) {
      if (LOCALE_NAMES[locale] === undefined) return ''
      return LOCALE_NAMES[locale]
    },
    getLocaleUrl (locale) {
      if (URL_LOCALE[locale] === undefined) return '#'
      return URL_LOCALE[locale] + '#' + this.$route.fullPath
    }
  }
}
</script>

<style lang="scss" scoped>

.img-icon-locale {
  height: 30px;
}

</style>
