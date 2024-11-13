const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .styles([
       'node_modules/bootstrap/dist/css/bootstrap.css',
       'resources/css/custom.css',
   ], 'public/css/all.css')
   .scripts([
       'node_modules/jquery/dist/jquery.js',
       'node_modules/bootstrap/dist/js/bootstrap.bundle.js',
   ], 'public/js/all.js');
