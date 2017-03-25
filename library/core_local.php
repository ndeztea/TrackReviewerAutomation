<?php 	
	class Core_Bootstrap extends PHPUnit_Extensions_Selenium2TestCase
	{
		
		public static $driver = '';
		private $testname;
		private $testId;

		public static $browsers = array(
            array(
	            'browserName' => TEST_BROWSER1,
	            'sessionStrategy' => 'shared',
	        )/*,
	        array(
	            'browserName' => TEST_BROWSER2,
	            'sessionStrategy' => 'shared',
	        ),
	        array(
	            'browserName' => TEST_BROWSER3,
	            'sessionStrategy' => 'shared',
	        )*/
	    );
		
		public function setUp(){
			// set the timezone manually 
			ini_set( 'date.timezone', TEST_TIMEZONE);
			
			$testUrlEnv = getenv('TEST_URL_ENV');
			$testUrl = !empty($testUrlEnv)?$testUrlEnv:TEST_URL;

			//$this->setBrowser(TEST_BROWSER);
        	$this->setBrowserUrl($testUrl);

        	Core::$driver = $this;
        	
		}
	}
?>