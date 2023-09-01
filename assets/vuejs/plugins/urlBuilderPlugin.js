/**
 * URL will be passed from Symfony/twig to component (through props).
 * This way a malformed url/route will create a full error when loading page
 * instead of just hide the vue component that fail
 * Use this plugin to rebuild URL with its parameters.
 * For parameters in path, use ":parameterName" when building URL in twig
 */

export default {
    install: (Vue, options) => {
        Vue.prototype.$url = (baseUrl, parameters = {}) => {
            let builtUrl = baseUrl
            let queryParameters = []
            // replace params
            let keys = Object.keys(parameters)
            keys.forEach((parameter) => {
                if (baseUrl.includes(":" + parameter)) {
                    builtUrl = builtUrl.replace(":" + parameter, parameters[parameter])
                } else {
                    queryParameters.push(parameter + "=" + encodeURI(parameters[parameter]))
                }
            })

            // auto replace locale
            builtUrl = builtUrl.replace("{_locale}", APP_LOCALE)

            if (queryParameters.length > 0) {
                builtUrl += '?' + queryParameters.join('&')
            }

            return builtUrl
        }

    }
}
