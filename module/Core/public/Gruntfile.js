module.exports = function(grunt) {
    grunt.initConfig({
        copy: {
            core: {
                files: [
                    {
                        expand: true,
                        cwd: nodeModulesPath+'/font-awesome/fonts',
                        src: "**",
                        dest: "public/dist/fonts"
                    },
                    {
                        expand: true,
                        cwd: nodeModulesPath+'/flag-icon-css/flags',
                        src: "**",
                        dest: "public/dist/flags"
                    },
                    {
                        expand: true,
                        cwd: nodeModulesPath+'/tinymce/skins',
                        src: "**",
                        dest: "public/dist/tinymce-skins"
                    },
                ]
            }
        },
        less: {
            options: {
                modifyVars: {
                    "fa-font-path": "../fonts",
                    "flag-icon-css-path": "../flags"
                }
            },
            core: {
                options: {
                    compress: false,
                },
                files: {
                    "public/dist/css/core.css": [
                        "public/modules/Core/less/yawik.less",
                        "./node_modules/select2/dist/css/select2.min.css",
                        "./node_modules/pnotify/dist/pnotify.css",
                        "./node_modules/pnotify/dist/pnotify.buttons.css",
                        "./node_modules/bootsrap3-dialog/dist/css/bootstrap-dialog.css"
                    ]
                },
            },
        },
        concat: {
            core: {
                src: [
                    "./node_modules/jquery/dist/jquery.js",
                    "./node_modules/bootstrap/dist/js/bootstrap.js",
                    "./node_modules/pnotify/dist/pnotify.js",
                    "./node_modules/pnotify/dist/pnotify.buttons.js",
                    "./node_modules/select2/dist/js/select2.js",
                    "./node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js",
                    "./node_modules/blueimp-file-upload/js/jquery.iframe-transport.js",
                    "./node_modules/blueimp-file-upload/js/jquery.fileupload.js",
                    "./node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js",
                    "./node_modules/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js",
                    "./node_modules/tinymce/tinymce.js",

                    // tiny mce tasks
                    "./node_modules/tinymce/themes/modern/theme.js",
                    "./node_modules/tinymce/plugins/autolink/plugin.js",
                    "./node_modules/tinymce/plugins/lists/plugin.js",
                    "./node_modules/tinymce/plugins/advlist/plugin.js",
                    "./node_modules/tinymce/plugins/visualblocks/plugin.js",
                    "./node_modules/tinymce/plugins/code/plugin.js",
                    "./node_modules/tinymce/plugins/fullscreen/plugin.js",
                    "./node_modules/tinymce/plugins/contextmenu/plugin.js",
                    "./node_modules/tinymce/plugins/paste/plugin.js",
                    "./node_modules/tinymce/plugins/link/plugin.js",

                    "./public/modules/Core/js/core.init.js",
                ],
                dest: "public/dist/js/core.js"
            },
            locales: {
                files: {
                    "./public/dist/locales/en.js": [
                        "./node_modules/select2/dist/js/i18n/en.js"
                    ],
                    "./public/dist/locales/de.js": [
                        "./node_modules/select2/dist/js/i18n/de.js",
                        "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.de.js"
                    ],
                    "./public/dist/locales/es.js": [
                        "./node_modules/select2/dist/js/i18n/es.js",
                        "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.js"
                    ],
                    "./public/dist/locales/fr.js": [
                        "./node_modules/select2/dist/js/i18n/fr.js",
                        "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.js"
                    ],
                    "./public/dist/locales/it.js": [
                        "./node_modules/select2/dist/js/i18n/it.js",
                        "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.it.js"
                    ],
                }
            },
            bootstrapDialog: {
                src: [
                    './node_modules/bootstrap3-dialog/dist/js/bootstrap-dialog.js'
                ],
                dest: "public/dist/js/bootstrap-dialog.js"
            }
        },
        uglify: {
            options: {
                "compress": true,
            },
            core: {
                files: {
                    './public/dist/js/core.min.js': './public/dist/js/core.js',
                    './public/dist/js/bootstrap-dialog.min.js': './public/dist/js/bootstrap-dialog.js',
                }
            },
        },
        cssmin: {
            core: {
                files: {
                    './public/dist/css/core.min.css': './public/dist/css/core.css'
                }
            }
        }
    });

    grunt.registerTask('yawik:core:dev',["copy","less","concat"]);
    grunt.registerTask('yawik:core',["copy:core","less:core","concat:core", 'cssmin:core',"uglify:core"]);
};
