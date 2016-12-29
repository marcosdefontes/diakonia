var app = angular.module('musicaStaffRecord', ['ngMessages','ngSanitize','remoteValidation','ui.select'])
  .config(['$interpolateProvider', function ($interpolateProvider) {
      $interpolateProvider.startSymbol('<%');
      $interpolateProvider.endSymbol('%>')


}]);

app.directive("toggleClass", function() {
  return {
    link: function($scope, element, attr) {
      element.on("click", function() {
        element.toggleClass("option-selected");
      });
    }
  }
});

app.controller('musicaEventoCreateCtrl', ['$scope', '$http', '$location', '$timeout',
  function ($scope, $http,$location,$timeout) {
    $scope.servicos = {};
    $scope.staffPorServico = [];
    $scope.staffSelecionado = {};

    var chamadaServicos = $http.get("/musica/servicos");


    chamadaServicos.then( function (response){
        $scope.servicos = response.data;

        for( i=0 ; i < $scope.servicos.length ; i++ ){
            $scope.servicos[i]['staffDisponivel'] = [];
            //$scope.staffSelecionado[i]['staffSelecionado'] = [];
        }
    }).then( function(){
        for( i=0 ; i < $scope.servicos.length ; i++ ){
           (function( id ){
               var url = "/musica/servicos/" + id + "/staff";
               console.log(url);
               $timeout(function () {
                   $http.get(url).then( function(response){
                       $scope.staffPorServico[id] = response.data;
                       $scope.staffSelecionado[id] = [];
                   });
               }, Math.floor(Math.random() * (800)) + 200);

           })($scope.servicos[i]['id']);

        }
    });



    $scope.serviceIcon = function( url ){
        //console.log(url);
        return window.location.origin + '/' + url;
	};

    $scope.avatar = function( url ){
        if( !url ){
            return window.location.origin + '/users/avatar/000-default-70px.jpg';
        } else{
            return window.location.origin + '/' + url + '70px.jpg';
        }
    }

    $scope.selectStaff = function( service, staff ){
        if( !service || !staff ) return;

        var idx  = $scope.staffSelecionado[service].indexOf( staff );
        if( idx  < 0 ){
            $scope.staffSelecionado[service].push( staff );

        } else{
            $scope.staffSelecionado[service].splice( idx, 1);
        }
    }


  }
]);
