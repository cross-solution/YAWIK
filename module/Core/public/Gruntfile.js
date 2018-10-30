module.exports = function(grunt) {

    var targetDir = grunt.config.get('targetDir');
    var nodeModulesPath = grunt.config.get('nodeModulesPath');

    grunt.config.merge({
        copy: {
            core: {
                files: [
                    {
                        expand: true,
                        cwd: nodeModulesPath+'/font-awesome/fonts',
                        src: "**",
                        dest: targetDir+"/dist/fonts"
                    },
                    {
                        expand: true,
                        cwd: nodeModulesPath+'/flag-icon-css/flags',
                        src: "**",
                        dest: targetDir+"/dist/flags"
                    },
                    {
                        expand: true,
                        cwd: nodeModulesPath+'/tinymce/skins',
                        src: "**",
                        dest: targetDir+"/dist/tinymce-skins"
                    },
                ]
            }
        },
        less: {
            core: {
                options: {
                    compress: false,
                    modifyVars: {
                        "fa-font-path": "../fonts",
                        "flag-icon-css-path": "../flags"
                    }
                },
                files: [
                    {
                        src: [
                            targetDir+"/modules/Core/less/yawik.less",
                            "./node_modules/select2/dist/css/select2.min.css",
                            "./node_modules/pnotify/dist/pnotify.css",
                            "./node_modules/pnotify/dist/pnotify.buttons.css",
                            "./node_modules/bootsrap3-dialog/dist/css/bootstrap-dialog.css"
                        ],
                        dest: targetDir+"/dist/css/core.css"
                    }
                ],
            },
        },
        concat: {
            core: {
                files: [
                    {
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

                            targetDir+"/modules/Core/js/core.init.js",
                        ],
                        dest: targetDir+"/dist/js/core.js"
                    },

                    // locales start
                    {
                        dest: targetDir+"/dist/locales/en.js",
                        src: [
                            "./node_modules/select2/dist/js/i18n/en.js"
                        ]
                    },
                    {
                        dest: targetDir+"/dist/locales/de.js",
                        src: [
                            "./node_modules/select2/dist/js/i18n/de.js",
                            "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.de.js"
                        ]
                    },
                    {
                        dest: targetDir+"/dist/locales/es.js",
                        src: [
                            "./node_modules/select2/dist/js/i18n/es.js",
                            "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.js"
                        ]
                    },
                    {
                        dest: targetDir+"/dist/locales/fr.js",
                        src: [
                            "./node_modules/select2/dist/js/i18n/fr.js",
                            "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.js"
                        ]
                    },
                    {
                        dest: targetDir+"/dist/locales/it.js",
                        src: [
                            "./node_modules/select2/dist/js/i18n/it.js",
                            "./node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.it.js"
                        ]
                    },
                    // locales end

                    // bootstrap dialog
                    {
                        src: [
                            './node_modules/bootstrap3-dialog/dist/js/bootstrap-dialog.js'
                        ],
                        dest: targetDir+"/dist/js/bootstrap-dialog.js"
                    }
                ]
            },
        },
        uglify: {
            core: {
                options: {
                    "compress": true,
                },
                files: [
                    {
                        dest: targetDir+'/dist/js/core.min.js',
                        src: targetDir+'/dist/js/core.js',
                    },
                    {
                        dest: targetDir+'/dist/js/bootstrap-dialog.min.js',
                        src: targetDir+'/dist/js/bootstrap-dialog.js'
                    },
                ]
            },
        },
        cssmin: {
            core: {
                files: [
                    {
                        dest: targetDir+'/dist/css/core.min.css',
                        src: targetDir+'/dist/css/core.css'
                    }
                ]
            }
        }
    });

    grunt.registerTask('yawik:core:dev',["copy","less","concat"]);
    grunt.registerTask('yawik:core',["copy:core","less:core","concat:core", 'cssmin:core',"uglify:core"]);
};
