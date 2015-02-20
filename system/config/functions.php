<?php
/**
 * loadClasses: Called by spl_autoload_register to auto declare classes.
 * 
 * @param String Class Name
 * 
 * @return null
 */ 
function loadClasses($class_name) {
		
        global $framework;
        global $app;
        global $model_paths;
        //class directories
        
        $directorys = $model_paths;
        
        //for each directory
        foreach($directorys as $directory)
        {
	        
            //see if the file exsists
            if(file_exists($framework->basepath.DS.$directory.$class_name . '.php'))
            {
            	//require file
                require_once($framework->basepath.DS.$directory.$class_name . '.php');
                return;
            }            
        }
}
/**
 * handleError: Called by set_error_handler to handle PHP errors.
 * 
 * @param String $errno Error Number
 * @param String $errstr Error String
 * @param String $errfile Error File
 * @param String $errline Error Line
 * 
 * @return null
 */ 

function handleError($errno="", $errstr="", $errfile = "", $errline = "") {
	if (!error_reporting()) {
        // This error code is not included in error_reporting
        return;
    }
    global $framework;
    global $framework;
	$logsettings = $framework->globalsettings()->logsettings;
	$framework->load('twaDebugger')->log($errno." ".$errstr." in file ".$errfile." on Line ".$errline);
    
}

/**
 * handleException: Called by set_exception_handler to handle PHP exception.
 * 
 * 
 * @param String $exception Exception
 * 
 * @return null
 */ 

function handleException($exception) {
	global $framework;
    global $framework;
	$logsettings = $framework->globalsettings()->logsettings;
	$framework->load('twaDebugger')->log($exception->getMessage());
}


?>