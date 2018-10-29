module.exports = function(grunt) {
    grunt.initConfig({
        copy: {
            main: {
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
                ]
            }
        },
        less: {
            options: {
                modifyVars: {
                    "fa-font-path": "fonts",
                    "flag-icon-css-path": "flags"
                }
            },
            dev: {
                options: {
                    compress: false,
                },
                files: {
                    "public/dist/core.css": "public/modules/Core/less/yawik.less" // destination file and source file
                }
            },
            prod: {
                options: {
                    compress: true,
                    optimization: 2
                },
                files: {
                    "public/dist/core.min.css": "public/modules/Core/less/yawik.less" // destination file and source file
                }
            }
        },
        concat: {
            options: {
                //compress: false,
                //beautify: true,
            },
            dist: {
                src: [
                    "./node_modules/jquery/dist/jquery.js",
                    "./node_modules/jquery-migrate/dist/jquery-migrate.js",
                    "./node_modules/bootstrap/dist/js/bootstrap.js",
                    "./node_modules/pnotify/dist/pnotify.buttons.js",
                    "./node_modules/select2/dist/js/select2.js",
                    "./node_modules/blueimp-file-upload/js/vendor/jquery.ui.widget.js",
                    "./node_modules/blueimp-file-upload/js/jquery.iframe-transport.js",
                    "./node_modules/blueimp-file-upload/js/jquery.fileupload.js",
                    "./node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.js",
                    "./node_modules/twitter-bootstrap-wizard/jquery.bootstrap.wizard.js",
                    "./public/modules/Core/js/core.init.js"
                ],
                dest: "public/dist/core.min.js"
            }
        }
    });

    grunt.registerTask('yawik:core',["copy","less:prod","concat"]);
};
