<!DOCTYPE html>
<html lang="en" ng-app="shortApp">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - URL Shortener</title>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.3/angular.min.js"></script>
    <style>
        body { font-family: Arial; background: #f6f6f6; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 0 5px rgba(0,0,0,0.2); width: 320px; }
        input { width: 100%; padding: 8px; margin: 6px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background: #3b82f6; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2563eb; }
        .error { color: red; font-size: 13px; margin-top: 5px; }
    </style>
</head>
<body ng-controller="LoginCtrl">

<form ng-submit="login()">
    <h2 style="text-align:center;">Login</h2>
    <input type="email" ng-model="credentials.email" placeholder="Email" required>
    <input type="password" ng-model="credentials.password" placeholder="Password" required>
    <button type="submit">Login</button>
    <div class="error" ng-if="error">@{{ error }}</div>
</form>

<script>
angular.module('shortApp', [])
.controller('LoginCtrl', ['$scope', '$http', function($scope, $http){
    $scope.credentials = {};
    $scope.error = null;
    $scope.baseUrl = '{{ url("/") }}';

    $http.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    $scope.login = function(){
        $http.post($scope.baseUrl + '/login', $scope.credentials)
        .then(function(res){
            window.location.href = $scope.baseUrl + '/dashboard';
        })
        .catch(function(err){
            $scope.error = err.data.message || 'Invalid credentials';
        });
    };
}]);
</script>
</body>
</html>