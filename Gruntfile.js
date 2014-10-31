module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-shell');

    grunt.initConfig({
        sourceDir: 'src',
        buildDir: 'build',
        shell: {
            clean: {
                command: [
                    'rm -rf <%= buildDir %>/logs',
                    'rm -rf <%= buildDir %>/coverage',
                    'rm -rf <%= buildDir %>/pdepend',
                    'rm -rf <%= buildDir %>/phpdoc',
                    'rm -rf <%= buildDir %>/phpdox',
                    'mkdir -p <%= buildDir %>/logs',
                    'mdkir -p <%= buildDir %>/coverage',
                    'mkdir -p <%= buildDir %>/pdepend',
                    'mkdir -p <%= buildDir %>/phpdoc',
                    'mkdir -p <%= buildDir %>/phpdox'
                ].join('&&')
            },
            phpunit: {
                command: 'phpunit',
                options: {
                    failOnError: true
                }
            },
            pdepend: {
                command: 'pdepend --jdepend-xml=<%= buildDir %>/logs/jdepend.xml --jdepend-chart=<%= buildDir %>/build/pdepend/dependencies.svg --overview-pyramid=<%= buildDir %>/build/pdepend/overview-pyramid.svg <%= sourceDir %>',
                options: {
                    failOnError: false
                }
            },
            phpmd: {
                command: 'phpmd <%= sourceDir %> xml codesize,design,naming,unusedcode --reportfile <%= buildDir %>/logs/pmd.xml',
                options: {
                    failOnError: false
                }
            },
            phpcpd: {
                command: 'phpcpd --log-pmd <%= buildDir %>/logs/pmd-cpd.xml <%= sourceDir %>',
                options: {
                    failOnError: false
                }
            },
            phploc: {
                command: 'phploc --count-tests --log-xml <%= buildDir %>/logs/phploc.xml <%= sourceDir %>',
                options: {
                    failOnError: true
                }
            },
            phpcs: {
                command: 'phpcs --report=checkstyle --report-file=<%= buildDir %>/logs/phpcs.xml --standard=Symfony2 <%= sourceDir %>',
                options: {
                    failOnError: false
                }
            },
            phpdoc: {
                command: 'phpdoc --target <%= buildDir %>/phpdoc --directory <%= sourceDir %>',
                options: {
                    failOnError: true
                }
            },
            phpdox: {
                command: 'phpdox',
                options: {
                    failOnError: true
                }
            }
        }
    });

    grunt.registerTask('default', ['shell:clean', 'shell:phpunit', 'shell:phpmd', 'shell:phpcpd', 'shell:phploc', 'shell:phpcs', 'shell:phpdox']);
    grunt.registerTask('clean', ['shell:clean']);
    grunt.registerTask('phpunit', ['shell:phpunit']);
    grunt.registerTask('pdepend', ['shell:pdepend']);
    grunt.registerTask('phpmd', ['shell:phpmd']);
    grunt.registerTask('phpcpd', ['shell:phpcpd']);
    grunt.registerTask('phploc', ['shell:phploc']);
    grunt.registerTask('phpcs', ['shell:phpcs']);
    grunt.registerTask('phpdoc', ['shell:phpdoc']);
    grunt.registerTask('phpdox', ['shell:phpdox']);
};
