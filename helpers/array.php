<?php 
	/**
	* Jomsocial Automation Testing
	* 
	* class for array helpers function
	*/
	Class ArrayHelpers{

		/**
		* functions for check sorting alphabetically 
		* this function is not smart code, if any suggestion please update it
		* 
		*/
		public static function assertSortingAlphabetically($array){
			if(empty($array))
				return false;

			// because we cant comparasion the 2 variable types string and integer, so we need convert it to int first
			// start with 11, b
			$tmpArrayLetter = range('a', 'z');
			$arrayLetter = array();
			$a = 11;
			foreach ($tmpArrayLetter as $key => $value) {
				$arrayLetter[$value] = $a;
				$a++;
			}

			// replace the current array letter with integer value
			$arrayForCheck = array();
			foreach($array as $key=>$value){
				if(is_string($value)){
					$arrayForCheck[] = $arrayLetter[strtolower($value)];
				}else{
					$arrayForCheck[] = $value;
				}
			}

			// assertion here
			$tmpVal = 0;
			foreach($arrayForCheck as $key=>$value){
				Core::$driver->assertGreaterThan($tmpVal,$value);
				$tmpVal = $value;
			}

		}
	}
?>