module.exports = function(grunt) {

    require("load-grunt-tasks")(grunt);

    grunt.initConfig({
        watch: {
            php: {
                files: ["src/**.php", "tests/**.php"],
                tasks: ["phpunit", "notify:phpunit"]
            }
        },
        phpunit: {
            classes: {
                dir: "tests/"
            },
            options: {
                bin: "vendor/bin/phpunit",
                colors: true
            }
        },
        notify: {
            phpunit: {
                options: {
                    title: "PHP Unit Tests Complete",
                    message: "All is well."
                }
            }
        }
    });

    grunt.loadNpmTasks("grunt-notify");

    grunt.registerTask("default", ["watch"]);
};
