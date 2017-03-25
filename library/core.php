<?php 
	/**
	* Jomsocial Automation Testing
	* 
	* Setup the selenium configuration
	*/

	// run this code if you are no using the phpunit.xml
	ini_set('include_path', '/usr/lib/php/pear');

	// if you cant call the phpunit library (maybe missed the phpunit configuratino) 
	// you can should call it manually
	require_once 'saucelab/vendor/autoload.php';
	//require_once '/Users/ndeztea/pear/share/pear/PHPUnit/Extensions/SeleniumTestCase/autoload.php';
	PHPUnit_Extensions_Selenium2TestCase::shareSession(true);

	
	if(TEST_REMOTELY=='true'){
		require_once(__DIR__.'/core_remote.php');
	}else{
		require_once(__DIR__.'/core_local.php');
	}
	
	class Core extends Core_Bootstrap
	{
		
		private $testname;
		private $testId;

		public function goToModulesPage(){
			$this->url('index.php/modules-page');
		}

		/**
		* set username and password for login
		*/
		public function loginUser($user){
			$this->user = new User($user);

			$this->byId('username')->value($this->user->username);
			$this->byId('password')->value($this->user->password);
			$this->byId('submit')->click();  
		}

		/**
		* Set screenshoot for page which opened via selenium
		*/
		public function screenshoot(){

			// execute this code if screenshoo configuration is enabled 
			$testScreenshoot = getenv('TEST_SCREENSHOOT_ENV');
			$testScreenshoot = empty($testScreenshoot)?TEST_SCREENSHOOT:$testScreenshoot;
			if($testScreenshoot=='true'){
				try{
					$screenshot = Core::$driver->currentScreenshot();

					// use time for filename
					$filename = date('His').'.png';
					
					$testBuildVersion = getenv('TEST_BUILD_VERSION');
					$testBuildVersion = !empty($testBuildVersion)?$testBuildVersion:TEST_BUILD_VERSION;

					$screenshootFolderRoot = __DIR__.'/../screenshoot/version_'.$testBuildVersion;
					
					$testId = $this->testId?$this->testId:$_SESSION['testId'];
					$screenshootFolder = $screenshootFolderRoot.'/'.$testId;
					// if folder not exist create new one depend the date
					if(!is_dir($screenshootFolderRoot)){
						mkdir($screenshootFolderRoot);
					}
					if(!is_dir($screenshootFolder)){
						mkdir($screenshootFolder);
					}
				
					// save the screenshoot
					$fp = fopen($screenshootFolder.'/'.$filename,'wb');
			        fwrite($fp,$screenshot);
			        fclose($fp);
			        Core::$driver->assertTrue(is_string($screenshot));
			        Core::$driver->assertTrue(strlen($screenshot) > 0);
			    }catch(exception $e){
			    	echo 'screenshoot failed';
			    }
			}
			
		}

		/**
		* function for checking the text is available on the page
		*
		* @param $text String
		*/
		public function assertContainsText($texts){
			sleep(5);
			$source = $this->source();
			$source = strtolower($source);
			if(!is_array($texts)){
				$text = strtolower($texts);
				if ( strpos((string)$source,$text) !== FALSE){
					$this->assertEquals($text,$text);
				}
				else{
					$this->assertEquals('Cant found',$text);
				}
			}else{
				foreach ($texts as $text) {
					$text = strtolower($text);
					if ( strpos((string)$source,$text) !== FALSE){
						$this->assertEquals($text,$text);
					}
					else{
						$this->assertEquals('Cant found',$text);
					}
				}
			}

		}

		/**
		* function for checking the text is NOT available on the page
		*
		* @param $text String
		*/
		public function assertNotContainsText($texts){
			sleep(5);

			$source = $this->source();
			$source = strtolower($source);

			// check assert text only
			if(!is_array($texts)){

				$text = strtolower($texts);
				if ( !strpos((string)$source,$text) !== FALSE){
					$this->assertFalse(false);
				}else{
					$this->assertEquals('Still found',$text);
				}
			}else{
				// array method to make asseration faster
				foreach ($texts as $text) {
					$text = strtolower($text);
					if ( !strpos((string)$source,$text) !== FALSE){
						$this->assertFalse(false);
					}
					else{
						$this->assertEquals('Still found',$text);
					}
				}
			}

		}

		/**
		* function for checking the text is  available on the page and avoid the HTML
		*
		* @param $text String
		*/
		public function assertContainsTextStripTags($text){
			sleep(5);

			$source = trim(strip_tags($this->source()));
			$source = trim(strtolower($source));
			$text = trim(strtolower($text));
			if ( strpos((string)$source,$text) !== FALSE)
				$this->assertEquals($text,$text);
			else 
				$this->assertEquals($text,0);
		}

		/**
		* function for checking the text is NOT  available on the page and avoid the HTML
		*
		* @param $text String
		*/
		public function assertNotContainsTextStripTags($text){
			sleep(5);

			$source = trim(strip_tags($this->source()));
			$source = trim(strtolower($source));
			$text = trim(strtolower($text));
			if ( !strpos((string)$source,$text) !== FALSE){
				$this->assertFalse(false);
			}else{
				echo 'text : "'.$text. '" is still exist';
				$this->assertFalse(true);
			}
		}
		
		public function startTestCase($testId,$testname){
			
			// set the browser widthxheight for the responsive
			$testResolution = getenv('TEST_RESOLUTION_ENV');
			$resolution = explode('x', $testResolution);
			if(!empty($resolution[1])){
				$browserWidth = intval($resolution[0]);
				$browserHeight =  intval($resolution[1]);
			}
			$browserWidth = !empty($browserWidth)?$browserWidth:1280;
			$browserHeight = !empty($browserHeight)?$browserHeight:800;
			
			//if($browserWidth!='1280' && $browserHeight!='800'){
			try{
				//$this->currentWindow()->size(array('width' => $browserWidth, 'height' => $browserHeight));
				$this->currentWindow()->maximize();
			}
			catch(exception $e){
				// nothing to do here
			}
			//}
			

			$this->testname = $testname;
			$this->testId = $testId;

			$_SESSION['testname'] = $testname;
			$_SESSION['testId'] = $testId;

			echo " Executing " . $testId.' : '.$testname . " ... \n";
		}

		/** 
		* waiting ID appears when ajax action running
		*
		* @param string $id - DOM id
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForId($id, $wait=30) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byId($id)->attribute('class');; 
					return $x; 
				} 
				catch (Exception $e) { 
					sleep(1); 
				} 
			} 
			return false; 
		}

		/** 
		* waiting class appears when ajax action running
		*
		* @param string $class - DOM class
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForClass($class, $wait=30) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byClassName($class)->attribute('class'); 
					return $x; 
				} 
				catch (Exception $e) { 
					sleep(1); 
				} 
			} 
			return false; 
		} 

		/** 
		* waiting class selector appears when ajax action running
		*
		* @param string $class - DOM class
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForClassSelector($class, $wait=30) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byClassName($class)->attribute('class'); 
					return $x; 
				} 
				catch (Exception $e) { 
					sleep(1); 
				} 
			} 
			return false; 
		} 

		/** 
		* waiting class selector appears when ajax action running
		*
		* @param string $class - DOM class
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForCssSelector($class, $wait=30) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byCssSelector($class)->attribute('class'); 
					return Core::$driver->byCssSelector($class); 
				} 
				catch (Exception $e) { 
					sleep(1); 
				} 
			} 
			return Core::$driver->byCssSelector($class);  
		} 

		/** 
		* waiting class selector appears when ajax action running
		*
		* @param string $class - DOM class
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForCssSelectorGone($class, $wait=10) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byCssSelector($class)->attribute('class'); 
					sleep(1); 
				} 
				catch (Exception $e) { 
					return true;
				} 
			} 
			return false; 
		} 

		/** 
		* waitin one attribute xPath appears when ajax action running
		*
		* @param string attribute xPath - DOM class
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForXPath($xPath, $wait=30) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byXPath($xPath)->attribute('class');; 
					return Core::$driver->byXPath($xPath);
				} 
				catch (Exception $e) { 
					sleep(1); 
				} 
			} 
			return false; 
		}

		/** 
		* waiting name appears when ajax action running
		*
		* @param string $name - DOM name
		* @param int $wait - maximum (in seconds)
		* @return element|false - false on time-out
		*/ 
		public static function waitForName($name, $wait=30) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byName($name)->attribute('class');; 
					return $x; 
				} 
				catch (Exception $e) { 
					sleep(1); 
				} 
			} 
			return false; 
		} 

		/** 
		* waiting name disappears when ajax action running
		*
		* @param string $name - DOM name
		* @param int $wait - maximum (in seconds)
		* @return element|false - false on time-out
		*/ 
		public static function waitForNameGone($name, $wait=10) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byName($name)->attribute('class');; 
					sleep(1);
				} 
				catch (Exception $e) { 
					return true; 
				} 
			} 
			return false; 
		}

		/** 
		* waiting ID disappears when ajax action running
		*
		* @param string $id - DOM id
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForIdGone($id, $wait=10) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byId($id)->attribute('class');; 
					sleep(1);
				} 
				catch (Exception $e) { 
					return true; 
				} 
			} 
			return false; 
		}

		/** 
		* waiting class disappears when ajax action running
		*
		* @param string $class - DOM class
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForClassGone($class, $wait=10) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byClassName($class)->attribute('class');; 
					sleep(1);
				} 
				catch (Exception $e) { 
					return true; 
				} 
			} 
			return false; 
		}

		/** 
		* waiting attribute xPath disappears when ajax action running
		*
		* @param string $xPath - xPath location
		* @param int $wait - maximum (in seconds)
		* @retrn element|false - false on time-out
		*/ 
		public static function waitForXPathGone($xPath, $wait=10) { 
			for ($i=0; $i <= $wait; $i++) { 
				try{ 
					$x = Core::$driver->byXPath($xPath)->attribute('class');; 
					sleep(1);
				} 
				catch (Exception $e) { 
					return true; 
				} 
			} 
			return false; 
		}

		public function executeCronJobs(){
			Core::$driver->url('index.php?option=com_community&task=cron');
			return $this;
		}
	}

	function autoloadLibrary($classname){
		$corePath = getcwd();
		$classes = array(
			// library  frontend
			'Apps'		=> $corePath.'/library/apps.php',
	    	'Bookmarks'	=> $corePath.'/library/bookmarks.php',
	    	'Comment'	=> $corePath.'/library/comment.php',
	    	'Event'		=> $corePath.'/library/event.php',
	    	'Facebook'	=> $corePath.'/library/facebook.php',
	    	'Friends'	=> $corePath.'/library/friends.php',
	    	'Group'		=> $corePath.'/library/group.php',
	    	'Inbox'		=> $corePath.'/library/inbox.php',
	    	'Photo'		=> $corePath.'/library/photo.php',
	    	'Privacy'	=> $corePath.'/library/privacy.php',
	    	'Register'	=> $corePath.'/library/register.php',
	    	'Sharebox'	=> $corePath.'/library/sharebox.php',
	    	'Status'	=> $corePath.'/library/status.php',
	    	'Stream'	=> $corePath.'/library/stream.php',
	    	'User'		=> $corePath.'/library/user.php',
	    	'Video'		=> $corePath.'/library/video.php',
	    	'Toolbar'		=> $corePath.'/library/toolbar.php',

	    	// module
	    	'ModuleHellome'		=> $corePath.'/library/moduleHellome.php',
	    	'ModuleHashTag'		=> $corePath.'/library/moduleHashTag.php',
	    	'ModuleCalendarEvent'		=> $corePath.'/library/moduleCalendarEvent.php',
	    	'ModuleProfileCompleteless'		=> $corePath.'/library/moduleProfileCompleteless.php',
	    	'ModuleEventSuggestion'		=> $corePath.'/library/moduleEventSuggestion.php',
	    	'ModuleFriendSuggestion'		=> $corePath.'/library/moduleFriendSuggestion.php',
	    	'ModuleGroupSuggestion'		=> $corePath.'/library/moduleGroupSuggestion.php',
	    	'ModuleTrendingGroup'		=> $corePath.'/library/moduleTrendingGroup.php',
	    	'ModuleTrendingHashTag'		=> $corePath.'/library/moduleTrendingHashTag.php',
	    	'ModuleTrendingEvent'		=> $corePath.'/library/moduleTrendingEvent.php',
	    	'ModuleTrendingVideo'		=> $corePath.'/library/moduleTrendingVideo.php',
	    	'ModuleTrendingPhoto'		=> $corePath.'/library/moduleTrendingPhoto.php',
	    	'ModuleBirthdate'		=> $corePath.'/library/moduleBirthdate.php',

	    	// library admin
	    	'DefaultPrivacy'		=> $corePath.'/library/admin/defaultPrivacy.php',
	    	'AntiSpam'				=> $corePath.'/library/admin/antiSpam.php',
	    	'AdminNavigation'		=> $corePath.'/library/admin/adminNavigation.php',
	    	'Configuration'			=> $corePath.'/library/admin/configuration.php',
	    	'configurationSite'		=> $corePath.'/library/admin/configurationSite.php',
	    	'EmailDigest'			=> $corePath.'/library/admin/emailDigest.php',
	    	'UserAdmin'				=> $corePath.'/library/admin/userAdmin.php',
	    	'eventConfiguration'	=> $corePath.'/library/admin/eventConfiguration.php',
	    	'groupConfiguration'	=> $corePath.'/library/admin/groupConfiguration.php',
	    	'photoConfiguration'	=> $corePath.'/library/admin/PhotoConfiguration.php',
	    	'videoConfiguration'	=> $corePath.'/library/admin/videoConfiguration.php',
	    	'monitorConfiguration'	=> $corePath.'/library/admin/monitorConfiguration.php',
	    	'CustomizeProfile'		=> $corePath.'/library/admin/customizeProfile.php',
	    	'PluginManager'			=> $corePath.'/library/admin/pluginManager.php',
	    	'PhotoManager'			=> $corePath.'/library/admin/photoManager.php',
	    	'GroupManager'			=> $corePath.'/library/admin/groupManager.php',
	    	'UserManager'			=> $corePath.'/library/admin/userManager.php',
	    	'UserPoints'			=> $corePath.'/library/admin/userPoints.php',
	    	'Template'				=> $corePath.'/library/admin/template.php',
	    	'MailQueueSetting'		=> $corePath.'/library/admin/mailQueueSetting.php',
	    	'MultiProfileManager'	=> $corePath.'/library/admin/multiProfileManager.php',
	    	'MoodsManager'	=> $corePath.'/library/admin/moodsManager.php',
	    	'ColorConfiguration'	=> $corePath.'/library/admin/colorConfiguration.php',
	    	'ThemeDesigerGeneralConfiguration' => $corePath.'/library/admin/themeDesignerGeneralConfiguration.php',
	    	'ThemeDesigerProfileConfiguration' => $corePath.'/library/admin/themeDesignerProfileConfiguration.php',
	    	'ThemeDesigerGroupConfiguration'	=> $corePath.'/library/admin/themeDesignerGroupConfiguration.php',
	    	'ThemeDesigerLayoutConfiguration'	=> $corePath.'/library/admin/themeDesignerLayoutConfiguration.php',
	    	'BadgesConfiguration'	=> $corePath.'/library/admin/badgesConfiguration.php',
	    	'ActivitiesConfiguration'	=> $corePath.'/library/admin/activitiesConfiguration.php',
	    	'ReportingConfiguration'	=> $corePath.'/library/admin/reportingConfiguration.php',
	    	'RegisterConfiguration'	=> $corePath.'/library/admin/registerConfiguration.php',
	    	'ContentManager'		=> $corePath.'/library/admin/contentManager.php',

	    	// module
	    	'AdminHashTagModule'	=> $corePath.'/library/admin/adminHashTagModule.php',
	    	'AdminHelloMeModule'	=> $corePath.'/library/admin/adminHelloMeModule.php',
	    	'AdminEventModule'		=> $corePath.'/library/admin/adminEventModule.php',
	    	'AdminVideosModule'		=> $corePath.'/library/admin/adminVideosModule.php',
	    	'AdminPhotosModule'		=> $corePath.'/library/admin/adminPhotosModule.php',
	    	'AdminMembersModule'	=> $corePath.'/library/admin/adminMembersModule.php',
	    	'AdminToolbarModule'	=> $corePath.'/library/admin/adminToolbarModule.php',
	    	'AdminSearchModule'	=> $corePath.'/library/admin/adminSearchModule.php',
	    	'AdminTopMembersModule'	=> $corePath.'/library/admin/adminTopMembersModule.php',
	    	'AdminEventNearbyModule'	=> $corePath.'/library/admin/adminEventNearbyModule.php',
	    	'AdminVideoCommentsModule'	=> $corePath.'/library/admin/adminVideoCommentsModule.php',
	    	'AdminPhotoCommentsModule'	=> $corePath.'/library/admin/adminPhotoCommentsModule.php',

	    	'ModuleManager'		=> $corePath.'/library/admin/moduleManager.php',
	    	
	    	
	    	// helpers
	    	'ArrayHelpers'		=> $corePath.'/helpers/array.php',
	    	'ScriptHelpers'		=> $corePath.'/helpers/script.php'

	    );

	    if (array_key_exists($classname, $classes)) {
	        require_once($classes[$classname]);
	    }
	}	

	// define the autoload function
	spl_autoload_register('autoloadLibrary');

?>