'use strict';
module.exports = function(grunt) {
  
  var jsFileList = [
    'assets/js/plugins/bootstrap/transition.js',
    'assets/js/plugins/bootstrap/alert.js',
    'assets/js/plugins/bootstrap/button.js',
    'assets/js/plugins/bootstrap/carousel.js',
    'assets/js/plugins/bootstrap/collapse.js',
    'assets/js/plugins/bootstrap/dropdown.js',
    'assets/js/plugins/bootstrap/modal.js',
    'assets/js/plugins/bootstrap/tooltip.js',
    'assets/js/plugins/bootstrap/popover.js',
    'assets/js/plugins/bootstrap/scrollspy.js',
    'assets/js/plugins/bootstrap/tab.js',
    'assets/js/plugins/bootstrap/affix.js',
    'assets/js/vendor/jquery-ui.min.js',
    'assets/js/plugins/*.js',
    'assets/js/_*.js'
  ];

  grunt.initConfig({
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: [
        'Gruntfile.js',
        'assets/js/*.js',
        '!assets/js/scripts.js',
        '!assets/js/scripts.min.js'
      ]
    },
    less: {
      dev: {
        files: {
          'assets/css/main.css': [
            'assets/less/app.less'
          ]
        },
        options: {
          compress: false,
          // LESS source map
          // To enable, set sourceMap to true and update sourceMapRootpath based on your install
          sourceMap: true,
          sourceMapFilename: 'assets/css/main.css.map',
          sourceMapRootpath: '/wp-content/themes/alexandervanberge/'
        }
      },
      build: {
        files: {
          'assets/css/main.min.css': [
            'assets/less/app.less'
          ]
        },
        options: {
          compress: true
        }
      }
    },
    concat: {
      options: {
        separator: ';',
      },
      dist: {
        src: [jsFileList],
        dest: 'assets/js/scripts.js',
      },
    },
    uglify: {
      dist: {
        files: {
          'assets/js/scripts.min.js': [jsFileList]
        }
      }
    },
    modernizr: {
      build: {
        devFile: 'assets/vendor/modernizr/modernizr.js',
        outputFile: 'assets/js/vendor/modernizr.min.js',
        files: {
          'src': [
            ['assets/js/scripts.min.js'],
            ['assets/css/main.min.css']
          ]
        },
        extra: {
          shiv: true
        },
        uglify: true,
        parseFiles: true
      }
    },
    version: {
      default: {
        options: {
          format: true,
          length: 32,
          manifest: 'assets/manifest.json',
          querystring: {
            style: 'roots_css',
            script: 'roots_js'
          }
        },
        files: {
          'lib/scripts.php': 'assets/{css,js}/{main,scripts}.min.{css,js}'
        }
      }
    },
    clean: {
      dist: [
        'assets/css/main.min.css',
        'assets/js/scripts.min.js'
      ]
    },
    ftpush: {
      build: {
        auth: {
          host:'ftp.alexandervanberge.nl',
          port:21,
          authKey:'deploy'
        },
        src: '',
        dest: '/wwwroot/wp-content/themes/alexandervanberge',
        exclusions: [
          '**/.DS_Store',
          '**/Thumbs.db',
          '**/node_modules/**',
          '**/.gitignore',
          'assets/less',
          'assets/css/main.css',
          'assets/css/main.css.map',
          'assets/js/_main.js',
          'assets/js/scripts.js',
          'assets/js/plugins',
          '.editorconfig',
          '.ftppass',
          '.git',
          '.grunt',
          '.jshintrc'
        ],
        keep: [],
        simple: true
      }
    },
    watch: {
      less: {
        files: [
          'assets/less/*.less',
          'assets/less/**/*.less'
        ],
        tasks: ['less:dev']
      },
      js: {
        files: [
          jsFileList,
          '<%= jshint.all %>'
        ],
        tasks: ['jshint', 'concat']
      },
      livereload: {
        // Browser live reloading
        // https://github.com/gruntjs/grunt-contrib-watch#live-reloading
        options: {
          livereload: false
        },
        files: [
          'assets/css/main.css',
          'assets/js/scripts.js',
          'templates/*.php',
          '*.php'
        ]
      }
    }
    
  });

  // Load tasks
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-modernizr');
  grunt.loadNpmTasks('grunt-wp-assets');
  grunt.loadNpmTasks('grunt-ftpush');

  // Register tasks
  grunt.registerTask('default', [
    'dev'
  ]);
  grunt.registerTask('dev', [
    'clean',
    'jshint',
    'less:dev'
  ]);
  grunt.registerTask('build', [
    'clean',
    'jshint',
    'less:build',
    'uglify',
    'modernizr',
    'version',
    'ftpush'
  ]);

};
