module.exports = function(grunt) {
	grunt.initConfig({
		// extract pot file from handlebars templates
		jspot: {
			all: {
				options: {
					keyword: 'i18n'
				},
				files: {
					'Locale': ['Plugin/BlueUpload/View/Elements/*.hbs', 'webroot/js/karolinum.js']
				}
			}
		},
		// convert translated po files into json
		po2json: {
			all: {
				options: {
					format: 'jed',
					pretty: true
				},
				src: ['Locale/**/messages.po'],
				dest: 'webroot/js/'
			}
		},
		// load i18n json data into [window.]App.messages
		json: {
			all: {
				options: {
					namespace: 'App',
					includePath: false,
					processName: function(filename) {
						return filename.toLowerCase();
					}
				},
				src: ['webroot/js/*.json'],
				dest: 'webroot/js/messages.js'
			}
		},
		// precompile handlebars templates into [window.]App.render
		handlebars: {
			all: {
				options: {
					namespace: 'App.render',
					processName: function(filePath) {
						return filePath
							.replace(/^Plugin\/BlueUpload\/View\/Elements\//, 'BlueUpload/')
							.replace(/^View\/Users\//, 'Users/')
							.replace(/^View\/Bookings\//, 'Bookings/')
							.replace(/\.hbs$/, '');
					}
				},
				files: {
					'webroot/js/templates.js': ['Plugin/BlueUpload/View/Elements/*.hbs', 'View/**/*.hbs']
				}
			}
		},
		// concatenate all js & css files
		concat: {
			js: {
				src: [
					'Vendor/jquery/dist/jquery.js',
					'Vendor/bootstrap/dist/js/bootstrap.js',
					'Vendor/moment/moment.js',
					//'Vendor/moment/locale/cs.js',
					'Vendor/fullcalendar/dist/fullcalendar.js',
					'Vendor/fullcalendar/dist/lang/cs.js',
					'Vendor/html5-history-api/history.iegte8.js',
					'Vendor/fullcalendar-history/fullcalendar.history.js',
					'Vendor/jquery.cookie/jquery.cookie.js',
					'Vendor/momentjs-business/momentjs-business.js',
					'Vendor/eonasdan-bootstrap-datetimepicker/src/js/bootstrap-datetimepicker.js',
					'Vendor/tinycolorpicker/lib/jquery.tinycolorpicker.js',
					'Vendor/fluidbox/jquery.fluidbox.js',
					'Vendor/blueimp-file-upload/js/vendor/jquery.ui.widget.js',
					'Vendor/blueimp-file-upload/js/jquery.iframe-transport.js',
					'Vendor/blueimp-file-upload/js/jquery.fileupload.js',
					'Plugin/BlueUpload/webroot/blueupload.js',
					'Vendor/html.sortable/dist/html.sortable.js',
					'webroot/css/cunistyle/scroll-to-top.js',
					'Vendor/typeahead.js/dist/typeahead.jquery.js',
					//'Vendor/wysihtml5x/dist/wysihtml5x-toolbar.js',
					//'Vendor/wysihtml5x/dist/wysihtml5x.js',
					'Vendor/Jed/jed.js',
					'node_modules/grunt-contrib-handlebars/node_modules/handlebars/dist/handlebars.runtime.js',
					'webroot/js/templates.js',
					'webroot/js/messages.js',
					'webroot/js/karolinum.js'
				],
				dest: 'webroot/js/site.js',
				nonull: true,
				options: {
					separator: ';\n'
				}
			},
			css: {
				src: [
					'Vendor/notes/style.css',
					'Vendor/components-font-awesome/css/font-awesome.css',
					'Plugin/BlueUpload/webroot/blueupload.css',
					'Vendor/tinycolorpicker/lib/jquery.tinycolorpicker.css',
					'Vendor/fluidbox/css/fluidbox.css',
					'Vendor/blueimp-file-upload/css/jquery.fileupload.css',
					'Vendor/fullcalendar/dist/fullcalendar.css',
					'Vendor/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
					'webroot/css/karolinum.css' // includes customized bootstrap
				],
				dest: 'webroot/css/site.css',
				nonull: true,
				options: {
					separator: '\n'
				}
			}
		},
		// compile less into css
		less: {
			development: {
				options: {
					compress: true
				},
				files: {
					'webroot/css/karolinum.css':'webroot/css/karolinum.less',
				}
			}
		},
		// minifications
		uglify: {
			options: {
				mangle: false
			},
			site: {
				files: {
					'webroot/js/site.js': 'webroot/js/site.js'
				}
			}
		},
		cssmin: {
			site: {
				files: {
					'webroot/css/site.css': 'webroot/css/site.css'
				}
			}
		},
		// tests
		phpunit: {
				// cakephp2 has not suitable bootstrap for phpunit.xml
				// (cakephp3 fixes this)
				// so we resort to running the cake wrapper instead
			  cases: {
					dir: 'AllTests'
				},
				options: {
					bin: 'Vendor/bin/cake test app',
				}
		},
		watch: {
			stylesheets: {
				files: ['<%= concat.css.src %>', 'webroot/**/*.less'],
				tasks: ['stylesheets']
			},
			scripts: {
				files: ['Locale/**/messages.po', '<%= concat.js.src %>', 'Plugin/BlueUpload/View/Elements/*.hbs', 'View/**/*.hbs'],
				tasks: ['scripts']
			},
			grunt: {
				files: ['Gruntfile.js'],
				tasks: ['default']
			}
		}
	});

	// Plugin loading
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-phpunit');
	grunt.loadNpmTasks('grunt-contrib-handlebars');
	grunt.loadNpmTasks('grunt-jspot');
	grunt.loadNpmTasks('grunt-po2json');
	grunt.loadNpmTasks('grunt-json');

	// Task definition
	grunt.registerTask('default', ['scripts', 'stylesheets']);
	grunt.registerTask('locales', ['po2json', 'json']);
	grunt.registerTask('stylesheets', ['less', 'concat:css', 'cssmin']);
	//grunt.registerTask('scripts', ['locales', 'handlebars', 'concat:js', 'uglify']);
	// uglify on direct invocation only as it is slow
	grunt.registerTask('scripts-min', ['locales', 'handlebars', 'concat:js', 'uglify']);
	grunt.registerTask('scripts', ['locales', 'handlebars', 'concat:js']);
};
