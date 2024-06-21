/**
 * Para usar este gulpfile, certifique-se de ter o ezoom-node-modules:
 * https://bitbucket.org/ezoom/ezoom-node-modules
 */
var pluginsDir = __dirname.replace(/\\/g, '/') + '/../node_modules/'

var projectFolder = 'default-modules-frontend'
var pathToDefaultModules = './../' + projectFolder + '/modules/'
var defaultModuleName = 'default'

function currentDirectory() {
    if(__dirname.search('/') < 0) {
        return __dirname.split(/(\\)/).pop();
    } else {
        return __dirname.split('/').pop();
    }
}

function camelCaser(input) {
    return input.replace(/[- _](.)/g, function(match, group1) {
        return group1.toUpperCase()
    })
}

function loadPlugin(name) {
    if(require.resolve(name)) {
        // console.log(require.resolve(name));
        return require(name)
    }

    // console.log(require.resolve(pluginsDir + name));
    return require(pluginsDir + name)
}

var gulp = require('gulp'),
    fs = loadPlugin('fs'),
    argv = loadPlugin('yargs').argv,
    config = {
        paths: {
            defaultModule: pathToDefaultModules + defaultModuleName,
            defaultModuleLess: pathToDefaultModules + defaultModuleName + '/assets/less',
            defaultModuleSass: pathToDefaultModules + defaultModuleName + '/assets/scss',
            bowerDir: 'bower_components',
            php: {
                srcAll:  'application/modules',
            },
            less: {
                srcAll:  'application/modules',
                main:    'application/modules/comum/assets/less/main.less',
                mainDir: 'application/modules/comum/assets/less',
            },
            sass: {
                srcAll:  'application/modules',
                main:    'application/modules/comum/assets/less/main.scss',
                mainDir: 'application/modules/comum/assets/scss',
            },
            js: {
                srcAll:  ['application/modules/**/assets/js/*.js','!application/modules/**/assets/js/*.min.js']
            },
            maps: {
                srcUrl: '/application/modules/comum/assets/sourcemaps'
            }
        }
    },
    plugins = {
        cssmin:      loadPlugin('gulp-clean-css'),
        debug:       loadPlugin('gulp-debug'),
        gulpReplace: loadPlugin('gulp-replace'),
        less:        loadPlugin('gulp-less'),
        livereload:  loadPlugin('gulp-livereload'),
        moment:      loadPlugin('moment'),
        notify:      loadPlugin('gulp-notify'),
        rename:      loadPlugin('gulp-rename'),
        sourcemaps:  loadPlugin('gulp-sourcemaps'),
        util:        loadPlugin('gulp-util'),
        vinyl:       loadPlugin('vinyl'),
        changed:     loadPlugin('gulp-dependencies-changed'),
        map:         loadPlugin('map-stream'),
        findInFile:  loadPlugin('find-in-file'),
        babel:       loadPlugin('gulp-babel'),
        browserSync:     loadPlugin('browser-sync'),
        autoprefixer:     loadPlugin('gulp-autoprefixer'),
    };


function getFilesThatContain(file){
    var regex = new RegExp('@import [\'"]?([^\'"]+)?('+file.replace('.','\\.')+')[\'"]?;', "g"),
        files = [],
        promises = [];
    return new Promise(
        (resolve) => {
            gulp.src(['application/modules/**/*.less', '!**/_*/**/*.less'])
                .pipe(plugins.map(function (file, cb) {
                    plugins.findInFile({
                        files: file.path,
                        find: regex
                    }, function(err, matchedFiles) {
                        if (matchedFiles.length){
                            matchedFiles.forEach((data) => {
                                var f = data.file.split('\\').pop();
                                if ((m = /^_/g.exec(f)) !== null) {
                                    promises.push(getFilesThatContain(f).then((fs)=>{
                                        files = files.concat(fs);
                                    }));
                                }else{
                                    files.push(data.file);
                                }
                            });
                        }
                    });
                    cb(null, file);
                })).on('end', function(){
                    Promise.all(promises).then(function(values) {
                        resolve(files)
                    });
                });
        }
    );
}

function css(file){
    var lessFile = new plugins.vinyl({ path: file.path }),
        basePath = (lessFile.path).replace(lessFile.relative,''),
        lessPromise = new Promise((resolve) => {
            // Confere se arquivo começa com _
            if ((m = /^_/g.exec(lessFile.basename)) !== null) {
                getFilesThatContain(lessFile.basename).then((files)=>{
                    resolve(files);
                });
            }else{
                // Pega o arquivo a ser alterado
                resolve([lessFile.path]);
            }
        }).then((files)=>{
            // Compila e minifica os arquivos selecionados até aqui:
            return gulp.src(files, { base: 'application/modules' })
                    .pipe(
                        plugins.less({
                            javascriptEnabled: true,
                            paths: [
                                '.',
                                './node_modules',
                                './bower_components',
                                'application/modules/comum/assets/less'
                            ]
                        }).on("error", plugins.notify.onError(function (error) {
                            return "Error: " + error.message + '\n' + error.line + ':' + error.index
                        }))
                    )
                    .pipe(plugins.autoprefixer('last 10 versions', 'ie 9'))
                    .pipe(plugins.cssmin({ compatibility: 'ie9', level: { 1: { specialComments: 0 } } }))
                    .pipe(plugins.rename(function(path) {
                        path.dirname += '/../css';
                    }))
                    .pipe(gulp.dest('application/modules'))
                    .pipe(plugins.debug({title:'Compiled LESS: '}))
                    .pipe(plugins.notify({
                        message:'Compiled LESS',
                        onLast:true
                    }))
                    .pipe(plugins.browserSync.stream());
        });
};

//Cria um novo módulo no framework
gulp.task('add', function() {
    if(argv.module) {
        var add = argv.module,
            name = add.replace(/ /g, '_')
            author = argv.author||'Ezoom',
            moment = plugins.moment().format('DD/MM/YYYY - HH:mm:ss'),
            year = plugins.moment().format('YYYY'),
            moduleName = name.charAt(0).toUpperCase() + name.slice(1),
            camelCase = camelCaser(moduleName),
            fileName = name.charAt(0).toLowerCase() + name.slice(1),
            dest = 'application/modules/' + fileName

        fs.stat('application/modules/'+fileName, function (err, stats) {
            if (err) {
                source = [config.paths.defaultModule+'/**/*.*', '!'+config.paths.defaultModule+'/language/**/*.*'];
                if(argv.sass)
                    source.push('!'+config.paths.defaultModuleLess+'/**/*.*');
                else {
                    source.push('!'+config.paths.defaultModuleSass+'/**/*.*');
                }
                return gulp.src(source)
                    .pipe(plugins.gulpReplace('{{Controller}}', moduleName))
                    .pipe(plugins.gulpReplace('{{CamelCase}}', camelCase))
                    .pipe(plugins.gulpReplace('{{Folder}}', fileName))
                    .pipe(plugins.gulpReplace('{{Author}}', author))
                    .pipe(plugins.gulpReplace('{{Moment}}', moment))
                    .pipe(plugins.gulpReplace('{{Year}}', year))
                    .pipe(plugins.rename(function(path) {
                        path.basename = fileName;
                        path.dirname.replace('default', fileName)
                    }))
                    .pipe(gulp.dest(dest));
            } else {
                plugins.util.log(plugins.util.colors.red('Módulo ' + fileName + ' já existe!'))
            }
        })
    }
});

//LESS compile task
gulp.task("less", function(){
    return gulp.src([config.paths.less.srcAll + '/**/[^_]*.less', '!application/modules/comum/assets/plugins/**/*.less'])
        .pipe(plugins.less({
            javascriptEnabled: true,
            paths: ['.',
                    './node_modules',
                    './bower_components',
                    'application/modules/comum/assets/less']
        })
            .on("error", plugins.notify.onError(function (error) {
                return "Error: " + error.message + '\n' + error.line + ':' + error.index
            })))
        .pipe(plugins.cssmin({compatibility:'ie9'}))
        .pipe(plugins.rename(function(path) {
            path.dirname += '/../css';
        }))
        .pipe(gulp.dest(config.paths.less.srcAll))
        .pipe(plugins.debug())
        .pipe(plugins.notify({
            message:'Compiled LESS (' + plugins.moment().format('MMM Do h:mm:ss A') + ')',
            onLast:true
        }));
});

gulp.task('reload', function() {
    plugins.browserSync.reload();
});

//Watches for changes and executes the tasks automatically
gulp.task('watch', function() {
    plugins.browserSync.init({
        proxy: "localhost/" + currentDirectory(),
    });
    //plugins.livereload.listen()
    gulp.watch(config.paths.php.srcAll + '/**/views/*.php', ['reload']);
    gulp.watch(config.paths.less.srcAll + '/**/*.less', css);
    gulp.watch(config.paths.js.srcAll, ['compress'])
    // gulp.watch(config.paths.sass.srcAll + '/**/*.scss', ['sass'])
});

gulp.task('compress', function() {
    var minify = require(pluginsDir + 'gulp-minify');
    gulp.src(config.paths.js.srcAll)
        .pipe(plugins.babel({
            presets: ['env']
        }))
        .on('error', plugins.notify.onError({
            message: "Error: <%= error.message %>",
            title: "Syntax error"
        }))
        .pipe(minify({
            ext:{
                min:'.min.js'
            },
            noSource: true,
            ignoreFiles: ['.min.js', '-min.js']
        }).on('error', plugins.notify.onError({
            message: "Error: <%= error.message %>",
            title: "Syntax error"
        })))
        .pipe(gulp.dest('application/modules'))
        .pipe(plugins.browserSync.stream())
        .pipe(plugins.notify({
            message:'Compressed JS (' + plugins.moment().format('MMM Do h:mm:ss A') + ')',
            onLast:true
        }));
});

gulp.task('default', ['watch','compress']);
gulp.task('dev', ['watch']);
gulp.task('dist', ['compress']);