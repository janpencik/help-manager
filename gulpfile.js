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

// --------------------------------------------------
// DEVELOPMENT BUILD
// --------------------------------------------------

// Clean
gulp.task("clean", function() {
    return del(["./admin/assets"]);
})

// Copy Reframe.js from node_modules to libs
gulp.task("copyReframe", function() {
    return (
        gulp
            .src(['./node_modules/reframe.js/dist/reframe.js', './node_modules/reframe.js/dist/reframe.min.js'])
            .pipe(gulp.dest("./admin/libs/reframe.js"))
    );
})

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

// Images
gulp.task("images", function() {
    return (
        gulp
            .src("./admin/src/img/**/*")
            .pipe(gulp.dest("./admin/assets/img"))
            .pipe(browserSync.stream())
    );
})

// Watch files
gulp.task("default", function watchFiles(done) {    
    browserSync.init({
        proxy: "https://" + process.env.URL + "/",
        host: process.env.URL,
        open: false,
        port: 8080,
        https: {
            key: process.env.SSL_KEY,
            cert: process.env.SSL_CERT
        },
    });
    gulp.watch(["./admin/src/scss/**/*.scss"], gulp.series(
        "mainStyles", 
        function cssBrowserReload(done) {
            browserSync.reload();
            done();
        }
    ));
    gulp.watch("./admin/src/js/**/*.js", gulp.series(
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
    "copyReframe",
    "mainStyles", 
    "mainScripts",
    "images"
), "default" ) )

// --------------------------------------------------
// PRODUCTION BUNDLE
// --------------------------------------------------

// Clean bundle
gulp.task("cleanBundle", function() {
    return del(["./dist"]);
})

// Admin folder
gulp.task("admin", function() {
    return (
        gulp
            .src("./admin/**/*")
            .pipe(gulp.dest("./dist/admin"))
    );
})

// Includes folder
gulp.task("includes", function() {
    return (
        gulp
            .src("./includes/**/*")
            .pipe(gulp.dest("./dist/includes"))
    );
})

// Languages folder
gulp.task("languages", function() {
    return (
        gulp
            .src("./languages/**/*")
            .pipe(gulp.dest("./dist/languages"))
    );
})

// Root files
gulp.task("rootFiles", function() {
    return (
        gulp
            .src([
                "./index.php",
                "./LICENSE.txt",
                "./README.txt",
                "./uninstall.php",
                "./help-manager.php",
                "./wpml-config.xml",
            ])
            .pipe(gulp.dest("./dist"))
    );
})

// Delete source and obsolete files
gulp.task("deleteSourceFiles", function() {
    return del([
        "./dist/admin/src",
    ]);
})

// Create bundle
gulp.task( "bundle", 
    gulp.series(
        "cleanBundle",
        gulp.parallel(
            "admin",
            "includes",
            "languages",
        ),
        gulp.parallel(
            "rootFiles",
            "deleteSourceFiles",
        )
    )
)
