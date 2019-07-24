var gulp = require('gulp');
var gulpLoadPlugins = require('gulp-load-plugins');
var browserSync = require('browser-sync').create();
var del = require('del');
var fs = require('fs');
var git = require('git-rev-sync');
var tryFn = require('nice-try');
var saveLicense = require('uglify-save-license');

var $ = gulpLoadPlugins();
var reload = browserSync.reload;

gulp.task('clean', del.bind(null, ['resources/aria-ng/.tmp', 'public/aria-ng', 'resources/views/ng.blade.php']));

gulp.task('lint', function () {
    return gulp.src([
        'resources/aria-ng/src/scripts/**/*.js'
    ]).pipe(reload({stream: true, once: true}))
        .pipe($.eslint.format())
        .pipe($.if(!browserSync.active, $.eslint.failAfterError()))
        .pipe(gulp.dest('resources/aria-ng/src/scripts'));
});

gulp.task('prepare-fonts', function () {
    return gulp.src([
        'node_modules/font-awesome/fonts/fontawesome-webfont.*'
    ]).pipe(gulp.dest('resources/aria-ng/.tmp/fonts'));
});

gulp.task('process-fonts', ['prepare-fonts'], function () {
    return gulp.src([
        'resources/aria-ng/.tmp/fonts/**/*'
    ]).pipe(gulp.dest('public/aria-ng/fonts'));
});

gulp.task('prepare-langs', function () {
    return gulp.src([
        'resources/aria-ng/src/langs/**/*'
    ]).pipe(gulp.dest('resources/aria-ng/.tmp/langs'));
});

gulp.task('process-langs', ['prepare-langs'], function () {
    return gulp.src([
        'resources/aria-ng/.tmp/langs/**/*'
    ]).pipe(gulp.dest('public/aria-ng/langs'));
});

gulp.task('prepare-styles', function () {
    return gulp.src([
        'resources/aria-ng/src/styles/**/*.css'
    ]).pipe($.autoprefixer({browsers: ['> 1%', 'last 2 versions', 'Firefox ESR']}))
        .pipe(gulp.dest('resources/aria-ng/.tmp/styles'))
        .pipe(reload({stream: true}));
});

gulp.task('prepare-scripts', function () {
    return gulp.src([
        'resources/aria-ng/src/scripts/**/*.js'
    ]).pipe($.plumber())
        .pipe($.injectVersion({replace: '${ARIANG_VERSION}'}))
        .pipe($.replace(/\${ARIANG_BUILD_COMMIT}/g, tryFn(git.short) || 'Local'))
        .pipe(gulp.dest('resources/aria-ng/.tmp/scripts'))
        .pipe(reload({stream: true}));
});

gulp.task('prepare-views', function () {
    return gulp.src([
        'resources/aria-ng/src/views/**/*.html'
    ]).pipe($.htmlmin({collapseWhitespace: true}))
        .pipe($.angularTemplatecache({module: 'ariaNg', filename: 'views/templates.js', root: 'views/'}))
        .pipe(gulp.dest('resources/aria-ng/.tmp/scripts'));
});

gulp.task('prepare-html', ['prepare-styles', 'prepare-scripts', 'prepare-views'], function () {
    return gulp.src([
        'resources/aria-ng/src/*.html'
    ]).pipe($.useref({searchPath: ['resources/aria-ng/.tmp', 'resources/aria-ng/src', 'resources/aria-ng/', '.']}))
        .pipe($.if('js/*.js', $.replace(/\/\/# sourceMappingURL=.*/g, '')))
        .pipe($.if('css/*.css', $.replace(/\/\*# sourceMappingURL=.* \*\/$/g, '')))
        .pipe($.if(['js/moment-with-locales-*.min.js', 'js/plugins.min.js', 'js/aria-ng.min.js'], $.uglify({output: {comments: saveLicense}})))
        .pipe($.if(['css/plugins.min.css', 'css/aria-ng.min.css'], $.cssnano({safe: true, autoprefixer: false})))
        .pipe($.if(['js/plugins.min.js', 'js/aria-ng.min.js', 'css/plugins.min.css', 'css/aria-ng.min.css'], $.rev()))
        .pipe($.if('*.html', $.htmlmin({collapseWhitespace: true})))
        .pipe($.revReplace())
        .pipe(gulp.dest('resources/aria-ng/.tmp'));
});

gulp.task('process-html', ['prepare-html'], function () {
    return gulp.src([
        'resources/aria-ng/.tmp/*.html'
    ]).pipe($.replace(/<!-- AriaNg-Bundle:\w+ -->/, ''))
        .pipe($.replace(
            /\{\{(([a-zA-Z ]+)|'([a-zA-z ]+)' \| translate|\('([a-zA-Z ]+)' \| translate\))\}\}/g, 
            function (match, p1, p2, p3, p4) {
                var matchStr = '';
                var i = 0;
                matchArray = [p2, p3, p4];
                for (i = 0; i <matchArray.length; i++) {
                    if (matchArray[i] === undefined) {
                        continue;
                    }
                    matchArray[i] = matchArray[i].toString().replace(/ /g, '_');
                    matchStr = matchArray[i].toLowerCase();
                    break;
                }
                
                return '{{ __(\'aria.' + matchStr + '\') }}';
            }
        ))
        .pipe($.rename('ng.blade.php'))
        .pipe($.replace(/(css|js)\/([A-Za-z0-9\-\.]+)\.(css|js)/g, 'aria-ng/$1/$2.$3'))
        .pipe(gulp.dest('resources/views/'));
});

gulp.task('process-assets', ['process-html'], function () {
    return gulp.src([
        'resources/aria-ng/.tmp/css/**/*',
        'resources/aria-ng/.tmp/js/**/*'
    ],{ base: 'resources/aria-ng/.tmp' })
        .pipe(gulp.dest('public/aria-ng'));
});

gulp.task('process-assets-bundle', ['prepare-fonts', 'prepare-langs', 'prepare-html'], function () {
    return gulp.src('resources/aria-ng/.tmp/index.html')
        .pipe($.replace(/<link rel="stylesheet" href="(css\/[a-zA-Z0-9\-_.]+\.css)">/g, function(match, fileName) {
            var content = fs.readFileSync('resources/aria-ng/.tmp/' + fileName, 'utf8');
            return '<style type="text/css">' + content + '</style>';
        }))
        .pipe($.replace(/<script src="(js\/[a-zA-Z0-9\-_.]+\.js)"><\/script>/g, function(match, fileName) {
            var content = fs.readFileSync('resources/aria-ng/.tmp/' + fileName, 'utf8');
            return '<script type="application/javascript">' + content + '</script>';
        }))
        .pipe($.replace(/url\(\.\.\/(fonts\/[a-zA-Z0-9\-]+\.woff)(\?[a-zA-Z0-9\-_=.]+)?\)/g, function(match, fileName) {
            if (!fs.existsSync('resources/aria-ng/.tmp/' + fileName)) {
                return match;
            }

            var contentBuffer = fs.readFileSync('resources/aria-ng/.tmp/' + fileName);
            var contentBase64 = contentBuffer.toString('base64');
            return 'url(data:application/x-font-woff;base64,' + contentBase64 + ')';
        }))
        .pipe($.replace('<!-- AriaNg-Bundle:languages -->', function() {
            var langDir = 'resources/aria-ng/.tmp/langs/';
            var result = '';
            var fileNames = fs.readdirSync(langDir, 'utf8');

            if (fileNames.length > 0) {
                result = '<script type="application/javascript">' +
                    'angular.module("ariaNg").config(["ariaNgAssetsCacheServiceProvider",function(e){';

                for (var i = 0; i < fileNames.length; i++) {
                    var fileName = fileNames[i];
                    var content = fs.readFileSync(langDir + fileName, 'utf8');

                    var lastPointIndex = fileName.lastIndexOf('.');
                    var languageName = fileName.substr(0, lastPointIndex);

                    content = content.replace(/\\/g, '\\\\');
                    content = content.replace(/\r/g, '');
                    content = content.replace(/\n/g, '\\n');
                    content = content.replace(/"/g, '\\"');
                    result += 'e.setLanguageAsset(\'' + languageName + '\',"' + content + '");';
                }

                result += '}]);</script>';
            }

            return result;
        }))
        .pipe($.replace(/<[a-z]+( [a-z\-]+="[a-zA-Z0-9\- ]+")* [a-z\-]+="((favicon.ico)|(favicon.png)|(tileicon.png)|(touchicon.png))"\/?>/g, ''))
        .pipe(gulp.dest('public/aria-ng'));
});

gulp.task('process-manifest', function () {
    return gulp.src([
        'public/aria-ng/css/**',
        'public/aria-ng/js/**',
        'public/aria-ng/fonts/fontawesome-webfont.woff2',
        'public/aria-ng/*.html',
        'public/aria-ng/*.ico',
        'public/aria-ng/*.png'
    ], {base: 'public/aria-ng/'})
        .pipe($.manifest({
            hash: true,
            preferOnline: true,
            network: ['*'],
            filename: 'index.manifest',
            exclude: 'index.manifest'
        }))
        .pipe(gulp.dest('public/aria-ng'));
});

gulp.task('process-full-extras', function () {
    return gulp.src([
        // 'LICENSE',
        'resources/aria-ng/src/*.*',
        '!resources/aria-ng/src/*.html'
    ], {
        dot: true
    }).pipe(gulp.dest('public/aria-ng'));
});

// gulp.task('process-tiny-extras', function () {
//     return gulp.src([
//         'LICENSE'
//     ]).pipe(gulp.dest('public/aria-ng'));
// });

gulp.task('info', function () {
    return gulp.src([
        'public/aria-ng/**/*'
    ]).pipe($.size({title: 'build', gzip: true}));
});

gulp.task('build', $.sequence('lint', 'process-fonts', 'process-langs', 'process-assets', 'process-manifest', 'process-full-extras', 'info'));

gulp.task('build-bundle', $.sequence('lint', 'process-assets-bundle', 'info'));
// gulp.task('build-bundle', $.sequence('lint', 'process-assets-bundle', 'process-tiny-extras', 'info'));

gulp.task('serve', ['prepare-styles', 'prepare-scripts', 'prepare-fonts'], function () {
    browserSync.init({
        notify: true,
        port: 3030,
        online: true,
        proxy: "http://ariang.test",
        files: [
            'resources/aria-ng/src/*.html',
            'resources/aria-ng/src/*.ico',
            'resources/aria-ng/src/*.png',
            'resources/aria-ng/src/langs/*.txt',
            'resources/aria-ng/src/views/*.html',
            'resources/aria-ng/src/imgs/**/*',
            'resources/aria-ng/.tmp/fonts/**/*'
        ],
        // server: {
        //     baseDir: ['resource/aria-ng/.tmp', 'resource/aria-ng/src'],
        //     routes: {
        //         '/node_modules': 'node_modules'
        //     }
        // }
    });

    gulp.watch([
        'resources/aria-ng/src/*.html',
        'resources/aria-ng/src/*.ico',
        'resources/aria-ng/src/*.png',
        'resources/aria-ng/src/langs/*.txt',
        'resources/aria-ng/src/views/*.html',
        'resources/aria-ng/src/imgs/**/*',
        'resources/aria-ng/.tmp/fonts/**/*'
    ]).on('change', reload);

    gulp.watch('resources/aria-ng/src/styles/**/*.css', ['prepare-styles']);
    gulp.watch('resources/aria-ng/src/scripts/**/*.js', ['prepare-scripts']);
    gulp.watch('resources/aria-ng/src/fonts/**/*', ['prepare-fonts']);
});

gulp.task('serve:dist', function () {
    browserSync({
        notify: false,
        port: 9000,
        server: {
            baseDir: ['dist']
        }
    });
});

gulp.task('default', ['clean'], function () {
    gulp.start('build');
});
