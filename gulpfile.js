var gulp  = require('gulp');
var util = require('gulp-util');
var expect = require('gulp-expect-file');
var concat = require('gulp-concat');
var cleanCSS = require('gulp-clean-css');
var size = require('gulp-size');
var rm = require('gulp-rimraf');
var uglify = require('gulp-uglify');
var gutil = require('gulp-util');

var paths = {
	'default' : 'resources/assets',
	'bower':'bower_components'
}

var config = {
    production: !!util.env.production
};

gulp.task('ambiente', function(done){
	if( config.production ){
		util.log(util.colors.bgRed(util.colors.white('Ambiente de Produção')));
	} else{
		util.log(util.colors.bgBlue(util.colors.white('Ambiente de Desenvolvimento')));
	}
	done();
});

gulp.task('clean', function() {
    return gulp.src([
		'public/css/*'
		,'public/js/*'
		,'public/fonts/*'
	]).pipe(rm());
});

gulp.task('fonts', function(done){

	gulp.src(paths.bower + '/components-font-awesome/fonts/*')
		.pipe(gulp.dest('public/fonts'));
	done();
});

gulp.task('css', function(done){

	var files = [

		paths.default + '/css/bootstrap-reset.css'
		,paths.default + '/css/bootstrap.css'
		,paths.default + '/css/checkbox3.css'
		,paths.bower + '/components-font-awesome/css/font-awesome.min.css'

		,paths.bower + '/datetimepicker/jquery.datetimepicker.css'
		,paths.bower + '/select2/dist/css/select2.min.css'
		,paths.bower + '/angular-ui-select/dist/select.min.css'
		,paths.bower + '/image-picker/image-picker/image-picker.css'
		,paths.bower + '/toastr/toastr.min.css'
		,paths.bower + '/toolbar/jquery.toolbar.css'

		,paths.default + '/css/main.css'
		,paths.default + '/css/forms.css'
		,paths.default + '/css/ng-tags-input.css'
		,paths.default + '/css/theme.css'
		,paths.default + '/css/login.css'

	];

	gulp.src(files)
		.pipe(expect({ checkRealFile: true, verbose: true },files))
		.pipe(size({showFiles: true, title: "CSS:: "}))
		.pipe(concat('all.min.css'))
		.pipe(config.production ? cleanCSS(): util.noop())
		.pipe(gulp.dest('public/css'));

	var files = [

		paths.default + '/css/bootstrap-reset.css'
		,paths.default + '/css/bootstrap.css'
		,paths.default + '/css/guest.css'
	];
	gulp.src(files)
		.pipe(expect({ checkRealFile: true, verbose: true },files))
		.pipe(size({showFiles: true, title: "CSS:: "}))
		.pipe(concat('guest.min.css'))
		.pipe(config.production ? cleanCSS(): util.noop())
		.pipe(gulp.dest('public/css'));


	done();

});

gulp.task('js', function(done){
	var filesjs = [
		// Definidos
		// AngularJS
		paths.bower + '/angular/angular.min.js'
		,paths.bower + '/ng-tags-input/ng-tags-input.min.js'
		,paths.bower + '/angular-resource/angular-resource.min.js'
		,paths.bower + '/angular-i18n/angular-locale_pt-br.js'
		,paths.bower + '/angular-sanitize/angular-sanitize.min.js'
		,paths.bower + '/angular-messages/angular-messages.min.js'
		,paths.bower + '/angular-bootstrap/ui-bootstrap-tpls.min.js'
		,paths.bower + '/angular-ui-mask/dist/mask.min.js'
		,paths.bower + '/angular-ui-select/dist/select.min.js'
		,paths.bower + '/ng-remote-validate/release/ngRemoteValidate.js'
		// JQuery
		,paths.bower + '/jquery/dist/jquery.min.js'
		,paths.default + '/js/bootstrap.js'
		,paths.bower + '/toastr/toastr.min.js'
		,paths.bower + '/datetimepicker/build/jquery.datetimepicker.full.min.js'
		,paths.bower + '/select2/dist/js/select2.full.min.js'
		,paths.bower + '/select2/dist/js/i18n/pt-BR.js'
		,paths.bower + '/image-picker/image-picker/image-picker.min.js'
		,paths.bower + '/toolbar/jquery.toolbar.js'
		// Javascript
		,paths.bower + '/moment/min/moment.min.js'
		,paths.bower + '/moment/locale/pt-br.js'
		,paths.bower + '/slug/slug.js'
		,paths.default + '/js/app.js'
	];

	gulp.src(filesjs)
		//.pipe(expect({ checkRealFile: true, verbose: true },filesjs))
		.pipe(size({showFiles: true, title: "Javascript:: "}))
		.pipe(concat('app.min.js'))
		.pipe(config.production ? uglify().on('error', function(err) {
gutil.log(gutil.colors.red('[Error]'), err.toString());
this.emit('end');
}): util.noop())
		.pipe(gulp.dest('public/js'));

	done();

});

gulp.task('angular', function(done){
	// Usuários
	gulp.src([
		paths.default + '/js/users/UserModule.js'
	]).pipe(size({showFiles: true, title: "AngularJS (Usuários):"}))
	.pipe(config.production ? uglify(): util.noop())
	.pipe(concat('app-users-module.min.js'))
	.pipe(gulp.dest('public/js/users'));

	gulp.src([
		paths.default + '/js/membro/MembroModule.js'
	]).pipe(size({showFiles: true, title: "AngularJS (Membro):"}))
	.pipe(config.production ? uglify(): util.noop())
	.pipe(concat('app-membro-module.min.js'))
	.pipe(gulp.dest('public/js/membro'));

	done();
});


gulp.task('watch', gulp.series('ambiente', function(){
	gulp.watch(paths.default + '/css/*.css',gulp.series('css'));
    gulp.watch(paths.default + '/js/*.js',gulp.series('js'));
	gulp.watch(paths.default + '/js/**/*.js',gulp.series('angular'));
}));


gulp.task('default',
	gulp.series('ambiente', 'fonts', 'css', 'js', 'angular')

);
