/**
 * Gulpfile
 *
 * Rename and Minify JavaScript... and more (later).
 *
 * Install Command:
 * npm install gulp gulp-rename gulp-uglify
 */

var gulp   = require('gulp');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var requirejsOptimize = require('gulp-requirejs-optimize');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');

gulp.task('js:builder', function() {
    gulp.src('assets/js/builder/main.js')
        .pipe(sourcemaps.init())
        .pipe(requirejsOptimize(function(file) {
            return {
                name: '../lib/almond',
                optimize: 'uglify2',
                wrap: true,
                baseUrl: 'assets/js/builder/',
                include: ['main'],
                preserveLicenseComments: false
            };
        }))
        .pipe(rename('builder.js'))
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('assets/js/min/'));
});

gulp.task('js:display', function() {
    gulp.src('assets/js/front-end/main.js')
        .pipe(sourcemaps.init())
        .pipe(requirejsOptimize(function(file) {
            return {
                name: '../lib/almond',
                optimize: 'uglify2',
                wrap: true,
                baseUrl: 'assets/js/front-end/',
                include: ['main'],
                preserveLicenseComments: false
            };
        }))
        .pipe(rename('front-end.js'))
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('assets/js/min/'));
});
gulp.task('js:frontend', ['js:front-end']);
gulp.task('js:front-end', ['js:front-end']);

gulp.task('js', ['js:builder', 'js:display']);

gulp.task('css:builder', function(){
    gulp.src('assets/scss/admin/builder.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('assets/css'));
});
gulp.task('sass:builder', ['css:builder'] );

gulp.task('css:display', function(){
    gulp.src('assets/scss/front-end/display-structure.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('assets/css'));

    gulp.src('assets/scss/front-end/display-opinions.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('assets/css'));

    gulp.src('assets/scss/front-end/display-opinions-light.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('assets/css'));

    gulp.src('assets/scss/front-end/display-opinions-dark.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest('assets/css'));
});

gulp.task('sass:display', ['css:display'] );
gulp.task('css:frontend', ['css:display'] );
gulp.task('css:front-end', ['css:display'] );

gulp.task('css', ['css:builder', 'css:display'] );
gulp.task('sass', ['css'] );

// Watch Files For Changes
gulp.task('watch', function() {
    gulp.watch('assets/js/builder/**/*.js', ['js']);
    gulp.watch('assets/js/front-end/**/*.js', ['js']);
    gulp.watch('assets/scss/**/*.scss', ['sass']);
});

// Default Task
gulp.task('default', ['js', 'sass', 'watch']);

function swallowError (error) {
    //If you want details of the error in the console
    console.log(error.toString());
    this.emit('end');
}
