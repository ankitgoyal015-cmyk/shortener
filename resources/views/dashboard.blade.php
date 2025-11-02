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
<body ng-controller="DashboardCtrl" ng-cloak>

<header>
  <h1>Welcome, @{{ user.name }} (@{{ user.role && user.role.name }})</h1>
  <form id="logoutForm" method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
  </form>
</header>

<main>

  <!-- ========================== -->
  <!-- SUPERADMIN AREA -->
  <!-- ========================== -->
  <div ng-if="user.role.name === 'SuperAdmin'">
    <h2>Company Management</h2>

    <form ng-submit="createCompany()" name="companyForm" novalidate>
    <input type="text"
            ng-model="singledata.name"
            name="name"
            placeholder="Enter New Company Name"
            required
            ng-trim="true">
    <button type="submit">Add Company</button>

    <p class="success" ng-if="message">@{{ message }}</p>
    <p class="error" ng-if="error">@{{ error }}</p>
    </form>


    <table ng-if="companies.length">
      <thead>
        <tr><th>#</th><th>Company Name</th><th>Created By</th><th>Action</th></tr>
      </thead>
      <tbody>
        <tr ng-repeat="c in companies">
          <td>@{{ $index+1 }}</td>
          <td>@{{ c.name }}</td>
          <td>@{{ c.creator ? c.creator.name : 'N/A' }}</td>
          <td><button ng-click="openInvite(c.id, 'Admin')">Invite Admin</button></td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- ========================== -->
  <!-- ADMIN AREA -->
  <!-- ========================== -->
  <div ng-if="user.role.name != 'SuperAdmin'">
    <h2>Your Company</h2>
    <p><strong>@{{ user.company && user.company.name }}</strong></p>
    <button ng-click="openInvite(user.company.id, 'Team')" ng-if="user.role.name === 'Admin'">Invite Sales/Manager</button>
  </div>

  <!-- ========================== -->
  <!-- SALES / MANAGER AREA -->
  <!-- ========================== -->
  <div ng-if="user.role.name != 'SuperAdmin'">
    <h2>Create Short URL</h2>
    <form ng-submit="createShort()">
      <input type="url" ng-model="singledataUrl.original_url" placeholder="Enter Original URL" required>
      <button type="submit">Create Short URL</button>
    </form>
  </div>

  <!-- ========================== -->
  <!-- SHORT URL LIST -->
  <!-- ========================== -->
  <div>
    <h2>Short URLs List</h2>
    <table ng-if="urls.length">
      <thead>
        <tr><th>#</th><th>Short URL</th><th>Original URL</th><th>Created By</th><th>Company</th></tr>
      </thead>
      <tbody>
        <tr ng-repeat="u in urls">
          <td>@{{ $index+1 }}</td>
          <td><a href="{{url('')}}/short-urls/@{{u.short_code}}" target="_blank">@{{ u.short_code }}</a></td>
          <td>@{{ u.original_url }}</td>
          <td>@{{ u.user ? u.user.name : 'N/A' }}</td>
          <td>@{{ u.company ? u.company.name : 'N/A' }}</td>
        </tr>
      </tbody>
    </table>
    <p ng-if="!urls.length">No URLs found for your role.</p>
  </div>

  <!-- ========================== -->
  <!-- INVITATION FORM -->
  <!-- ========================== -->
  <div ng-if="showInviteForm">
    <h2>Send Invitation (@{{ inviteType }})</h2>
    <form ng-submit="sendInvite()">
      <input type="hidden" ng-model="invite.company_id">
      <input type="email" ng-model="invite.email" placeholder="Enter Email" required>
      <div ng-if="inviteType === 'Team'">
        <select ng-model="invite.role_id" required>
          <option value="">Select Role</option>
          <option value="@{{ roles.Sales }}">Sales</option>
          <option value="@{{ roles.Manager }}">Manager</option>
        </select>
      </div>
      <button type="submit">Send Invite</button>
      <button type="button" ng-click="cancelInvite()">Cancel</button>
    </form>
    <p class="success" ng-if="message">@{{ message }}</p>
    <p class="error" ng-if="error">@{{ error }}</p>
  </div>

</main>

<script>
angular.module('shortApp', [])
.config(['$httpProvider', function($httpProvider){
  $httpProvider.defaults.withCredentials = true;
}])
.controller('DashboardCtrl', ['$scope','$http', '$window', function($scope, $http, $window){
  $scope.user = @json($user);
  $scope.singledata = {name:''};
  $scope.singledataUrl = {original_url : ''};
  $scope.companies = [];
  $scope.urls = [];
  $scope.message = '';
  $scope.error = '';
  $scope.showInviteForm = false;
  $scope.inviteType = '';
  $scope.invite = {};
  $scope.baseUrl = '{{ url("/") }}';

  $http.defaults.headers.common['X-CSRF-TOKEN'] =
    document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Load companies for SuperAdmin
  if ($scope.user.role.name === 'SuperAdmin') {
    $scope.loadCompanies = function(){
      $http.get($scope.baseUrl + '/companies').then(res => $scope.companies = res.data);
    };
    $scope.loadCompanies();
  }

  // Create Company
  $scope.createCompany = function(){
    $scope.submitted = 1;
    $scope.message = '';
    $scope.error = '';

    // front-end check
    $http.post($scope.baseUrl + '/companies', $scope.singledata )
      .then(res => { $scope.message = res.data.message; $scope.singledata.name = ''; $scope.submitted = 0; $scope.loadCompanies(); })
      .catch(err => { $scope.error = err.data.message || 'Error creating company'; });
  };

  // Load Short URLs
  $scope.loadUrls = function(){
    $http.get($scope.baseUrl + '/short-urls')
      .then(res => { $scope.urls = res.data; })
      .catch(err => { $scope.error = err.data.message || 'Error loading URLs'; });
  };
  $scope.loadUrls();

  // Create Short URL (Sales/Manager)
  $scope.createShort = function(){
    $scope.message = ''; $scope.error = '';
    $http.post($scope.baseUrl + '/short-urls', $scope.singledataUrl)
      .then(res => { $scope.message = 'Short URL created'; $scope.singledataUrl.original_url = ''; $scope.loadUrls(); })
      .catch(err => { $scope.error = err.data.message || 'Failed to create short URL'; });
  };

  // Invite form toggle
  $scope.openInvite = function(companyId, type){
    $window.location.href = $scope.baseUrl + '/invitations?companyId='+btoa(companyId);
  };

  // Send invite
  $scope.sendInvite = function(){
    $scope.message = ''; $scope.error = '';
    let data = angular.copy($scope.invite);
    if ($scope.inviteType === 'Admin') data.role_id = 2;
    $http.post($scope.baseUrl + '/invitations', data)
      .then(res => { $scope.message = res.data.message; $scope.showInviteForm = false; })
      .catch(err => { $scope.error = err.data.message || 'Invite failed'; });
  };
}]);
</script>
</body>
</html>