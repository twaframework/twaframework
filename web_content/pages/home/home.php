<?php
	global $framework;
	global $app;
	$conf = $framework->load('twaConfig');
	$dbconf = $framework->load('twaDBConfig_default');
	
?>

<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700|Open+Sans:400,300,600' rel='stylesheet' type='text/css'>		
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/mfglabs_iconset.css">
<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/animation/animated.min.css">
<link rel="stylesheet" href="https://s3-us-west-2.amazonaws.com/com.twaframework.api/scripts/install-complete.css">
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css" rel="stylesheet">

<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script>
	$(document).ready(function(){
		$('.config-db').click(function(){
			$framework.request({
				"axn":"framework/database",
				"code":"runSQL",
				"db":"default",
				"filename":'system/config/databases/twaFramework.sql'
			},function(r){
				location.reload();
			});
		});
	});	
</script>


<div class="main-container container">
	<div class="row">
		<div class="container">
			
			<div class="grid">
				<h2 class='main-title'>Installation Complete</h2>
				<p>Welcome to twaFramework!  Congratulations, your installation is complete.</p>
				
				<?php if($dbconf->host == "") { ?>
				
				<p>To configure your database, please go to the <b>/system/config/databases</b> folder and edit the <b>twaDBConfig_default.php</b> file</p>
					<br/>	
				<?php } ?>
				
				<div class="info-table">
					<div class="info-row ok">
						<div class="info-icon pull-right">
							<p><i class='fa fa-check-circle'></i></p>
						</div>
						<div class="info-message">
							<p><strong>Version : </strong><?php echo TWA_VERSION; ?></p>
						</div>
						
					</div>
					
					<div class="info-row <?php echo $conf->twa_API_KEY == ""? "not-ok":"ok"; ?>">
						<div class="info-icon pull-right">
							<p><i class='fa <?php echo $conf->twa_API_KEY == ""? "fa-exclamation-circle":"fa-check-circle"; ?>'></i></p>
						</div>
						<div class="info-message">
							<p><strong>API Configured : </strong><?php echo $conf->twa_API_KEY == ""? "NO":"YES"; ?></p>
						</div>
						
					</div>
					
					<div class="info-row <?php echo $dbconf->host != "" ? "ok":"not-ok"; ?>">
						<div class="info-icon pull-right">
							<p><i class='fa <?php echo $dbconf->host != "" ? "fa-check-circle":"fa-exclamation-circle"; ?>'></i></p>
						</div>
						<div class="info-message">
							<p><strong>DB Configured : </strong><?php echo $dbconf->host != "" ? "YES":"NO"; ?></p>
						</div>
						
					</div>
					
					<div class="info-row <?php echo $dbconf->isDBConfigured ? "ok":"not-ok"; ?>">
						<div class="info-icon pull-right">
							<p><i class='fa <?php echo $dbconf->isDBConfigured ? "fa-check-circle":"fa-exclamation-circle"; ?>'></i></p>
						</div>
						<div class="info-message">
							<p><strong>Tables Created : </strong><?php echo $dbconf->isDBConfigured ? "YES":"NO"; ?></p>
						</div>
						
					</div>
					
					<?php if($dbconf->host != "") { ?>
						<div class="info-row">
							<a href='#' class='btn btn-primary config-db'>Create Tables</a>
						</div>
					
					<?php } else { ?>
						
					<?php } ?>
					
				</div>
				
			</div>
		</div>
	</div>
</div>
	
	