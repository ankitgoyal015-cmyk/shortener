<!DOCTYPE html>
<html lang="en" ng-app="shortApp">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - URL Shortener</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.3/angular.min.js"></script>
</head>
<body ng-controller="RegisterCtrl">

<form ng-submit="register()">
    <h2>Register</h2>
    <input type="text" ng-model="user.name" placeholder="Full Name" required>
    <input type="email" ng-model="user.email" placeholder="Email" required>
    <input type="password" ng-model="user.password" placeholder="Password" required>
    <input type="password" ng-model="user.password_confirmation" placeholder="Confirm Password" required>
    <button type="submit">Register</button>
    <div style="color:red;" ng-if="error">@{{ error }}</div>
</form>

<script>
angular.module('shortApp', [])
.controller('RegisterCtrl', ['$scope', '$http', function($scope, $http){
    $scope.user = {};
    $scope.baseUrl = '{{ url("/") }}';
    $http.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    $scope.register = function(){
        $http.post($scope.baseUrl + '/register', $scope.user)
        .then(function(res){
            window.location.href = $scope.baseUrl + '/dashboard';
        })
        .catch(function(err){
            $scope.error = err.data.message || 'Registration failed';
        });
    };
}]);
</script>
</body>
</html>
