<?php 
 defined('_TWACHK') or die; 
 global $framework;
 global $app;
?>

<body id='<?php echo $app->_viewid; ?>'>
<?php $this->add('content'); ?>
<?php $this->declareJSVariables();  ?>
</body>