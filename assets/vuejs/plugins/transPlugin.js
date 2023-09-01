import MessagesEn from './../../../translations/messages+intl-icu.en.yaml'
import MessagesFr from './../../../translations/messages+intl-icu.fr.yaml'

/**
 * This plugin will be used to translate texts in Vue components.
 * Here we load yaml files from SF to keep all translations in one place.
 * As in SF, we have option to ucfirst and manage labels (including nbsp or not)
 * If translations not found, we do not display anything here instead of displaying transCode as in twig
 */

export default {
    install: (Vue, options) => {
        Vue.prototype.$trans = (key, parameters = {}, domain = null, isUcfirst = false, isLabel = false) => {
            if (domain == null) {
                domain = 'messages'
            }

            let transFile = MessagesEn
            let translation = '';

            if (APP_LOCALE === 'transCode') {
                translation = '#' + domain + '[' + key + ']'
            } else {
                switch (APP_LOCALE) {
                    case 'fr': transFile = MessagesFr; break
                }
                const pathPieces = key.split('.')
                for (var i = 0; i < pathPieces.length; i++) {
                    let prop = pathPieces[i];

                    var candidate = transFile[prop];
                    if (candidate !== undefined) {
                        transFile = candidate;
                        translation = candidate;
                    } else {
                        translation = ''
                        break
                    }
                }
            }

            // replace parameters
            for (const paramKey in parameters) {
                translation = translation.replace('{'+paramKey+'}', parameters[paramKey])
            }

            if (isUcfirst) {
                translation = translation.charAt(0).toUpperCase() + translation.slice(1);
            }
            if (isLabel) {
                let separator = ''
                switch (APP_LOCALE) {
                    case 'fr': separator = '\xa0'; break
                }
                translation += separator + ':'
            }

            return translation
        }

    }
}
