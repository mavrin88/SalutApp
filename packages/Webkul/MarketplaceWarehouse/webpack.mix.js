const mix = require("laravel-mix");

if (mix == 'undefined') {
    const { mix } = require("laravel-mix");
}

require("laravel-mix-merge-manifest");

if (mix.inProduction()) {
    var publicPath = 'publishable/assets';
} else {
    var publicPath = '../../../public/vendor/webkul/marketplaceWarehouse/assets';
}

mix.setPublicPath(publicPath).mergeManifest();
mix.disableNotifications();

mix.copyDirectory(__dirname + '/src/Resources/assets/images', publicPath + "/images")
    .sass(__dirname + '/src/Resources/assets/sass/app.scss', 'css/mpwarehouse.css')
    .options({
        processCssUrls: false
    });


if (! mix.inProduction()) {
    mix.sourceMaps();
}

if (mix.inProduction()) {
    mix.version();
}