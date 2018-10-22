var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('yawik', './public/modules/Core/yawik.js')

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .addPlugin(new CopyWebpackPlugin([
        {
            from: './node_modules/bootstrap3-dialog/dist',
            to: '../assets/bootstrap3-dialog'
        },
    ]))
    .autoProvideVariables({
        'global.$': 'jquery',
        jQuery: 'jquery',
        'global.jQuery': 'jquery',
    })
    .enableLessLoader()
;

const core = Encore.getWebpackConfig();
core.name = 'core';
core.resolve = {
    extensions: ['.js'],
    alias: {
        'jquery-ui/ui/widget': 'blueimp-file-upload/js/vendor/jquery.ui.widget.js'
    }
};

Encore.reset();
Encore
    .setOutputPath('public/modules')
    .setPublicPath('/modules')
    // enables hashed filenames (e.g. app.abc123.css)
    .addEntry('../modules/Core/locales/en','./public/modules/Core/locales/src/en.js')
    .addEntry('../modules/Core/locales/de','./public/modules/Core/locales/src/de.js')
    .addEntry('../modules/Core/locales/fr','./public/modules/Core/locales/src/fr.js')
    .addEntry('../modules/Core/locales/es','./public/modules/Core/locales/src/es.js')
    .addEntry('../modules/Core/locales/it','./public/modules/Core/locales/src/it.js')
    .enableSourceMaps(false)
;
const locales = Encore.getWebpackConfig();
locales.name = 'locales';

module.exports = [core,locales];