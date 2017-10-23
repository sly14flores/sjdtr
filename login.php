<!DOCTYPE html>
<html lang="en">
  
<head>
<meta charset="utf-8">
<title>Login - PGLU DTR System</title>
<link rel="icon" href="favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes"> 
    
<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css" />

<link href="css/font-awesome.css" rel="stylesheet">
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600" rel="stylesheet">
    
<link href="css/style.css" rel="stylesheet" type="text/css">
<link href="css/pages/signin.css" rel="stylesheet" type="text/css">

<style type="text/css">

	#logo {
		
		width: 50px;
		
	}
	
	.login-footer {
		
		width: 100%;
		position: absolute;
		left: 0;
		bottom: 0;
		overflow: auto;
		margin: 0!important;
		
	}
	
	.login-footer .footer-inner {
		
		background-color: rgba(0, 0, 0, 0.88);
		
	}

</style>

</head>

<body ng-app="login" ng-controller="loginCtrl">
	
	<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<a class="brand" href="index.php" style="margin: 0!important; padding: 0!important;">
				<img id="logo" src="img/logo.png">
				DTR System				
			</a>		
			
			<!--<div class="nav-collapse">
				<ul class="nav pull-right">
					
					<li class="">						
						<a href="javascript:;" class="">
							Don't have an account?
						</a>
						
					</li>
					
					<li class="">						
						<a href="javascript:;" class="">
							<i class="icon-chevron-left"></i>
							Back to Homepage
						</a>
						
					</li>
				</ul>
				
			</div><!--/.nav-collapse -->	
	
		</div> <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->



<div class="account-container">
	
	<div class="content clearfix">
		
		<form ng-submit="login()" autocomplelte="off">
		
			<h1>Admin Login</h1>
			
			<div class="login-fields">
				
				<p>Please provide your details</p>
				
				<div class="field">
					<label for="username">Username</label>
					<input type="text" name="username" placeholder="Username" class="login username-field" ng-model="account.username" autofocus>
				</div> <!-- /field -->
				
				<div class="field">
					<label for="password">Password:</label>
					<input type="password" name="password" placeholder="Password" class="login password-field" ng-model="account.password">
				</div> <!-- /password -->
				
				<div class="alert alert-danger" role="alert" ng-show="views.incorrect">
				  <span class="sr-only">Error:</span>
				  Invalid username or password
				</div>				
				
			</div> <!-- /login-fields -->
			
			<div class="login-actions">
				
				<!--<span class="login-checkbox">
					<input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
					<label class="choice" for="Field">Keep me signed in</label>
				</span>-->
									
				<button type="submit" class="button btn btn-primary btn-large">Sign In</button>
				
			</div> <!-- .actions -->
			
		</form>
		
	</div> <!-- /content -->
	
</div> <!-- /account-container -->

<!--<div class="login-extra">
	<a href="#">Reset Password</a>
</div> <!-- /login-extra -->

<div class="footer login-footer">
  <div class="footer-inner">
    <div class="container">
      <div class="row">
        <div class="span12"> &copy; 2016 PGLU-MISD </div>
        <!-- /span12 --> 
      </div>
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /footer-inner --> 
</div>
<!-- /footer --> 

<script src="angularjs/angular.min.js"></script>
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="controllers/login.js"></script>

</body>

</html>
