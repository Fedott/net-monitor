'use strict';

var gulp       = require('gulp'),
    typescript = require('typescript'),
    ts         = require('gulp-typescript'),
    browserify = require('browserify'),
    source     = require('vinyl-source-stream')
;

var project = ts.createProject('src/tsconfig.json', {typescript: typescript});

gulp.task('through', function () {
    return gulp
        .src(['src/index.html'])
        .pipe(gulp.dest('dist'));
});

gulp.task('css', function () {
    return gulp
        .src(['src/css/*.css'])
        .pipe(gulp.dest('dist/css'));
});

gulp.task('compile', function () {
    var result = gulp
        .src('src/**/*{ts,tsx}')
        .pipe(ts(project));
    return result.js.pipe(gulp.dest('.tmp'));
});

gulp.task('bundle', ['through', 'css', 'compile'], function () {
    var bundle = browserify('.tmp/bootstrap.js');
    return bundle.bundle()
        .pipe(source('bundle.js'))
        .pipe(gulp.dest('dist'));
});
