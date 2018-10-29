global.nodeModulesPath = __dirname + "/node_modules";
module.exports = function(grunt) {
    require('load-grunt-tasks')(grunt);

    grunt.loadTasks('./public/modules/Core');
    grunt.registerTask('default', ['yawik:core']);
    grunt.registerTask('build',['yawik:core']);
};