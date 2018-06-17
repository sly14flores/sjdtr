<?php

require_once 'authentication.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Dashboard - PGLU DTR System</title>
<link rel="icon" href="favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="yes">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bootstrap-responsive.min.css" rel="stylesheet">
<!-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600"
        rel="stylesheet"> -->
<link href="css/font-awesome.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
<link href="css/pages/dashboard.css" rel="stylesheet">
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<style type="text/css">

	#logo {
		
		width: 50px;
		margin-right: 5px;
		
	}
	
	.upload {
		
		padding: 15px;
		
	}
	
	.console {
		
		font-family: 'Lucida Console';
		font-size: 14px;		
		background-color: #282828;
		height: 285px;
		padding-left: 5px;
		overflow: auto;
		padding-top: 5px;
		padding-bottom: 5px;
		
	}
	
	.console .success-response {
		
		display: block;
		margin: 0!important;
		padding: 0!important;
		line-height: 18px!important;
		color: #17b53c;
		
	}
	
	.console .info-response {
		
		display: block;
		margin: 0!important;
		padding: 0!important;
		line-height: 18px!important;		
		color: #1ec9c3;
		
	}		
	
	.console .error-response {
		
		display: block;
		margin: 0!important;
		padding: 0!important;
		line-height: 18px!important;		
		color: #ef2f2f;
		
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
<body ng-app="dashboard" ng-controller="dashboardCtrl">
<div class="navbar navbar-fixed-top">
  <div class="navbar-inner">
    <div class="container"> <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse"><span
                    class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span> </a><a class="brand" href="index.php" style="margin: 0!important; padding: 0!important;"><img id="logo" src="img/logo.png">DTR System</a>
      <div class="nav-collapse">
	  
        <ul class="nav pull-right">
          <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i><b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="javascript:;">Settings</a></li>
              <li><a href="javascript:;" logout>Logout</a></li>
            </ul>
          </li>
        </ul>
		
      </div>
      <!--/.nav-collapse --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /navbar-inner --> 
</div>
<!-- /navbar -->

<div class="subnavbar">
  <div class="subnavbar-inner">
    <div class="container">
      <ul class="mainnav">
        <li class="active"><a href="index.php"><i class="icon-dashboard"></i><span>Dashboard</span> </a> </li>
        <li><a href="employees.php"><i class="icon-group"></i><span>Employees</span> </a> </li>
        <li><a href="schedules.php"><i class="icon-calendar"></i><span>Schedules</span> </a> </li>		
      </ul>
    </div>
    <!-- /container --> 
  </div>
  <!-- /subnavbar-inner --> 
</div>
<!-- /subnavbar -->

<div class="main">
  <div class="main-inner">
    <div class="container">
      <div class="row">
		<div class="span5">
			<div cl ass="widget widget-nopad">
				<div class="widget-header"> <i class="icon-upload"></i>
				  <h3>Upload Logs</h3>
				</div>
				<div class="widget-content upload">
					<div class="control-group">				
						<h3 style="margin-bottom: 5px;">Date</h3>
						<strong>From:&nbsp;</strong><input type="date" class="span2" ng-model="filter.dateFrom">
						<strong>To:&nbsp;</strong><input type="date" class="span2" ng-model="filter.dateTo">
					</div>
					<div class="control-group">					
						<h3 style="margin-bottom: 5px;">ID</h3>
						<strong>From:&nbsp;</strong><input type="text" class="span2" ng-model="filter.idFrom">
						<strong>To:&nbsp;</strong><input type="text" class="span2" ng-model="filter.idTo">
					</div>
					<div class="control-group">
						<h3 style="margin-bottom: 5px;">Select how to import</h3>					
						<select class="span4" ng-model="views.howToImport" ng-change="appService.howToImport(this)">
							<!--<option value="preuploaded">Import logs from pre-uploaded file</option>-->
							<option value="upload">Upload log file</option>
						</select>
					</div>					
					<div class="control-group" ng-show="views.showPreUploadedOpt">
						<h3 style="margin-bottom: 5px;">Import logs from pre-uploaded file</h3>					
						<select ng-model="views.prefile" class="span2" ng-disabled="views.usePreviousFile && views.howToImport == 'upload'">
							<option value="dat">Text Files</option>
							<option value="mdb">Network File</option>
						</select>
					</div>
					<div class="control-group" ng-show="views.showUploadOpt">					
						<h3 style="margin-bottom: 5px;">Upload log file</h3>
						<input type="file" name="logFile" id="logFile" file-model="views.logFile" ng-disabled="views.usePreviousFile">
						<div class="checkbox">
							<label>
							  <input type="checkbox" name="usePreviousFile" ng-model="views.usePreviousFile" ng-change="appService.chkPf(views.usePreviousFile)"> Use previously uploaded file {{(views.pf != '')?'(':''}}{{views.pf}}{{(views.pf != '')?')':''}}
							</label>
						</div>						
					</div>					
					<div class="control-group">
						<div class="span4">
							<button class="btn btn-primary pull-right" type="button" ng-click="appService.start(this)" ng-disabled="views.started">Upload</button>
						</div>
					</div>
				</div>
			  </div>	
		</div>
		<div class="span7">
          <div class="widget widget-table action-table">
            <div class="widget-header"> <i class="icon-ellipsis-horizontal"></i>
              <h3>Console {{views.importProgressDetail}}</h3>
            </div>
            <!-- /widget-header -->
            <div class="widget-content">
				<div class="console">
				</div>
            </div>
            <!-- /widget-content --> 
          </div>		
		</div>		
      </div>
      <!-- /row --> 
    </div>
    <!-- /container --> 
  </div>
  <!-- /main-inner --> 
</div>
<!-- /main -->

<div class="footer" ng-class="{'login-footer': views.howToImport == 'preuploaded' || views.howToImport == undefined}">
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

<!-- Le javascript
================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.blockUI.js"></script>
<script src="js/bootstrap-notify-3.1.3/bootstrap-notify.min.js"></script>
<script src="js/bootbox.min.js"></script>

<script src="angularjs/angular.min.js"></script>

<script src="modules/bootstrap-modal.js"></script>
<script src="modules/block-ui.js"></script>
<script src="modules/bootstrap-notify.js"></script>
<script src="modules/account.js"></script>

<script src="controllers/dashboard.js"></script>

</body>
</html>
