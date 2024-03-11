const path = require('path');
const mix = require('laravel-mix');

require('laravel-mix-merge-manifest');
require('laravel-mix-clean');

const publicPath = path.join(
    'public',
    'themes',
    'salutarium',
    'assets'
);

console.log(`Assets will be published in: ${publicPath}`);

const assetsPath = path.join(__dirname, 'resources', 'assets');
const jsPath = path.join(assetsPath, 'js');
const imagesPath = path.join(assetsPath, 'img');
const vendorParh = path.join(assetsPath, 'vendor');


mix.setPublicPath(publicPath)
    .js(path.join(jsPath, 'app-core.js'), 'js/salutarium-core.js')
    .js(path.join(jsPath, 'app.js'), 'js/salutarium.js')
    .vue()
    .alias({
        '@components': path.join(jsPath, 'UI', 'components'),
    })
    .extract({
        to: `/js/components.js`,
        test(mod) {
            return /(component|style|loader|node)/.test(mod.nameForCondition());
        },
    })
    .copy(imagesPath, path.join(publicPath, 'img'))
    .sass(
        path.join(assetsPath, 'sass', 'app.scss'),
        path.join(__dirname, publicPath, 'css', 'salutarium.css'),
        {
            sassOptions: {
                includePaths: [
                    'node_modules/bootstrap-sass/assets/stylesheets/',
                ],
            },
        }
    ).combine([
            path.join(vendorParh, 'custom-select', 'custom-select.min.js'),
            path.join(vendorParh, 'imask', 'imask.js'),
            path.join(vendorParh, 'moment', 'moment.js'),
            path.join(vendorParh, 'selectize', 'selectize.js'),
            path.join(vendorParh, 'swiper', 'swiper.min.js'),
            path.join(vendorParh, 'tavo-calendar', 'tavo-calendar.js'),
            path.join(jsPath, 'bundle.js')
        ],
        path.join(publicPath, 'js/salutarium-common.js')
    )
    .combine([
            path.join(assetsPath, 'css', 'bundle.min.css'),
            path.join(vendorParh, 'custom-select', 'custom-select.css'),
            path.join(vendorParh, 'selectize', 'selectize.css'),
            path.join(vendorParh, 'swiper', 'swiper.min.css'),
            path.join(vendorParh, 'tavo-calendar', 'tavo-calendar.css'),
        ],
        path.join(__dirname, publicPath, 'css', 'salutarium.css'))
    .clean({
        cleanOnceBeforeBuildPatterns: [
            'js/**/*',
            'css/salutarium.css',
            'mix-manifest.json',
        ],
    })

    .options({
        processCssUrls: false,
        clearConsole: mix.inProduction(),
    })

    .disableNotifications()
    .mergeManifest();

if (mix.inProduction()) {
    mix.version();
}
