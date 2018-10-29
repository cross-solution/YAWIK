
module.exports = function(grunt) {
    require('load-grunt-tasks')(grunt);
    grunt.config.init({
        nodeModulesPath: __dirname + "/node_modules",
        targetDir: './public'
    });
    grunt.loadTasks('./public/modules/Core');
    grunt.registerTask('default', ['yawik:core']);
    grunt.registerTask('build',['yawik:core']);
};