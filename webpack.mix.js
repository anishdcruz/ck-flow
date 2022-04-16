let mix = require('laravel-mix');

const WebpackShellPlugin = require('webpack-shell-plugin');
mix.disableNotifications();

mix.webpackConfig({
   resolve: {
       alias: {
           "@js": path.resolve(
               __dirname,
               "resources/assets/js"
           )
       }
   }
});

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .webpackConfig({
       plugins:
       [
           new WebpackShellPlugin({onBuildStart:['php artisan lang:js'], onBuildEnd:[]})
       ]
   })
   .version();
