module.exports = function( grunt ) {
	'use strict';

	// Load all grunt tasks
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	// Project configuration
	grunt.initConfig( {
		pkg:    grunt.file.readJSON( 'package.json' ),
		cssmin: {
			all: {
				src: [
					'css/libs/bootstrap-grid.css',
					'css/base.css',
					'css/layout.css',
					'css/module.css',
					'css/state.css',
					'css/theme.css'
				],
				dest: 'css/style.min.css',
				ext: '.min.css'
			}
		},
		uglify: {
			all: {
				options: {
					preserveComments: 'some',
					mangle: {
						except: ['jQuery']
					}
				},
				src: [
					'js/libs/skrollr.min.js',
					'js/libs/skrollr.menu.min.js',
					'js/plugins.js',
					'js/libs/jquery.slabtext.js',
					'js/main.js'
				],
				dest: 'js/script.min.js',
				mangle: {
					except: ['jQuery']
				}
			}
		},
		md5: {
			options: {
				keepBasename: false,
				afterEach: function (fileChange, options) {
					var type = fileChange.newPath.indexOf('css/') >= 0 ? 'css' : 'js';
					var shortHash = fileChange.newPath.replace(type+'/', '').substr(0,8);
					grunt.file.copy(fileChange.newPath, type+'/'+shortHash+'.min.'+type);
					grunt.log.writeln('File \''+type+'/'+shortHash+'.min.'+type+'\' created.');
					grunt.file.delete(fileChange.newPath);
					grunt.file.delete(fileChange.oldPath);
					grunt.file.write('build/'+type+'build.php', "<?php define('"+type.toUpperCase()+"BUILD', '"+shortHash+"'); ?>");
					grunt.log.writeln('File \'build/'+type+'build.php\' updated.');
				}
			},
			css: {
				files: {
					'css/': 'css/style.min.css'
				}
			},
			js: {
				files: {
					'js/': 'js/script.min.js'
				}
			}
		},
		clean: {
			css: ['css/*.min.css', 'build/cssbuild.php'],
			js: ['js/*.min.js', 'build/jsbuild.php']
		}
	});

	// Build task
	grunt.registerTask( 'build-css',   [ 'clean:css', 'cssmin', 'md5:css' ] );
	grunt.registerTask( 'build-js',   [ 'clean:js', 'uglify', 'md5:js' ] );
	grunt.registerTask( 'build',   [ 'build-css', 'build-js' ] );
	grunt.registerTask( 'default', [ 'build' ]);

	grunt.util.linefeed = '\n';
};
