
import Vue from 'vue'
import store from './store'
import router from './router'
import CxltToastr from 'cxlt-vue2-toastr'
import { mapGetters, mapActions } from 'vuex'
import { SweetModal } from 'sweet-modal-vue'
import 'vue-awesome/icons'
import Icon from 'vue-awesome/components/Icon'
import vueDraggable from "vuedraggable";

import transPlugin from "./plugins/transPlugin"
import urlBuilderPlugin from "./plugins/urlBuilderPlugin"

import flashMessage from './components/flashMessage'
import appMenu from './components/menu'
import mediaPlayer from './components/mediaPlayer/mediaPlayer'
import playlistTrash from './components/listing/playlistTrash'

/************
 WARNINGS (yeah, multiple)
 webpack will build javascript and style files.
 - Import javascript with encore_entry_script_tags() method in symfony/twig
 - Import style with encore_entry_link_tags() method in symfony/twig

 In component, use style scoped to not overwhelm main styles
 In component, use classic vue delimiters {{ }} to use variables

 In Symfony template, use snake_case attribute, camelCase will not be recognized

 This file is considered as the rootApplication (not included ot further called 'application')
 It will be called on all routes for the whole HTML page
 We also have applications, that will be nested in rootApplication, and could be part of the HTML pages.
 These applications should be loaded on demand. To use it:
 - In twig, use encore entry to load application
 - In twig, define a tag with associated ID so that Vue can create vue app. On this tag, add directive v-pre:
    this avoid rootApplication to load anything inside as it will be handle by nested application
 - Inside application, avoid advanced logic beyond display logic: it should be moved to services
************/

const toastrConfig = {
    position: 'custom',
    timeOut: 5000,
    progressBar: true,
    hideDuration: 500,
    closeButton: false,
    showMethod: 'bounceInRight',
    hideMethod: 'fadeOutRight'
}

Vue.use(CxltToastr, toastrConfig)

Vue.use(transPlugin)
Vue.use(urlBuilderPlugin)

Vue.component('v-icon', Icon)
Vue.component('vue-draggable', vueDraggable)

/* eslint-disable no-new */
new Vue({
    el: '#rootApp',
    store,
    router,
    delimiters: ['${', '}$'],
    data: {
        toastBackup: {
            title: 'Title',
            icon: 'info',
            text: 'Message'
        },
        windowSize: {
            height: window.innerHeight,
            width: window.innerWidth
        }
    },
    components: { flashMessage, SweetModal, appMenu, mediaPlayer, playlistTrash },
    computed: {
        ...mapGetters(['messageBag']),
        screenWidthClass () {
            // based on bootstrap size
            // https://getbootstrap.com/docs/4.4/layout/grid/#grid-options
            if (this.windowSize.width >= 1200) return 'xl'
            if (this.windowSize.width >= 992) return 'lg'
            if (this.windowSize.width >= 768) return 'md'
            if (this.windowSize.width >= 576) return 'sm'
            return 'xs'
            // use this.$root.screenWidthClass in component
        }
    },
    watch: {
        messageBag: function (newValue, oldValue) {
            const messageManager = this.$toast
            const toastBackup = this.$refs.toastBackup
            const toastBackupData = this.toastBackup
            this.nextMessage().then(
                function (data) {
                    // on success
                    if (data.type) {
                        switch (data.type) {
                            case 'success':
                                messageManager.success(data)
                                break
                            case 'warn':
                            case 'warning':
                                messageManager.warn(data)
                                break
                            case 'error':
                            case 'danger':
                                messageManager.error(data)
                                break
                            default:
                                messageManager.info(data)
                        }

                        // get toaster DOM elements with a small timeout, waiting for generation
                        setTimeout(() => {
                            const allToast = document.getElementsByClassName("toast");
                            for (let toastMsg of allToast) {
                                // for each toast, we add a click event if we want to 'freeze' message before its timeout
                                toastMsg.addEventListener("click", (e) => {
                                    // this click event will open a sweet-modal component we prepared: just change data
                                    const toaster = e.currentTarget
                                    toastBackupData.title = toaster.querySelector('.toast-title').innerHTML
                                    toastBackupData.text = toaster.querySelector('.toast-message').innerHTML
                                    const toasterClass = toaster.getAttribute('class')
                                    toastBackupData.icon = toasterClass.replace('toast toast-', '')
                                    toastBackup.open()
                                });
                            }
                        }, 100);
                    }
                },
                function (data) {
                    // on failure
                }
            )
        }
    },
    mounted () {
        this.$nextTick(() => {
            window.addEventListener('resize', this.onResize);
        })
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResize);
    },
    methods: {
        ...mapActions(['nextMessage']),
        onResize() {
            this.windowSize = {
                height: window.innerHeight,
                width: window.innerWidth
            }
        }
    }


})
