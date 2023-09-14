import { ref, isRef, unref, watchEffect } from 'vue'
/**
 */

export function useRemoteCall(url) {
    const callData = ref(null)
    const callError = ref(null)

    async function doFetch() {
        // reset state before fetching..
        callData.value = null
        callError.value = null

        // resolve the url value synchronously, so it's tracked as a
        // dependency by watchEffect()
        const urlValue = unref(url)

        try {
            // unref() will return the ref value if it's a ref
            // otherwise the value will be returned as-is
            const response = await fetch(urlValue)
            callData.value = await response.json()
        } catch (e) {
            callError.value = e
        }
    }

    if (isRef(url)) {
        // setup reactive re-fetch if input URL is a ref
        watchEffect(doFetch)
    } else {
        // otherwise, just fetch once
        doFetch()
    }

    return {callData, callError}
}
