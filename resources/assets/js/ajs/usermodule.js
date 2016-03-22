var app = angular.module('usuariosRecord', [])
  .config(['$interpolateProvider', function ($interpolateProvider) {
      $interpolateProvider.startSymbol('<%');
      $interpolateProvider.endSymbol('%>');
}]);


app.controller('usuariosController', ['$scope', '$http',
  function ($scope, $http) {
    $scope.usuarios = [];
    $http.get("/usuario").success(function(data) {
        console.log(data);
        $scope.usuarios = data;
    });
    $scope.avatarPathSmall = function( avatar ){
        if ( avatar === null ){
            return window.location.origin + '/users/avatar/000-default-70px.jpg';
        } else{
            return window.location.origin  + '/' + avatar + '70px.jpg';
        }
	};

    $scope.userEditLink = function( user ){
        return window.location.origin + '/usuario/' + user + '/edit';
	};

    $scope.userShowLink = function( user ){
        return window.location.origin + '/usuario/' + user;
	};

    }]
);
