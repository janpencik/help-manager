/*
 * Gulpfile.js
 * Author: Bohemia Plugins (contact@bohemiaplugins.com)
 */

// Development modules
var dotenv          = require('dotenv').config(),
    gulp            = require("gulp"),
    sass            = require('gulp-sass')(require('sass')),
    del             = require('del'),
    postcss         = require("gulp-postcss"),
    autoprefixer    = require("autoprefixer"),
    cssnano         = require("cssnano"),
    concat          = require("gulp-concat"),
    uglify          = require("gulp-uglify");

// BrowserSync
var browserSync     = require("browser-sync").create();

// Clean
gulp.task("clean", function() {
    return del(["./admin/assets"]);
})

// External Documents CSS
gulp.task("externalDocumentsStyles", function() {
    return (
        gulp
            .src([
                // Select2
                "./node_modules/select2/dist/css/select2.min.css",
                // Magnific popup
                "./node_modules/magnific-popup/dist/magnific-popup.css",
            ])
            .pipe(postcss([cssnano()]))
            .pipe(concat('documents-libs.css'))
            .pipe(gulp.dest("./admin/assets/css"))
            .pipe(browserSync.stream())
    );
})

// External Settings CSS
// gulp.task("externalSettingsStyles", function() {
//     return (
//         gulp
//             .src([
//                 // Select2
//                 "./node_modules/select2/dist/css/select2.min.css",
//             ])
//             .pipe(postcss([cssnano()]))
//             .pipe(concat('settings-libs.css'))
//             .pipe(gulp.dest("./admin/assets/css"))
//             .pipe(browserSync.stream())
//     );
// })

// Main CSS
gulp.task("mainStyles", function() {
    return (
        gulp
            .src("./admin/src/scss/**/*.scss")
            .pipe(sass())
            .on("error", sass.logError)
            .pipe(postcss([autoprefixer(), cssnano()]))
            .pipe(gulp.dest("./admin/assets/css"))
            .pipe(browserSync.stream())
    );
})

// External Documents JS
gulp.task("externalDocumentsScripts", function() {
    return (
        gulp
            .src([
                // Reframe.js
                "./node_modules/reframe.js/dist/reframe.min.js",
                // Magnific popup
                "./node_modules/magnific-popup/dist/jquery.magnific-popup.min.js",
                // Clipboard
                "./node_modules/clipboard/dist/clipboard.min.js",
            ])
            .pipe(concat('documents-libs.js'))
            .pipe(uglify())
            .pipe(gulp.dest("./admin/assets/js"))
            .pipe(browserSync.stream())
    );
})

// External Settings JS
// gulp.task("externalSettingsScripts", function() {
//     return (
//         gulp
//             .src([
//                 // Clipboard
//                 "./node_modules/clipboard/dist/clipboard.min.js",
//             ])
//             .pipe(concat('settings-libs.js'))
//             .pipe(uglify())
//             .pipe(gulp.dest("./admin/assets/js"))
//             .pipe(browserSync.stream())
//     );
// })

// Main JS
gulp.task("mainScripts", function() {
    return (
        gulp
            .src("./admin/src/js/**/*.js")
            .pipe(uglify())
            .pipe(gulp.dest("./admin/assets/js"))
            .pipe(browserSync.stream())
    );
})

// Watch files 
gulp.task("default", function watchFiles(done) {    
    browserSync.init({
        proxy: "https://" + process.env.URL + "/",
        host: 'help.test',
        open: false,
        port: 8080,
        https: {
            key: process.env.SSL_KEY,
            cert: process.env.SSL_CERT
        },
    });
    gulp.watch(["./admin/src/scss/**/*.scss"], gulp.series(
        "externalDocumentsStyles",
        // "externalSettingsStyles",
        "mainStyles", 
        function cssBrowserReload(done) {
            browserSync.reload();
            done();
        }
    ));
    gulp.watch("./admin/src/js/**/*.js", gulp.series(
        "externalDocumentsScripts",
        // "externalSettingsScripts",
        "mainScripts", 
        function jsBrowserReload(done) {
            browserSync.reload();
            done();
        }
    ));
    gulp.watch([
        "**/*.php",
        "**/*.html"
    ]).on('change', browserSync.reload);
    done();
})

// Build assets
gulp.task( "build", gulp.series( "clean", gulp.parallel( 
    "externalDocumentsStyles", 
    // "externalSettingsStyles",
    "mainStyles", 
    "externalDocumentsScripts", 
    // "externalSettingsScripts",
    "mainScripts"
), "default" ) )
