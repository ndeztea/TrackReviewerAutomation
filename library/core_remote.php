<?php 	
	class Core_Bootstrap extends Sauce\Sausage\WebDriverTestCase
	{
		
		public static $driver = '';
		private $testname;
		private $testId;
		protected $start_url = 'http://manual3.jomsocial.com/';

		public static $browsers = array(
            array(
	            'browserName' => TEST_BROWSER1,
	            'sessionStrategy' => 'shared',
	            'username'=> 'meravkn',
    			'access-key'=> 'feec7398-e831-47ed-89c2-9af29b196832',
	            'desiredCapabilities' => array(
                	'platform' => TEST_PLATFORM1
          		)
	        )/*,
	        array(
	            'browserName' => TEST_BROWSER2,
	            'sessionStrategy' => 'shared',
	            'username'=> 'meravkn',
    			'access-key'=> 'feec7398-e831-47ed-89c2-9af29b196832',
	            'desiredCapabilities' => array(
                	'platform' => TEST_PLATFORM2
          		)
	        ),
	        array(
	            'browserName' => TEST_BROWSER3,
	            'sessionStrategy' => 'shared',
	            'username'=> 'meravkn',
    			'access-key'=> 'feec7398-e831-47ed-89c2-9af29b196832',
	            'desiredCapabilities' => array(
                	'platform' => TEST_PLATFORM3
          		)
	        )*/
	    );
		
		public function setUp(){
			// set the timezone manually 
			ini_set( 'date.timezone', TEST_TIMEZONE);
			
			$testUrlEnv = getenv('TEST_URL_ENV');
			$testUrl = !empty($testUrlEnv)?$testUrlEnv:TEST_URL;

			$testBuildVersion = getenv('TEST_BUILD_VERSION');
			$testBuildVersion = !empty($testBuildVersion)?$testBuildVersion:TEST_BUILD_VERSION;

			//$this->setBrowser(TEST_BROWSER);
        	$this->setBrowserUrl($testUrl);

        	$caps = $this->getDesiredCapabilities();
        	if (!isset($caps['name'])) {
	            $caps['name'] = get_called_class().'::'.$this->getName();
	            $caps['build'] = $testBuildVersion;
	            $caps['idle-timeout'] = 600;
	            $this->setDesiredCapabilities($caps);
	        }

        	Core::$driver = $this;
        	
		}

		public function tearDown(){
			// prepare the code this for next time
		}
	}
?>