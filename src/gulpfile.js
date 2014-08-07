var fs = require('fs'),
    https = require('https'),
    exec = require('child_process').exec,
    eventStream = require('event-stream'),
    runSequence = require('run-sequence'),
    gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    phpcs = require('gulp-phpcs'),
    order = require('gulp-order'),
    handlebars = require('gulp-handlebars'),
    defineModule = require('gulp-define-module'),
    header = require('gulp-header'),
    zip = require('gulp-zip'),
    tar = require('gulp-tar'),
    gzip = require('gulp-gzip'),
    chmod = require('gulp-chmod');

// Set global configs.
var config = {
    mibewPath: 'mibew',
    phpVendorPath: 'mibew/vendor',
    pluginsPath: 'mibew/plugins',
    jsPath: 'mibew/js',
    chatStylesPath: 'mibew/styles/dialogs',
    pageStylesPath: 'mibew/styles/pages',
    compiledTemplatesHeader: fs.readFileSync('tools/compiled_templates_header.txt'),
    getComposerUrl: 'https://getcomposer.org/installer',
    package: require('./package.json')
}


// Checks all PHP files with PHP Code Sniffer.
gulp.task('phpcs', ['composer-install-dev'], function() {
    return gulp.src([
        config.mibewPath + '/**/*.php',
        '!' + config.phpVendorPath + '/**/*.*',
        '!' + config.pluginsPath + '/**/*.*'
    ])
    .pipe(phpcs({
        bin: config.phpVendorPath + '/bin/phpcs',
        standard: 'PSR2',
        warningSeverity: 0
    }));
});

// Get and install PHP Composer
gulp.task('get-composer', function(callback) {
    // Check if Composer already in place
    if (fs.existsSync('./composer.phar')) {
        callback(null);

        return;
    }

    // Get installer from the internet
    https.get(config.getComposerUrl, function(response) {
        // Run PHP to install Composer
        var php = exec('php', function(error, stdout, stderr) {
            callback(error ? stderr : null);
        });
        // Pass installer code to PHP via STDIN
        response.pipe(php.stdin);
    });
});

// Install Composer dependencies excluding development ones
gulp.task('composer-install', ['get-composer'], function(callback) {
    exec('php composer.phar install --no-dev', function(error, stdout, stderr) {
        callback(error ? stderr : null);
    });
});

// Install all Composer dependencies
gulp.task('composer-install-dev', ['get-composer'], function(callback) {
    exec('php composer.phar install', function(error, stdout, stderr) {
        callback(error ? stderr : null);
    });
});

// Compile all JavaScript files of the Mibew Core
gulp.task('js', function() {
    return eventStream.merge(
        getClientSideApp('default'),
        getClientSideApp('chat'),
        getClientSideApp('thread_log'),
        getClientSideApp('users'),
        gulp.src(config.jsPath + '/source/**/*.js')
    )
    .pipe(uglify({preserveComments: 'some'}))
    .pipe(gulp.dest(config.jsPath + '/compiled'));
});

// Performs all job related with chat styles.
gulp.task('chat-styles', ['chat-styles-handlebars', 'chat-styles-js'], function() {
    // This task is just a combination of other tasks. That is why there is no
    // real code.
});

// Compile and concatenate handlebars files for all chat styles.
gulp.task('chat-styles-handlebars', function() {
    // TODO: Process all available styles, not only the default one.
    var stylePath = config.chatStylesPath + '/default';

    return gulp.src(stylePath + '/templates_src/client_side/**/*.handlebars')
        .pipe(handlebars())
        .pipe(wrapHandlebarsTemplate())
        .pipe(concat('templates.js'))
        .pipe(uglify({preserveComments: 'some'}))
        .pipe(header(config.compiledTemplatesHeader))
        .pipe(gulp.dest(stylePath + '/templates_compiled/client_side'));
});

// Compile and concatenate js files for all chat styles.
gulp.task('chat-styles-js', function() {
    // TODO: Process all available styles, not only the default one.
    var stylePath = config.chatStylesPath + '/default';

    return gulp.src(stylePath + '/js/source/**/*.js')
        .pipe(concat('scripts.js'))
        .pipe(uglify({preserveComments: 'some'}))
        .pipe(gulp.dest(stylePath + '/js/compiled'));
});

// Performs all job related with pages styles.
gulp.task('page-styles', function() {
    // TODO: Process all available styles, not only the default one.
    var stylePath = config.pageStylesPath + '/default';

    return eventStream.merge(
        gulp.src(stylePath + '/templates_src/client_side/default/**/*.handlebars')
            .pipe(handlebars())
            .pipe(wrapHandlebarsTemplate())
            .pipe(concat('default_app.tpl.js')),
        gulp.src(stylePath + '/templates_src/client_side/users/**/*.handlebars')
            .pipe(handlebars())
            .pipe(wrapHandlebarsTemplate())
            .pipe(concat('users_app.tpl.js'))
    )
    .pipe(uglify({preserveComments: 'some'}))
    .pipe(header(config.compiledTemplatesHeader))
    .pipe(gulp.dest(stylePath + '/templates_compiled/client_side'));
});

// Pack sources to .zip and .tar.gz archives.
gulp.task('pack-sources', ['composer-install'], function() {
    var sources = config.mibewPath + '/**/*',
        version = config.package.version;

    return eventStream.merge(
        gulp.src(sources, {dot: true})
            .pipe(zip('mibew-' + version + '.zip')),
        gulp.src(sources, {dot: true})
            .pipe(tar('mibew-' + version + '.tar'))
            .pipe(gzip())
    )
    .pipe(chmod(0644))
    .pipe(gulp.dest('release'));
});

// Builds all the sources
gulp.task('default', function(callback) {
    runSequence(
        ['phpcs', 'js', 'chat-styles', 'page-styles'],
        'pack-sources',
        callback
    );
});


/**
 * Loads and prepare js file for a client side application with the specified
 * name.
 *
 * @param {String} name Application name
 * @returns A files stream that can be piped to any gulp plugin.
 */
var getClientSideApp = function(name) {
    var appSource = config.jsPath + '/source/' + name;

    return gulp.src(appSource + '/**/*.js')
        .pipe(order(
            [
                appSource + '/init.js',
                // The following line is equivalent to
                // gulp.src([appSource + '/*.js', '!' + appSource + '/app.js']);
                appSource + '/!(app).js',
                appSource + '/models/**/base*.js',
                appSource + '/models/**/*.js',
                appSource + '/collections/**/base*.js',
                appSource + '/collections/**/*.js',
                appSource + '/model_views/**/base*.js',
                appSource + '/model_views/**/*.js',
                appSource + '/collection_views/**/base*.js',
                appSource + '/collection_views/**/*.js',
                appSource + '/regions/**/base*.js',
                appSource + '/regions/**/*.js',
                appSource + '/layouts/**/base*.js',
                appSource + '/layouts/**/*.js',
                appSource + '/**/base*.js',
                // The following line is equivalent to
                // gulp.src([appSource + '/**/*.js', '!' + appSource + '/app.js']);
                '!' + appSource + '/app.js',
                appSource + '/app.js'
            ],
            {base: process.cwd()}
        ))
        .pipe(concat(name + '_app.js'));
}

/**
 * Wraps a handlebars template with a function and attach it to the
 * Handlebars.templates object.
 *
 * @returns {Function} A function that can be used in pipe() method of a file
 *   stream right after gulp-handlebars plugin.
 */
var wrapHandlebarsTemplate = function() {
    return defineModule('plain', {
        wrapper: '(function() {\n'
            + 'var templates = Handlebars.templates = Handlebars.templates || {};\n'
            + 'Handlebars.templates["<%= relative %>"] = <%= handlebars %>;\n'
            + '})()',
        context: function(context) {
            return {relative: context.file.relative.replace(/\.js$/, '')};
        }
    });
}
