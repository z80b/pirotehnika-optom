var gulp = require('gulp');

gulp.task('styles', function() {
    var stylus       = require('gulp-stylus'),
        autoprefixer = require('gulp-autoprefixer'),
        cleanCSS     = require('gulp-clean-css'),
        rename       = require('gulp-rename'),
        axis         = require('axis');

    return gulp.src('./src/css/admin/revisions.styl')
        .pipe(stylus({
            'include css': true,
            'compress': false,
            'use': axis(),
            'rawDefine': { 'inline-image': stylus.stylus.url({
                paths: ['./src/css/imgs']
            }) }
        }))
        .pipe(autoprefixer(['> 0%']))
        .pipe(rename('revisions.css'))
        .pipe(gulp.dest('./css/admin'));
});

gulp.task('scripts', function() {
    var browserify = require('browserify'),
        babelify   = require('babelify'),
        uglify     = require('gulp-uglify'),
        buffer     = require('vinyl-buffer'),
        source     = require('vinyl-source-stream');

    return browserify({
            entries: [
                './src/js/admin/revisions.js'
            ],
            debug: false,
            paths: [
                //'./src/js/vendor',
                './node_modules'
            ]
        })
        .transform(babelify, {
            presets: ['es2015', 'stage-0']
        })
        .bundle()
        .on('error', function(err){
            console.log('[browserify error]');
            console.log(err.message);
        })
        .pipe(source('revisions.js'))
        .pipe(buffer())
        //.pipe(uglify())
        .pipe(gulp.dest('./js/admin'));
});

gulp.task('theme', ['theme_styles'], function() {
    gulp.watch('./themes/ogonek/stylus/**/*.styl', ['theme_styles'])
});

gulp.task('theme_styles', function() {
    var stylus       = require('gulp-stylus'),
        autoprefixer = require('gulp-autoprefixer'),
        cleanCSS     = require('gulp-clean-css'),
        rename       = require('gulp-rename'),
        ignore       = require('gulp-ignore'),
        bootstrap    = require('bootstrap-styl'),
        axis         = require('axis'),
        __basedir      = __dirname;

    return gulp.src([
        './themes/ogonek/stylus/**/*.styl',
        '!./themes/ogonek/stylus/blocks/*.styl'])
        .pipe(stylus({
            'include css': true,
            'import': [
                __basedir +'/themes/ogonek/stylus/variables.styl'
            ],
            'compress': false,
            'use': [axis(), bootstrap()],
            'rawDefine': { 'inline-image': stylus.stylus.url({
                paths: ['./src/css/imgs']
            }) }
        }))
        .pipe(autoprefixer(['> 0%']))
        .pipe(gulp.dest('./themes/ogonek/css/')); 
})

gulp.task('sass', function() {
    var sass = require('gulp-sass');
    var compass = require('compass-importer'); 
    var theme_variables = require('./themes/ogonek3/sass/_theme_variables.scss');
    var __basedir      = __dirname;
    return gulp.src('themes/ogonek3/sass/**/*.scss')
        .pipe(sass({
            includePaths: [
                __basedir + '/themes/ogonek3/sass/',
                __basedir + '/ themes/ogonek3/sass/bootstrap_lib/'
            ],
            importer: compass
        }).on('error', sass.logError))
        .pipe(gulp.dest('./themes/ogonek3/css/'));
});

gulp.task('build', ['styles', 'scripts']);

gulp.task('deploy', function() {
    var git =     require('gulp-git'),
        ftpConf = require('./ftp.json'),
        ftp =     require( 'vinyl-ftp' );
  
    git.exec({args : 'diff --name-only HEAD^..HEAD'}, function (err, stdout) {
        if (err) throw err;
        ftpConf.log = console.log;
        var conn = ftp.create(ftpConf);
        var files = stdout.split("/n");
        return gulp.src( files, { base: '.', buffer: true } )
            .pipe(conn.dest('/pirotehnika-optom.ru/public_html'));

    });
});

gulp.task('default', ['styles', 'scripts'], function() {
    gulp.watch('./src/css/**/*.styl', ['styles']);
    gulp.watch('./src/js/**/*.js', ['scripts']);
});