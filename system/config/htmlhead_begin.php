<?php
global $framework;
global $app;
$user = $framework->getUser();
?>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">

<script>
var $baseurl = '<?php echo $app->siteurl; ?>';
var $contenturl = '<?php echo $app->siteurl; ?>web_content/';
var $secureurl = '<?php echo $app->secureurl; ?>';
var $lang = '<?php echo $framework->load('twaLanguage')->lang; ?>';
var twaController = '<?php echo $app->_controllerid; ?>';
var twaView = '<?php echo $app->_viewid; ?>';
var $version = {id:'<?php echo TWA_VERSION; ?>'};
var $authtoken = '<?php echo $_SESSION['_twa_auth_token']; ?>';
</script>


<link rel="stylesheet" href="<?php echo $app->siteurl; ?>web_content/styles/boilerplate/normalize.css">
<link rel="stylesheet" href="<?php echo $app->siteurl; ?>web_content/styles/boilerplate/main.css">
<link rel="stylesheet" href="<?php echo $app->siteurl; ?>web_content/styles/plugins/animate/animate.css">
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">


<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo $app->siteurl; ?>web_content/javascripts/jquery-2.1.3.js"><\/script>')</script>
<!--
<script type='text/javascript' src="https://code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<script type='text/javascript' src="<?php echo $app->siteurl; ?>web_content/javascripts/jquery-ui.custom.min.js"></script>
-->

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script type='text/javascript' src="<?php echo $app->siteurl; ?>web_content/javascripts/utilities/plugins.js"></script>
<script type='text/javascript' src="<?php echo $app->siteurl; ?>web_content/javascripts/framework/framework.js"></script>
<script type='text/javascript' src="<?php echo $app->siteurl; ?>web_content/javascripts/framework/social.js"></script>
<script type='text/javascript' src="<?php echo $app->siteurl; ?>web_content/javascripts/framework/user.js"></script
<script type='text/javascript' src="<?php echo $app->siteurl; ?>web_content/javascripts/utilities/ellipsis.js"></script>
<script type='text/javascript' src="<?php echo $app->siteurl; ?>web_content/javascripts/framework/socket.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<script>
	var $user = new User(<?php echo $user->fields['user_id']; ?>,<?php  if($user->isLoggedIn()){ echo 'true';} else { echo 'false'; }; ?>);
	<?php  if($user->isLoggedIn()){
	?>
		$user.fields = <?php echo $user->getJSON(); ?>;
		$user.social = <?php echo json_encode($user->social()); ?>
	<?php	
	}
	?>
</script>

<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

