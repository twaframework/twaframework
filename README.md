twaframework.8.1
================

twaFramework Beta
-----------------

Note: twaFramework is currently in beta so you may expect some bugs, issues or unfinished features.


Installation Instructions
=========================

No installation is necessary.  Simply download the code to the root directory of your website and you are all set.


Configuring Database
-----------------------------

If you use the administration console, you can configure your database easily.  However if you wish to manually configure the database you will need to follow the instructions below.

	Go to /system/config/databases/twaDBConfig_default.php
	public $host = '<databse host name>';
	public $driver = '<databse driver for e.g. mysql, pgsql, sqlite, sqlite2, sqlsrv, odbc>';
	public $db = '<databse name>';
	public $user = '<databse username>';
	public $password = '<databse password>';
	public $prefix = '<table name prefix for e.g. 'twa_'>';
	public $isDBConfigured = TRUE;
	
One your have added the parameters to connect to the database simply open your website http://mysite.com/.
Once on your home page, click "Create Tables" to create the necessary tables.

Usage
=====

Create A Layout
---------------

twaFramework uses a hierarchy when loading your views.  First you have a layout, this layout defines the basic outer structure of your site.  By default, we have created a simple blank layout for you. This can be found under /web_content/layouts/default/layout.php

This is the simplest form of a layout.  It simply opens the body tag and loads the view in it.  However, you can have more complex layouts.  Let us say that we want to create a new layout that has a side bar on the left. 

1. Go to /web_content/layouts and create a new folder called “left”.  
2. Within this folder we will create a layout.php file and an htmlhead.php file.   
3. The layout.php file looks as follows.

	```<?php 
	defined('_TWACHK') or die; 
	global $framework;
	global $app;
	?>
	<body id='<?php echo $app->_viewid; ?>’>
		<div class=‘container’>
			<div class=‘row’>
				<div class=‘col-md-3’>
					<?php $this->add('sidebar'); ?>
				</div>
				<div class=‘col-md-9’>
					<?php $this->add('content'); ?>
				</div>
			</div>
		</div>
		<?php $this->declareJSVariables();  ?>
	</body>
	``` 
	
4. There are couple things to note in the layout.php file.  First, 

	```
	<?php $this->add('content'); ?>
	```

	This line simply adds the contents of the view.  So, the place where we put this is the place where all the items from the view will be loaded.  

	The same way, 

		<?php $this->add('sidebar'); ?>  
		
		
	adds the content of a component.  A component is different from a view in that a component is re-usable across many views.  For e.g. our sidebar could contain a menu that could be used for many different views.   We will learn about creating components later.  For now, just note that the add function can be used to add the contents of a view by using $this->add(‘content’) or a component by using $this->add(component_name)

5. Now let us take a look at the htmlhead.php file.  By default, twaFramework loads some standard items into the <head> section of your page.  These are defined in the /system/config/htmlhead_begin.php   These items will be loaded on every page of your site.  You can however add specific items to the <head> section for each layout.  These can be defined in the htmlhead.php under your folder.  Let us say that we for the left side layout we created, we want to add a link to the style.css and add a script pointing to home.js.

	```
	<?php $this->setScript(‘main’,’1.0'); ?>
	<?php $this->setStyle('style','1.0'); ?>
	```

6. The setScript function adds a script tag pointing to the main.js file under the web_content/javascripts folder.  The "1.0" defines a version that can be used as a cache buster.  Similarly, the setStyle function adds a link tag pointing to the style.css file under the web_content/styles folder.  

7. When setting the script, we can also define a third parameter to indicate if this file should be loaded now or later.  If set to FALSE, the JS will be loaded after the document is ready.

	```
	<?php $this->setScript(‘main’,’1.0’,false); ?>
	```

Create A Page
-------------

To create a page called "test":

1. Go to /web_content/pages/
2. Create a folder "test"
3. Create a file within the folder called "test.php"
4. In test.php add the code:
	
		
		<h1>My Test Page</h1>
		
	
5. To configure your page, edit the /system/config/twaRoutes.php
6. In the twaRoutes.php, add the following code:

	```
	$this->pages['test'] = array(
				"name"=>"help",
				"access"=>AUTHORIZE_ALL,
				“layout”=>"default",
				"title"=>"Test Page",
				"keywords"=>"",
				"description"=>"This is a test page."				  
			    );
	```

7. This configuration allows you to set the title and meta tags for the page. Also, it allows you to select the layout, which in this case is set to default.
8. Go to /web_content/pages/test and create another file called htmlhead.php
9. You can use this file to add items to the <head> section specifically for this page.
10. To use the metadata defined in the twaRoutes.php file, simply type:

	```
	<?php $this->page_metadata(); ?>
	```

11. To add a script, you may either type the HTML in directly or use:
	
	```
	<?php $this->setScript("test","1.0"); ?>
	```

12. This will attempt to load a file called test.js from the /web_content/javascripts folder.  The second parameter "1.0" defines the version number of this file. This can be used as a cache buster. You may also pass a third parameter as TRUE or FALSE.  If set to FALSE, the script will added only after the page has been loaded.
13. To add a style, you may either type in the HTML or use:

	
		<?php $this->setStyle("style","1.0"); ?> 
	
	
14. This will attempt to load a file called style.scss or style.css from the /web_content/styles folder.  The second parameter "1.0" defines the version number of this file.


Create A Component
------------------

To create a header that will repeat on multiple pages

1. Go to /web_content/components
2. Create a folder called "header"
3. Create a file within the folder called "header.php"
4. In header.php add the code:

		
		<header>
			<h1>My Header</h1>
		</header>
		
		
5. To add the header to your test page to go /web_content/pages/test
6. Open test.php and add the following line above the h1 tag.

		
		<?php $this->add('header'); ?>
		
		
7. That's it. Your page will now show a header.  You can add the header to any page you wish or directly in the layout.

Create A Controller
------------------

In most of your applications you might use only a single controller.  The controller allows you to call functions that will load data into your view. A controller has been created by default for you in the installation.  You can find the class under /system/controllers/default/controller.php.  Controllers provide two opportunities to load data.  First, is the default() method.  The default() method is executed before any view is updated making data generated from this method available to all views.  You can also create specific methods for each view or component. For example, if we create a method called help() it will be executed before the help view is called - making data from this method available only within the help view.  Similarly, the sidebar() method will be called before executing the code for the sidebar.

Furthermore, you can create any custom methods in the controller which can be called from any view or method using $this->methodname().  When creating a method, if you define a variable, you can access it in your view using $this->variablename.  For e.g. Let us say that we have defined a method called test() which assigns an array to a variable called testtopics

		…
		public function test(){
			$this->testtopics = array(
				"Introduction",
				"Basics",
				"Advanced"
			);
			
		}
		…


We can access this array in our view by calling $this->testtopics

However, there is a special array called $this->data.  If you assign any variables in this array, they will be available directly in your view.  For e.g.

	…
	public function test(){
		$this->data[‘title’] = "Test Page";
	}
	…
	

This variable will be available as $title in your view.

Variables like $framework, $app, $router and $debugger are available directly in your view.  
Furthermore, $base_path defines the base path of your application, $content_path defines the path of your web_content folder while $base_url defines the base URL of your site and $content_url defines the url to access your web_content folder. 
