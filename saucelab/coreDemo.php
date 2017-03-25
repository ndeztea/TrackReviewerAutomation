<?php

require_once 'vendor/autoload.php';

class CoreDemo extends Sauce\Sausage\WebDriverTestCase
{

    protected $start_url = 'http://saucelabs.com/test/guinea-pig';

    public static $browsers = array(
        // run FF15 on Windows 8 on Sauce
        array(
            'browserName' => 'firefox',
            'desiredCapabilities' => array(
                'version' => '15',
                'platform' => 'Windows 2012',
            )
        ),
        // run Chrome on Linux on Sauce
        array(
            'browserName' => 'chrome',
            'desiredCapabilities' => array(
                'platform' => 'Linux'
          )
        ),
        // run Mobile Safari on iOS
        //array(
            //'browserName' => '',
            //'desiredCapabilities' => array(
                //'app' => 'safari',
                //'device' => 'iPhone Simulator',
                //'version' => '6.1',
                //'platform' => 'Mac 10.8',
            //)
        //)//,
        // run Chrome locally
        //array(
            //'browserName' => 'chrome',
            //'local' => true,
            //'sessionStrategy' => 'shared'
        //)
    );

}
