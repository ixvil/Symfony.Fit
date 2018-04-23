'use strict';
module.exports = function(grunt) {
    grunt.initConfig({
        jshint: {
            options: {
                jshintrc: '.jshintrc'
            },
            all: [
                'Gruntfile.js',
                'public/assets/js/*.js',
                '!assets/js/scripts.min.js'
            ]
        },
        less: {
            dist: {
                files: {
                    'public/assets/css/main.min.css': [
                        'assets/less/app.less'
                    ]
                },
                options: {
                    compress: true,
// LESS source map
// To enable, set sourceMap to true and update sourceMapRootpath based on your install
                    sourceMap: false,
                    sourceMapFilename: 'assets/css/main.min.css.map',
                    sourceMapRootpath: '/app/'
                }
            }
        },
        uglify: {
            dist: {
                files: {
                    'public/assets/js/scripts.min.js': [
                        'assets/js/plugins/bootstrap/*',
                        'assets/js/plugins/*.js',
                        'assets/js/_*.js'
                    ]
                },
                options: {
                }
            }
        },
        watch: {
            less: {
                files: [
                    'assets/less/*.less',
                    'assets/less/bootstrap/*.less'
                ],
                tasks: ['less']
            },
            js: {
                files: [
                    '<%= jshint.all %>'
                ],
                tasks: ['jshint', 'uglify']
            },
            livereload: {
// Browser live reloading
// https://github.com/gruntjs/grunt-contrib-watch#live-reloading
                options: {
                    livereload: true
                },
                files: [
                    'public/assets/css/main.min.css',
                    'public/assets/js/scripts.min.js'
                ]
            }
        },
        clean: {
            dist: [
                'public/assets/css/main.min.css',
                'public/assets/js/scripts.min.js'
            ]
        }
    });
// Load tasks
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
// Register tasks
    grunt.registerTask('default', [
        'clean',
        'less',
        'uglify'
    ]);
    grunt.registerTask('dev', [
        'watch'
    ]);
};