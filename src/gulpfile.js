// Initialize requirements
var fs = require('fs'),
    https = require('https'),
    exec = require('child_process').exec,
    eventStream = require('event-stream'),
    runSequence = require('run-sequence'),
    through = require('through2'),
    lodash = require('lodash'),
    PoFile = require('pofile'),
    strftime = require('strftime'),
    del = require('del'),
    bower = require('bower'),
    gulp = require('gulp'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    phpcs = require('gulp-phpcs'),
    order = require('gulp-order'),
    handlebars = require('gulp-handlebars'),
    handlebarsEngine = require('handlebars'),
    defineModule = require('gulp-define-module'),
    header = require('gulp-header'),
    zip = require('gulp-zip'),
    tar = require('gulp-tar'),
    gzip = require('gulp-gzip'),
    chmod = require('gulp-chmod'),
    xgettext = require('gulp-xgettext'),
    concatPo = require('gulp-concat-po'),
    rename = require('gulp-rename'),
    eslint = require('gulp-eslint'),
    mkdirp = require('mkdirp');

// Set global configuration
var config = {
    mibewPath: 'mibew',
    configsPath: 'mibew/configs',
    phpVendorPath: 'mibew/vendor',
    jsVendorPath: 'mibew/js/vendor',
    pluginsPath: 'mibew/plugins',
    avatarsPath: 'mibew/files/avatar',
    cachePath: 'mibew/cache',
    jsPath: 'mibew/js',
    chatStylesPath: 'mibew/styles/chats',
    pageStylesPath: 'mibew/styles/pages',
    i18nPath: 'i18n',
    i18nPrefix: 'mibew-i18n-',
    releasePath: 'release',
    uploadPath: 'upload',
    compiledTemplatesHeader: fs.readFileSync('tools/compiled_templates_header.txt'),
    getComposerUrl: 'https://getcomposer.org/installer',
    phpBin: 'php -d "suhosin.executor.include.whitelist = phar" -d "memory_limit=512M"',
    package: require('./composer.json'),
    msginit: 'msginit',
    msgcat: 'msgcat'
}
config.i18nSuffix = '-' + config.package.version + '-' + strftime('%Y%m%d');

// Tasks for cleaning //////////////////

// Cleans all built files
gulp.task('clean', function(callback) {
    del([
        config.releasePath,
        'composer.lock',
        config.phpVendorPath,
        config.jsVendorPath,
        config.jsPath + '/compiled/**/*',
        '!' + config.jsPath + '/compiled/.keep',
        config.chatStylesPath + '/*/templates_compiled/client_side/*.js',
        config.chatStylesPath + '/*/js/compiled/*.js',
        config.pageStylesPath + '/*/templates_compiled/client_side/*.js'
    ], callback);
});

// Cleans temporary English translation in the release dir
gulp.task('clean-tmp-po', function() {
    return del([config.releasePath + '/translation.po']);
});

// Cleans temporary files of translations in the release dir
gulp.task('clean-tmp-i18n-files', function() {
    return del([
            config.releasePath + '/**/*',
            '!' + config.releasePath + '/*.zip',
            '!' + config.releasePath + '/*.tar.gz',
            '!' + config.releasePath + '/*.pot'
    ]);
});

// Cleans all files prepared for uploading to CDN
gulp.task('clean-upload', function(callback) {
    return del([config.uploadPath], callback);
});

// Cleans everything
gulp.task('clean-all', function(callback) {
    runSequence( ['clean', 'clean-upload'],
                 callback );
});

// Tasks for checking //////////////////

// Checks all PHP files with PHP Code Sniffer
gulp.task('phpcs', ['composer-install-dev'], function() {
    return gulp.src([
                config.mibewPath + '/**/*.php',
                '!' + config.phpVendorPath + '/**/*.*',
                '!' + config.pluginsPath + '/**/*.*',
                '!' + config.cachePath + '/**/*.*'
        ], {
            // Content of the cache directory is readable only for webserver.
            // Thus we must to set "strict" option to false to prevent "EACCES"
            // errors. At the same we need to see all errors that take place.
            strict: false,
            silent: false
        })
        .pipe(phpcs({
            bin: config.phpVendorPath + '/bin/phpcs',
            standard: 'PSR2',
            warningSeverity: 0
        }))
        .pipe(phpcs.reporter('log'))
        .pipe(phpcs.reporter('fail'));
});

// Checks all JavaScript Source files with ESLint
gulp.task('eslint', function() {
    return gulp.src(config.jsPath + '/source/**/*.js')
        .pipe(eslint())
        .pipe(eslint.format())
        .pipe(eslint.failAfterError());
});

// Watcher tasks ///////////////////////

// Watch styles
gulp.task('watch', [], function(){
    gulp.watch(config.pageStylesPath + '/**/*.handlebars', ['page-styles']);
    gulp.watch(config.chatStylesPath + '/**/js/source/**/*.js', ['chat-styles-js']);
    gulp.watch(config.chatStylesPath + '/**/*.handlebars', ['chat-styles-handlebars']);
});

// Service tasks //////////////////

// Get and install PHP Composer
gulp.task('get-composer', function(callback) {
    // Check whether Composer is already in place
    if (fs.existsSync('./composer.phar')) {
        callback(null);
        return;
    }

    // Get installer from the Internet
    https.get(config.getComposerUrl, function(response) {
        // Run PHP to install Composer
        var php = exec(config.phpBin, function(error, stdout, stderr) {
            callback(error ? stderr : null);
        });
        // Pass installer code to PHP via STDIN
        response.pipe(php.stdin);
    });
});

// Install Composer dependencies excluding development ones
gulp.task('composer-install', ['get-composer'], function(callback) {
    exec(config.phpBin + ' composer.phar install --no-dev',
        function(error, stdout, stderr) {
            callback(error ? stderr : null);
        });
});

// Install all Composer dependencies
gulp.task('composer-install-dev', ['get-composer'], function(callback) {
    exec(config.phpBin + ' composer.phar install',
        function(error, stdout, stderr) {
            callback(error ? stderr : null);
        });
});

// Installs bower dependencies
gulp.task('bower-install', function(callback) {
    bower.commands.install([], {}, {})
        .on('error', function(error) {
            callback(error);
        })
        .on('end', function() {
            // We should manually minify JavaScript files that was not minified
            // by bower packages' authors
            // TODO: This is a temproary workaround and should be removed once
            // the packages will be fixed
            var stream = eventStream.merge(
                gulp.src(config.jsVendorPath + '/backbone/backbone.js',
                         {base: config.jsVendorPath})
                    .pipe(uglify({preserveComments: 'some'}))
                    // There are neither "@license" tag nor "!preserve" in the
                    // header. Add the header manually
                    .pipe(header(
                        "// Backbone.js 1.1.2\n"
                            + "// (c) 2010-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors\n"
                            + "// Backbone may be freely distributed under the MIT license.\n"
                            + "// For all details and documentation:\n"
                            + "// http://backbonejs.org\n"
                    ))
                    .pipe(rename('backbone/backbone-min.js')),
                gulp.src(config.jsVendorPath + '/json/json2.js',
                         {base: config.jsVendorPath})
                    .pipe(uglify({preserveComments: 'some'}))
                    // There are neither "@license" tag nor "!preserve" in the
                    // header, add the header manually
                    .pipe(header("// json2.js. Public Domain. See http://www.JSON.org/js.html\n"))
                    .pipe(rename('json/json2.min.js'))
            )
            .pipe(gulp.dest(config.jsVendorPath));

            stream
                .on('error', callback)
                .on('end', callback);
        });
});

// Restore empty English translation in the source code
gulp.task('post-pack-cleanup', function(callback) {
    gulp.src('translation.po')
        .pipe(gulp.dest(config.mibewPath + '/locales/en/'));

    del(['translation.po', config.releasePath + '/translation.po'], callback);
});

// Tasks of compiling //////////////////

// Compile all JavaScript files of the Mibew Messenger Core
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

// Compile and concatenate Handlebars templates for all chat styles
gulp.task('chat-styles-handlebars', function() {
    return getChildDirs(config.chatStylesPath)
        .then(function (dirs) {
            return Promise.all(dirs.map(function (dir) {
                return new Promise(function(resolve, reject) {
                    gulp.src(config.chatStylesPath + '/' + dir
                             + '/templates_src/client_side/**/*.handlebars')
                        .pipe(handlebars({
                            // Use specific version of Handlebars.js
                            handlebars: handlebarsEngine
                        }))
                        .pipe(wrapHandlebarsTemplate())
                        .pipe(concat('templates.js'))
                        .pipe(uglify({preserveComments: 'some'}))
                        .pipe(header(config.compiledTemplatesHeader))
                        .pipe(gulp.dest(config.chatStylesPath + '/' + dir
                                        + '/templates_compiled/client_side'))
                        .on('end', resolve)
                        .on('error', reject);
                });
            }));
        });
});

// Compile and concatenate js files for all chat styles
gulp.task('chat-styles-js', function() {
    return getChildDirs(config.chatStylesPath)
        .then(function (dirs) {
            return Promise.all(dirs.map(function (dir) {
                return new Promise(function(resolve, reject) {
                    gulp.src(config.chatStylesPath + '/' + dir
                             + '/js/source/**/*.js')
                        .pipe(concat('scripts.js'))
                        .pipe(uglify({preserveComments: 'some'}))
                        .pipe(gulp.dest(config.chatStylesPath + '/' + dir
                                        + '/js/compiled'))
                        .on('end', resolve)
                        .on('error', reject);
                });
            }));
        });
});

// Performs all job related to pages styles
gulp.task('page-styles', function() {
    return getChildDirs(config.pageStylesPath)
        .then(function (dirs) {
            return Promise.all(dirs.map(function (dir) {
                return new Promise(function(resolve, reject){
                    gulp.src(config.pageStylesPath + '/' + dir
                             + '/templates_src/client_side/**/*.handlebars')
                        .pipe(handlebars({
                            // Use specific version of Handlebars.js
                            handlebars: handlebarsEngine
                        }))
                        .pipe(wrapHandlebarsTemplate())
                        .pipe(concat('templates.js'))
                        .pipe(uglify({preserveComments: 'some'}))
                        .pipe(header(config.compiledTemplatesHeader))
                        .pipe(gulp.dest(config.pageStylesPath + '/' + dir
                                        + '/templates_compiled/client_side'))
                        .on('end', resolve)
                        .on('error', reject);
                });
            }));
        });
});

// Performs all job related to chat styles
gulp.task('chat-styles', ['chat-styles-handlebars', 'chat-styles-js'], function() {
    // This task is just a combination of other tasks, there is no real code.
});

// Prepares ready to use development version of Mibew Messenger without
// packing or validating it
gulp.task('rebuild', function(callback) {
    runSequence(
        'clean',
        ['js', 'chat-styles', 'page-styles', 'composer-install',
         'bower-install'],
        callback
    );
});

// Tasks for localization issues ///////

// Generate POT-file based upon the source code
gulp.task('generate-pot', function() {
    return eventStream.merge(
        gulp.src([
                config.mibewPath + '/**/*.php',
                '!' + config.phpVendorPath + '/**/*.*',
                '!' + config.pluginsPath + '/**/*.*',
                '!' + config.cachePath + '/**/*.*'
            ], {
                // Content of the cache directory is readable only for
                // webserver. Thus we must to set "strict" option to false
                // to prevent "EACCES" errors. At the same we need to see all
                // errors that take place.
                strict: false,
                silent: false
            })
            .pipe(xgettext({
                language: 'PHP',
                keywords: [
                    {name: 'getlocal'},
                    {name: 'no_field'},
                    {name: 'wrong_field'},
                    {name: 'failed_uploading_file', singular: 2}
                ]
            })),
        gulp.src(config.jsPath + '/source/**/*.js', {base: config.mibewPath})
            .pipe(xgettext({
                language: 'JavaScript',
                keywords: [
                    {name: 'trans'}
                ]
            })),
        gulp.src([
                config.chatStylesPath + '/default/templates_src/**/*.handlebars',
                config.pageStylesPath + '/default/templates_src/**/*.handlebars'
            ], {base: config.mibewPath})
            .pipe(xgettextHandlebars())
    )
    .pipe(concatPo(config.i18nPrefix + 'translation'
                   + config.i18nSuffix + '.pot',
            {
                headers: {
                    'Project-Id-Version': 'Mibew Messenger '
                                            + config.package.version,
                    'Report-Msgid-Bugs-To': config.package.support.email,
                    'POT-Creation-Date': strftime('%Y-%m-%d %H:%M%z'),
                    'PO-Revision-Date': '',
                    'Last-Translator': '',
                    'Language-Team': '',
                    'Content-Type': 'text/plain; charset=UTF-8'
                }
            })
    )
    .pipe(gulp.dest(config.releasePath));
});

// Generate temporary English translation to be used in the release
gulp.task('generate-tmp-po', ['generate-pot'], function(callback) {
    return exec(config.msginit + ' --no-translator --no-wrap -i '
                + config.releasePath + '/' + config.i18nPrefix + 'translation'
                + config.i18nSuffix + '.pot' + ' -l en -o '
                + config.releasePath + '/translation.po',
            function(error, stdout, stderr) {
                callback(error ? stderr : null);
            });
});

// Generate complete translations (that includes missed strings) to be used
// in the release
gulp.task('generate-pos', ['generate-tmp-po'], function(callback) {

    return getChildDirs(config.i18nPath + '/translations')
       .then(function (dirs) {
            return Promise.all(dirs.map(function (dir) {
                return new Promise(function(resolve, reject) {
                    mkdirp(config.releasePath + '/' + dir, function (error) {
                        if (error) {
                            reject(error);
                        }
                    });
                    exec(config.msgcat + ' ' + config.i18nPath + '/translations/'
                         + dir + '/translation.po ' + config.releasePath
                         + '/translation.po --no-location --no-wrap --use-first'
                         + ' -o ' + config.releasePath + '/' + dir
                         + '/translation.po',
                         function(error, stdout, stderr) {
                            if (error) {
                                reject(error);
                            }
                    });

                    var sources = [
                        config.i18nPath + '/translations/' + dir + '/**/*',
                        '!' + config.i18nPath + '/translations/' + dir
                        + '/translation.po'
                    ];

                    gulp.src(sources)
                        .pipe(gulp.dest(config.releasePath + '/' + dir))
                        .on('error', reject)
                        .on('end', resolve);
                });
            }));
    });
});

// Packaging tasks /////////////////////

// Pack sources for release as .zip and .tar.gz archives
gulp.task('pack-sources', ['composer-install', 'bower-install'], function() {
    gulp.src(config.mibewPath + '/locales/en/translation.po')
        .pipe(gulp.dest('.'));
    gulp.src(config.releasePath + '/translation.po')
        .pipe(gulp.dest(config.mibewPath + '/locales/en/'));

    var sources = [
        config.mibewPath + '/**/*',
        // Exclude user's config
        '!' + config.configsPath + '/config.yml',
        // Exclude cache files but not the ".keep" file.
        '!' + config.cachePath + '/**/!(.keep)',
        // Exclude avatars but not the ".keep" file.
        '!' + config.avatarsPath + '/!(.keep)',
        // Exclude plugins but not the ".keep" file.
        '!' + config.pluginsPath + '/!(.keep)',
        '!' + config.pluginsPath + '/*/**/*',
        // Exclude Git repositories that can be shipped with third-party libs
        '!' + config.phpVendorPath + '/**/.git',
        '!' + config.phpVendorPath + '/**/.git/**/*',
        // Exclude vendors binaries
        '!' + config.phpVendorPath + '/bin/**/*',
        // Exclude JavaScript sources
        '!' + config.jsPath + '/source/**/*',
        // Actually we does not need backbone.babysitter and backbone.wreqr
        // dependencies because they embed into marionette.js. So we exclude
        // "backbone.babysitter" and "backbone.wreqr" directories and all their
        // contents.
        '!' + config.jsVendorPath + '/backbone.babysitter{,/**}',
        '!' + config.jsVendorPath + '/backbone.wreqr{,/**}',
        // Exclude dot files within third-party JS libraries.
        '!' + config.jsVendorPath + '/**/.*',
        // Exclude config files of various package systems
        '!' + config.jsVendorPath + '/**/{bower,component,package,composer}.json',
        // Exclude config files of various build systems
        '!' + config.jsVendorPath + '/**/Gruntfile.*',
        '!' + config.jsVendorPath + '/**/gulpfile.*',
        '!' + config.jsVendorPath + '/**/Makefile',
        // Exclude HTML files from third-party JS libraries. Such files can be
        // used for docs or for tests, we need none of them.
        '!' + config.jsVendorPath + '/**/*.html',
        // There are too many useless files in Vex.js library. Exclude them.
        '!' + config.jsVendorPath + '/vex/sass{,/**}',
        '!' + config.jsVendorPath + '/vex/docs{,/**}',
        '!' + config.jsVendorPath + '/vex/docs{,/**}',
        '!' + config.jsVendorPath + '/vex/coffee{,/**}'
    ];
    var srcOptions = {
        // Dot files (.htaccess, .keep, etc.) must be included in the package
        dot: true,
        // Content of the cache directory is readable only for webserver. Thus
        // we must to set "strict" option to false to prevent "EACCES" errors.
        // At the same we need to see all errors that take place.
        strict: false,
        silent: false,
        // Preserve root directory name (i.e. 'mibew/')
        base: '.'
    }
    var version = config.package.version;

    return eventStream.merge(
        gulp.src(sources, srcOptions)
            .pipe(zip('mibew-' + version + '.zip')),
        gulp.src(sources, srcOptions)
            .pipe(tar('mibew-' + version + '.tar'))
            .pipe(gzip())
    )
    .pipe(chmod(644))
    .pipe(gulp.dest(config.releasePath));
});

// Pack translations for release as .zip and .tar.gz archives
gulp.task('pack-i18n', function(callback) {
    return getChildDirs(config.i18nPath + '/translations')
       .then(function (dirs) {
            return Promise.all(dirs.map(function (dir) {
                return new Promise(function(resolve, reject) {

                    var srcOptions = {
                        base: config.releasePath
                    };

                    eventStream.merge(
                        gulp.src(config.releasePath + '/' + dir
                                 + '/**/*', srcOptions)
                            .pipe(zip(config.i18nPrefix + dir
                                      + config.i18nSuffix + '.zip')),
                        gulp.src(config.releasePath + '/' + dir
                                 + '/**/*', srcOptions)
                            .pipe(tar(config.i18nPrefix + dir
                                      + config.i18nSuffix + '.tar'))
                            .pipe(gzip())
                    )
                    .pipe(chmod(644))
                    .pipe(gulp.dest(config.releasePath))
                    .on('error', reject)
                    .on('end', resolve);

                });
            }));
    });
});

// Tasks of releasing //////////////////

// Prepare release of Mibew Messenger
gulp.task('prepare-release', function(callback) {
    runSequence(
        'clean',
        ['phpcs', 'js', 'chat-styles', 'page-styles', 'generate-pot',
         'generate-tmp-po'],
        'pack-sources',
        'post-pack-cleanup',
        callback
    );
});

// Prepare releases of translations for Mibew Messenger
gulp.task('prepare-i18n-release', function(callback) {
    runSequence( 'clean-tmp-po',
                 'generate-pos',
                 'pack-i18n',
                 'clean-tmp-i18n-files',
                 callback
    );
});

// Mirror prepared releases of Mibew Messenger and its translations in a form
// intended for uploading into the CDN
gulp.task('prepare-for-upload', function(callback) {
    gulp.src(config.releasePath + '/mibew-' + config.package.version + '.*')
        .pipe(gulp.dest(config.uploadPath + '/core/' + config.package.version + '/'));

    gulp.src(config.releasePath + '/mibew-i18n-translation-' + config.package.version + '-*.pot')
        .pipe(gulp.dest(config.uploadPath + '/i18n/_pot/' + config.package.version + '/'));

    return getChildDirs(config.i18nPath + '/translations')
           .then(function (dirs) {
                return Promise.all(dirs.map(function (dir) {
                    return new Promise(function(resolve, reject) {
                        gulp.src(config.releasePath + '/mibew-i18n-' + dir + '-' + config.package.version + '-*.*')
                            .pipe(gulp.dest(config.uploadPath + '/i18n/' + dir + '/' + config.package.version + '/'))
                            .on('error', reject)
                            .on('end', resolve);
                    });
                }));
           });
});

// Perform all preparations before uploading the release of Mibew Messenger and
// its translations into the CDN
gulp.task('prepare-all', function(callback) {
    runSequence('clean-all', 'prepare-release', 'prepare-i18n-release',
                'prepare-for-upload', callback);
});

// Prepare release archives of the development version of Mibew Messenger
gulp.task('default', function(callback) {
    runSequence(
        'clean',
        ['js', 'chat-styles', 'page-styles'],
        'pack-sources',
        'post-pack-cleanup',
        callback
    );
});

/**
 * Loads and prepare js file for a client side application with the specified
 * name.
 *
 * @param {String} name Application name.
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
            return {relative: context.file.relative.replace(/\.js$/, '').replace(/\\/g, '/')};
        }
    });
}

/**
 * Extracts gettext messages from handlebars templates.
 *
 * @returns {Function} A function that can be used in pipe() method of a file
 *   stream.
 */
var xgettextHandlebars = function() {
    var helperRegExp = /\{{2}l10n\s*('|")(.*?[^\\])\1.*?\}{2}/g;

    return through.obj(function (file, enc, callback) {
        var po = new PoFile();
            match = false,
            contents = file.contents.toString();

        while (match = helperRegExp.exec(contents)) {
            // Try to find item in the .po file by its name
            var item = lodash.find(po.items, function(item) {
                return match[2] === item.msgid;
            });

            var line = contents.substr(0, match.index).split(/\r?\n|\r/g).length;

            if (!item) {
                // There is no such item, create new one
                item = new PoFile.Item();
                item.msgid = match[2].replace(/\\'/g, "'").replace(/\\"/g, '"');
                po.items.push(item);
            }

            // Add new reference
            item.references.push(file.relative + ':' + line);
        }

        // Update file contents
        file.contents = new Buffer(po.toString());
        this.push(file);

        callback();
    });
}

/**
 * Retrieves list of all dirs which are placed in the specified one.
 *
 * @param {String} srcDir A dir to search.
 * @returns Promise A promise which will be resolved with list of child dirs or
 *   rejected with the occured error.
 */
var getChildDirs = function(srcDir) {
    return (new Promise(function(resolve, reject) {
        fs.readdir(srcDir, function (err, files) {
            if (err) {
                reject(err);
            } else {
                resolve(files);
            }
        });
    })).then(function (files) {
        // Replace all files that are not a directory with nulls.
        return Promise.all(files.map(function (file) {
            return new Promise(function (resolve, reject) {
                fs.lstat(srcDir + '/' + file, function (err, stat) {
                    if (err) {
                        reject(err);
                    } else {
                        resolve(stat.isDirectory() ? file : null);
                    }
                });
            });
        }));
    }).then(function(dirs) {
        // Remove all nulls from the array.
        return dirs.filter(function (dir) {
            return null !== dir;
        });
    });
}
