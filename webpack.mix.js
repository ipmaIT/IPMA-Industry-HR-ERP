// const mix = require('laravel-mix');
let mix = require('laravel-mix');
const path = require('path')

mix.webpackConfig({
//	plugins: [
//	],
//	resolve: {
//	},
	stats: {
		children: true
	}
});

mix.js('resources/js/app.js', 'public/js/app.js')
	.postCss('resources/css/app.css', 'public/css/app.css', [
		require('postcss-custom-properties'),
		// require('@tailwindcss/postcss'),
	])
	// .sass('resources/scss/app.scss', 'public/css/app.css')
	// .scripts([
	// 	'node_modules/chartjs/chart.js',
	// 	'node_modules/fullcalendar/index.global.js'
	// ], 'public/js/app.js')
	// .styles([
	// 	'node_modules/chartjs/chart.css',
	// 	'node_modules/fullcalendar/index.global.css'
	// ], 'public/js/app.css')
	// .combine([
	// 	'public/css/app.css',
	// 	'./node_modules/select2-theme-bootstrap5/dist/select2-bootstrap.css',
	// ], 'public/css/app.css')
	// .setPublicPath('public/)
	// .autoload({
	// 	jquery: ['$', 'window.jQuery']
	// })
	.copyDirectory('node_modules/fullcalendar', 'public/js/fullcalendar')
	.copyDirectory('node_modules/chart.js', 'public/js/chart.js')
	.copyDirectory('node_modules/jquery-chained', 'public/js/jquery-chained')
	.sourceMaps();
