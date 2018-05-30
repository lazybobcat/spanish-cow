var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .addStyleEntry('css/app', [ './assets/sass/main.scss' ])
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .addEntry('js/main', [ './assets/js/main.js' ])
    .addEntry('js/modernizr', './assets/js/vendor/modernizr.min.js')
    .addEntry('js/respond', './assets/js/vendor/respond.min.js')
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
