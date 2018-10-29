
global.nodeModulesPath = __dirname + "/node_modules";

module.exports = function(grunt) {
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({
        watch: {
            styles: {
                files: ['module/**/*.less'], // which files to watch
                tasks: ['build:dev'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.registerTask('watch',['watch']);

    grunt.loadTasks('./public/modules/Core');
    grunt.registerTask('default', ['yawik:core']);
    grunt.registerTask('build',['yawik:core']);
};