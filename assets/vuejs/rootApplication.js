
import Vue from 'vue'
import store from './store'
import router from './router'
import 'cxlt-vue2-toastr/dist/css/cxlt-vue2-toastr.css'
import CxltToastr from 'cxlt-vue2-toastr'
import { EnhancedCheck, EnhancedToggle } from 'vue-enhanced-check'
import flashMessage from './components/flashMessage'
import { mapGetters, mapActions } from 'vuex'
import transPlugin from "./plugins/transPlugin"
import urlBuilderPlugin from "./plugins/urlBuilderPlugin"
import { SweetModal } from 'sweet-modal-vue'
import Icon from 'vue-awesome/components/Icon'
import 'vue-awesome/icons'

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
    position: 'bottom right',
    timeOut: 5000,
    progressBar: true,
    hideDuration: 500,
    closeButton: false,
    showMethod: 'bounceInRight',
    hideMethod: 'rotateOutDownRight'
}

Vue.use(CxltToastr, toastrConfig)

Vue.use(transPlugin)
Vue.use(urlBuilderPlugin)

Vue.component('icon', Icon)

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
        }
    },
    components: { flashMessage, EnhancedCheck, EnhancedToggle, SweetModal },
    computed: {
        ...mapGetters(['messageBag'])
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
    methods: {
        ...mapActions(['nextMessage'])
    }


})

console.log('vue loaded');
