<!DOCTYPE html>
<html lang="en" ng-app="shortApp">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard - URL Shortener</title>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.3/angular.min.js"></script>
  <style>
    body { font-family: Arial; background: #f9fafb; margin: 0; }
    header { background: #2563eb; color: #fff; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
    main { padding: 20px; }
    h2 { color: #2563eb; margin-top: 20px; }
    form, table { background: #fff; padding: 15px; margin-top: 15px; border-radius: 6px; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
    input, select, button { padding: 8px; margin: 5px 0; }
    button { background: #3b82f6; border: none; color: #fff; border-radius: 4px; cursor: pointer; }
    button:hover { background: #2563eb; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #e5e7eb; }
    .success { color: green; margin-top: 10px; }
    .error { color: red; margin-top: 10px; }
  </style>
</head>

<body ng-controller="InvitationCtrl" ng-cloak>

<header>
  <h1>Welcome, @{{ user.name }} (@{{ user.role && user.role.name }})</h1>
  <form id="logoutForm" method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
  </form>
</header>

<main>

  <h2 class="mb-3">Invitations ({{$company->name}})</h2>

  <form ng-submit="createInvitation()" novalidate>
    <input type="text" ng-model="invitation.name" placeholder="Full Name" required>
    <input type="email" ng-model="invitation.email" placeholder="Email" required>

    <select ng-model="invitation.role" required>
      <option value="">Select Role</option>
      <option ng-value="role.id" ng-repeat="role in roles">@{{role.name}}</option>
    </select>

    <button type="submit">Invite User</button>
  </form>

  <p style="color:green">@{{ message }}</p>
  <p style="color:red">@{{ error }}</p>

  <hr>
  <h3>All Invitations</h3>

  <table border="1" width="100%">
    <thead>
      <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th>Invited By</th>
      </tr>
    </thead>
    <tbody>
      <tr ng-repeat="invite in invites">
        <td>@{{ $index + 1 }}</td>
        <td>@{{ invite.name }}</td>
        <td>@{{ invite.email }}</td>
        <td>@{{ invite.role}}</td>
        <td>@{{ invite.status }}</td>
        <td>@{{ invite.invited_by}}</td>
      </tr>
    </tbody>
  </table>
  <a href="{{url('')}}/dashboard"><button type="button">Back</button>

</main>

<script>
angular.module('shortApp', [])
.controller('InvitationCtrl', ['$scope', '$http', '$window', function($scope, $http, $window) {

  $scope.invitation = {};
  $scope.invites = [];
  $scope.message = '';
  $scope.error = '';
  $scope.user = @json($user);
  $scope.roles = @json($role);
  $scope.baseUrl = '{{ url("/") }}';

  const encodedCompanyId = getParameterByName('companyId');
  $scope.companyId = encodedCompanyId;

  $scope.decodedCompanyId = atob(encodedCompanyId);

  // Load all invitations
  $scope.loadInvites = function() {
    $http.get($scope.baseUrl + '/api/invitations?companyId='+$scope.companyId)
      .then(res => { $scope.invites = res.data; })
      .catch(err => { $scope.error = err.data.message || 'Error loading invitations'; });
  };

  $scope.loadInvites();

  // now you can use $scope.decodedCompanyId in POST requests
  $scope.invitation = { company_id: $scope.decodedCompanyId };

  $scope.createInvitation = function() {
    const data = {
      name: $scope.invitation.name,
      email: $scope.invitation.email,
      role_id: $scope.invitation.role,
      company_id: $scope.decodedCompanyId,
    };

    $http.post($scope.baseUrl + '/api/invitations', data)
      .then(res => {
        $scope.message = res.data.message;
        $scope.invitation = {};
        $scope.loadInvites();
      })
      .catch(err => {
        $scope.error = err.data.message || 'Error creating invitation';
      });
  };

  function getParameterByName(name) {
    const url = $window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
    const results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

}]);
</script>
</body>
</html>