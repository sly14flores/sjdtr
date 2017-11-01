<?php

require_once 'authentication.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Schedules - PGLU DTR System</title>
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
<body ng-app="schedules" ng-controller="schedulesCtrl">
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
        <li><a href="index.php"><i class="icon-dashboard"></i><span>Dashboard</span> </a> </li>
        <li><a href="employees.php"><i class="icon-group"></i><span>Employees</span> </a> </li>
        <li class="active"><a href="schedules.php"><i class="icon-calendar"></i><span>Schedules</span> </a> </li>
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
		<div class="span4">
          <div class="widget widget-table action-table">
            <div class="widget-header"> <i class="icon-calendar"></i>
              <h3>Schedules</h3><button class="btn btn-primary btn-xs pull-right" type="button" style="margin-top: 6px; margin-right: 10px;" ng-click="appService.add(this)" ng-disabled="controls.schedule.addBtn">Add</button>
            </div>
            <!-- /widget-header -->
            <div class="widget-content">
				
				<div class="controls" style="margin: 10px 0 10px 10px;">
					<strong>Search:&nbsp;</strong><input type="text" class="span3" ng-model="q">
				</div>
			
              <table class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>				
                  <tr dir-paginate="schedule_row in schedules | filter: q | itemsPerPage: pageSize" current-page="currentPage" style="cursor: pointer;" ng-click="appService.view(this)">
                    <td> {{schedule_row.id}} </td>
                    <td> {{schedule_row.description}} </td>
                  </tr>                
                </tbody>
              </table>
			  <dir-pagination-controls template-url="angularjs/utils/pagination/dirPagination.tpl.html"></dir-pagination-controls>
            </div>
            <!-- /widget-content --> 
          </div>	
		</div>
		<div class="span8">
          <div class="widget">
            <div class="widget-header"> <i class="icon-calendar"></i>
              <h3></h3>
            </div>
            <!-- /widget-header -->
            <div class="widget-content">
              <div class="widget big-stats-container">
                <div class="widget-content">
				
					<div class="pull-right"><a href="javascript:;" class="btn btn-small btn-success" ng-disabled="controls.schedule.editBtn" ng-click="appService.edit(this)"><i class="btn-icon-only icon-edit"> </i></a>&nbsp;&nbsp;<a href="javascript:;" class="btn btn-danger btn-small" ng-disabled="controls.schedule.delBtn" ng-click="appService.confirmDel(this)"><i class="btn-icon-only icon-remove"> </i></a></div>
					<div style="clear: both; padding-top: 15px;"></div>
					<hr>
					<!--<div class="alert alert-info">
					  <button type="button" class="close" data-dismiss="alert">×</button>
					  <strong>Note!</strong> If transcending is set to Yes you only need to provide Morning In and Morning Out
					</div>					-->
					<form name="frmHolder.schedule" autocomplete="off" novalidate>
						<fieldset>						
							<div class="row">
								<div class="span4">
									<div class="control-group" ng-class="{'error': frmHolder.schedule.description.$invalid && frmHolder.schedule.description.$touched}">
										<label><h2>Description</h2></label>
										<div class="controls">
											<input type="text" class="span4" name="description" ng-model="schedule.description" ng-disabled="controls.schedule.description" required>
										</div>
									</div>
								</div>
								<div class="span2">
									<div class="control-group">
										<label><h2>Flexible?</h2></label>
										<div class="controls">
											<select class="span2" ng-model="schedule.flexible" ng-disabled="controls.schedule.flexible">
												<option value="No">No</option>
												<option value="Yes">Yes</option>
											</select>
										</div>
									</div>
								</div>								
							</div>
							<div ng-repeat="detail in schedule.details">
							<h3 style="margin-bottom: 5px;">{{detail.day}}</h3>
							<hr>
							<a href="javascript:;" class="btn btn-small btn-default pull-right" ng-click="appService.clone(this)" ng-show="$index>0"><i class="btn-icon-only icon-copy"> </i></a>
							<div class="row">
								<div class="span2">
									<div class="control-group">
										<label><strong>Morning In</strong></label>
										<div class="controls">
											<input type="time" class="span2" name="morning_in" ng-model="detail.morning_in" ng-disabled="controls.schedule.morning_in">
										</div>
									</div>
								</div>
								<div class="span2">
									<div class="control-group">
										<label><strong>Morning Cutoff</strong></label>
										<div class="controls">
											<input type="time" class="span2" name="morning_cutoff" ng-model="detail.morning_cutoff" ng-disabled="controls.schedule.morning_cutoff">
										</div>
									</div>
								</div>								
								<div class="span2">
									<div class="control-group">
										<label><strong>Morning Out</strong></label>
										<div class="controls">
											<input type="time" class="span2" name="morning_out" ng-model="detail.morning_out" ng-disabled="controls.schedule.morning_out">
										</div>
									</div>
								</div>								
							</div>
							<div class="row">
								<div class="span2">
									<div class="control-group">
										<label><strong>Lunch Break Cutoff</strong></label>
										<div class="controls">
											<input type="time" class="span2" name="lunch_break_cutoff" ng-model="detail.lunch_break_cutoff" ng-disabled="controls.schedule.lunch_break_cutoff">
										</div>
									</div>
								</div>							
							</div>
							<div class="row">						
								<div class="span2">
									<div class="control-group">
										<label><strong>Afternoon In</strong></label>
										<div class="controls">
											<input type="time" class="span2" name="afternoon_in" ng-model="detail.afternoon_in" ng-disabled="controls.schedule.afternoon_in">
										</div>
									</div>
								</div>
								<div class="span2">
									<div class="control-group">
										<label><strong>Afternoon Cutoff</strong></label>
										<div class="controls">
											<input type="time" class="span2" name="afternoon_cutoff" ng-model="detail.afternoon_cutoff" ng-disabled="controls.schedule.afternoon_cutoff">
										</div>
									</div>
								</div>									
								<div class="span2">
									<div class="control-group">
										<label><strong>Afternoon Out</strong></label>
										<div class="controls">
											<input type="time" class="span2" name="afternoon_out" ng-model="detail.afternoon_out" ng-disabled="controls.schedule.afternoon_out">
										</div>
									</div>
								</div>
							</div>						
						</fieldset>
						</div>
						<div class="row">
							<div class="span6">
								<div class="form-actions">
									<button type="button" class="btn btn-primary" ng-disabled="controls.schedule.saveBtn" ng-click="appService.update(this)">{{views.addUpdateTxt}}</button> 
									<button class="btn" ng-disabled="controls.schedule.cancelBtn" ng-click="appService.cancel(this)">{{views.cancelCloseTxt}}</button>
								</div>
							</div>
						</div>						
					</form>
				
                </div>
                <!-- /widget-content -->                 
              </div>
            </div>
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

<div class="footer">
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

<div id="confirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="label-confirm">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="label-confirm">Modal title</h4>
	  </div>
	  <div class="modal-body">
		<p>One fine body&hellip;</p>
	  </div>
	  <div class="modal-footer">
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="notify" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="label-notify">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="label-notify">Modal title</h4>
	  </div>
	  <div class="modal-body">
		<p>One fine body&hellip;</p>
	  </div>
	  <div class="modal-footer">
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="modal-show" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="label-modal-show">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="label-modal-show">Modal title</h4>
	  </div>
	  <div class="modal-body">
		<p>One fine body&hellip;</p>
	  </div>
	  <div class="modal-footer">
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Le javascript
================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script src="angularjs/angular.min.js"></script>
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/jquery.blockUI.js"></script>
<script src="js/bootstrap-notify-3.1.3/bootstrap-notify.min.js"></script>

<script src="angularjs/utils/pagination/dirPagination.js"></script>

<script src="modules/bootstrap-modal.js"></script>
<script src="modules/block-ui.js"></script>
<script src="modules/bootstrap-notify.js"></script>
<script src="modules/account.js"></script>

<script src="controllers/schedules.js"></script>

</body>
</html>
