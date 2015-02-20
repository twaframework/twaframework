<?php
/**
 * The deploy object for git methods.  
 *
 * @see twaController::email();
 * @category system
 * @author Akshay Kolte <akshay.kolte@etlok.com>
 */

defined('_TWACHK') or die;

class twaDeploy {

  private $_user = false;
  
  /**
  * The name of the branch to pull from.
  * 
  * @var string
  */
  private $_branch = 'master';

  /**
  * The name of the remote to pull from.
  * 
  * @var string
  */
  private $_remote = 'origin';

  /**
  * The directory where your website and git repository are located, can be 
  * a relative or absolute path
  * 
  * @var string
  */
  private $_directory;
  
  private $_commit_message = 'Committing Changes Remotely';
  
  private $_include = array('-A');

  /**
  * Executes the necessary commands to deploy the website.
  */
  public function pull($options) {
      global $framework;
      $debugger = $framework->load('twaDebugger');
	     
      try
      {
	      
	      $this->_directory = $framework->basepath;
	      
	      $available_options = array('directory', 'user', 'branch', 'remote');

	      foreach ($options as $option => $value)
	      {
	          if (in_array($option, $available_options))
	          {
	              $this->{'_'.$option} = $value;
	          }
	      }
          
          // Make sure we're in the right directory
          exec('cd '.$this->_directory, $output);
          $debugger->log('Changing working directory... '.implode(' ', $output));

          // Discard any changes to tracked files since our last deploy
          exec('git reset --hard HEAD', $output);
          $debugger->log('Reseting repository... '.implode(' ', $output));

          // Update the local repository
          exec('git pull '.$this->_remote.' '.$this->_branch, $output);
          $debugger->log('Pulling in changes... '.implode(' ', $output));
		  
		  if($this->_user){
		  	exec('chown -R '.$this->_user.' '.$this->_directory);
		  }
          // Secure the .git directory
          exec('chmod -R og-rx .git');
          $debugger->log('Securing .git directory... ');

      }
      catch (Exception $e){
          handleException($e);
      }
  }
  
  public function commit($options){
	  global $framework;
      $debugger = $framework->load('twaDebugger');
	     
      try
      {
	      
	      $this->_directory = $framework->basepath;
	      
	      $available_options = array('directory', 'user', 'branch', 'remote','commit_message','include');

	      foreach ($options as $option => $value)
	      {
	          if (in_array($option, $available_options))
	          {
	              $this->{'_'.$option} = $value;
	          }
	      }
          
          // Make sure we're in the right directory
          exec('cd '.$this->_directory, $output);
          $debugger->log('Changing working directory... '.implode(' ', $output));

          // Discard any changes to tracked files since our last deploy
          if($this->_include) {
	          foreach($this->_include as $item){
		           exec('git add '.$item, $output);
				   $debugger->log('Adding... '.implode(' ', $output));
	          }
          }
         

          // Update the local repository
          exec('git commit -m "'.$this->_commit_message.'"', $output);
          $debugger->log('Committing... '.implode(' ', $output));

      }
      catch (Exception $e){
          handleException($e);
      }
  }
}


  /**
  * Executes the necessary commands to deploy the website.
  */
  public function push($options) {
      global $framework;
      $debugger = $framework->load('twaDebugger');
	     
      try
      {
	      
	      $this->_directory = $framework->basepath;
	      
	      $available_options = array('directory', 'user', 'branch', 'remote');

	      foreach ($options as $option => $value)
	      {
	          if (in_array($option, $available_options))
	          {
	              $this->{'_'.$option} = $value;
	          }
	      }
          
          // Update the local repository
          exec('git push '.$this->_remote.' '.$this->_branch, $output);
          $debugger->log('Pushing changes... '.implode(' ', $output);

      }
      catch (Exception $e){
          handleException($e);
      }
  }

		  
		  

  
  