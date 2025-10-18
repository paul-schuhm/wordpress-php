// Programme en charge de compiler, minifier et exporter les assets (CSS, JS, SVG)
// dans le thème en cours de développement

const gulp = require('gulp'),
    autoprefixer = require('autoprefixer'),
    cssnano = require('gulp-cssnano'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    plumber = require('gulp-plumber'),
    svgstore = require('gulp-svgstore'),
    postcss = require("gulp-postcss"),
    svgmin = require('gulp-svgmin'),
    path = require('path');
const sass = require('gulp-sass')(require('sass'));

// Liste des thèmes
const themes = ['mon_theme'];

const svg = function (theme) {
    return gulp.src(['src/' + theme + '/icons/*.svg'])
        .pipe(gulp.dest('web/wp-content/themes/' + theme + '/icons'))
        .pipe(svgmin(function (file) {
            var prefix = path.basename(file.relative, path.extname(file.relative));
            return {
                plugins: [{
                    cleanupIDs: {
                        prefix: prefix + '-',
                        minify: true
                    }
                }]
            }
        }))
        .pipe(rename({ prefix: 'icon-' }))
        .pipe(svgstore({
            inlineSvg: true
        }))
        .pipe(gulp.dest('web/wp-content/themes/' + theme + '/styles/svg'));
}

const styles = function (theme) {
    return gulp.src(['src/' + theme + '/scss/*.scss'])
        .pipe(sass().on('error', sass.logError))
        .pipe(postcss([autoprefixer()])) // Utilisation de PostCSS avec Autoprefixer
        .pipe(rename({ suffix: '.min' }))
        .pipe(cssnano({ zindex: false }))
        .pipe(gulp.dest('web/wp-content/themes/' + theme + '/styles'));
}

const scripts = function (theme) {
    return gulp.src(['src/' + theme + '/js/*.js'])
        .pipe(plumber())
        .pipe(concat('scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('web/wp-content/themes/' + theme + '/js'));
}

const blockscripts = function (theme) {
    return gulp.src(['src/' + theme + '/blocks/scripts/*.js', 'src/' + theme + '/blocks/scripts/**/*.js'])
        .pipe(plumber())
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest('web/wp-content/themes/' + theme + '/blocks/js'));
}

const assigntasks = function (theme) {
    gulp.task('svg-' + theme, function () {
        return svg(theme);
    });
    gulp.task('styles-' + theme, function () {
        return styles(theme);
    });
    gulp.task('scripts-' + theme, function () {
        return scripts(theme);
    });
    gulp.task('blockscripts-' + theme, function () {
        return blockscripts(theme);
    });
    gulp.task('watch-' + theme, function () {
        gulp.watch('src/' + theme + '/icons/*.svg', gulp.parallel('svg-' + theme));
        gulp.watch('src/' + theme + '/scss/*.scss', gulp.parallel('styles-' + theme));
        gulp.watch('src/' + theme + '/js/*.js', gulp.parallel('scripts-' + theme));
        gulp.watch('src/' + theme + '/blocks/scripts/**/*.js', gulp.parallel('blockscripts-' + theme));
    });
    tasks.push('watch-' + theme)
    tasks.push('styles-' + theme)
    tasks.push('scripts-' + theme)
    tasks.push('svg-' + theme)
    tasks.push('blockscripts-' + theme)
}


const tasks = [];

themes.forEach(function (theme) {
    assigntasks(theme)
});

gulp.task('default', gulp.parallel(tasks));