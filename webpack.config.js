const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    .copyFiles({
        from: './public/img',
        to: 'images/[path][name].[ext]',
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('vue', './assets/vuejs/rootApplication.js')

    .addStyleEntry('default', './assets/styles/theme_default.scss')
    .addStyleEntry('alt', './assets/styles/theme_alt.scss')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // enable sass
    .enableSassLoader()

    // enable VueJS
    .enableVueLoader()

    // add custom loader
    .addLoader({ test: /\.ya?ml$/, loader: 'yaml-loader' })

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // Below you can define the base of manifest.json file
    .configureManifestPlugin( (options) => {
        options.seed = {
            "short_name": "Maule",
            "name": "Maule player",
            "start_url": "/",
            "icons": [{
                    "src": "/build/images/512.png",
                    "sizes": "512x512",
                    "type": "image/png"
            },
                {
                    "src": "/build/images/256.png",
                    "sizes": "256x256",
                    "type": "image/png"
                },
                {
                    "src": "/build/images/192.png",
                    "sizes": "192x192",
                    "type": "image/png"
                }
            ],
            "background_color": "#FDAD72",
            "theme_color": "#66275C",
            "display": "standalone",
            "orientation": "portrait",
        }
    })
;

module.exports = Encore.getWebpackConfig();
