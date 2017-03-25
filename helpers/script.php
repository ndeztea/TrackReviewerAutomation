<?php 
	/**
	* Jomsocial Automation Testing
	* 
	* class for javascript function
	*/
	Class ScriptHelpers{

		/**
		* helper for hide element by class
		*
		*/
		public static function hideByClass($className){
			// show the hidden function
			Core::$driver->execute(array(
	            'script' => "jQuery('.".$className."').hide()",
	            'args' => array(),
	        ));
		}

		/**
		* helper for show the hidden class
		*
		*/
		public static function showHiddenClass($className){
			// show the hidden function
			Core::$driver->execute(array(
	            'script' => "jQuery('.".$className."').show()",
	            'args' => array(),
	        ));
		}

		/**
		* helper for show the hidden ID
		*
		*/
		public static function showHiddenId($idName){
			// show the hidden function
			Core::$driver->execute(array(
	            'script' => "jQuery('#".$idName."').show()",
	            'args' => array(),
	        ));
		}

		/**
		* set html / text inside the class
		*/
		public static function setClassValue($className,$value){
			Core::$driver->execute(array(
	            'script' => "jQuery('.".$className."').html('".$value."')",
	            'args' => array(),
	        ));
		}

		/**
		* append html / text inside the class
		*/
		public static function setClassValueAppend($className,$value){
			Core::$driver->execute(array(
	            'script' => "jQuery('.".$className."').append('".$value."')",
	            'args' => array(),
	        ));
		}

		/**
		* helper for executing the javascript function
		*
		*/
		public static function execute($script){
			Core::$driver->execute(array(
	            'script' => $script,
	            'args' => array(),
	        ));
		}

		/**
		* helper for click YES || NO at Jomsocial administrator
		* @param $name name of checkbox
		* @param $value O or 1
		*/
		public static function clickYesNoBox($name = null, $value = null){
			//Core::$driver->waitForXPath("//input[@name=".$name."]");
			sleep(2);
			
			// revome class
			Core::$driver->execute(array(
	            'script' => 'jQuery(\'input[name="'.$name.'"]\').removeAttr("class")',
	            'args' => array(),
	        ));

			// revome type to make it as textbox
	        Core::$driver->execute(array(
	            'script' => 'jQuery(\'input[name="'.$name.'"]\').removeAttr("type")',
	            'args' => array(),
	        ));
			
			// remove YES | NO label
			Core::$driver->execute(array(
	            'script' => "jQuery('span.lbl').removeAttr('class')",
	            'args' => array(),
	        ));

			// input value 1 for enable, 0 for disable
	        Core::$driver->execute(array(
	            'script' => 'jQuery(\'input[name="'.$name.'"]\').val("'.$value.'")',
	            'args' => array(),
	        ));

		}

		/**
		* helper for click radio button from Jomsocial configuration
		* @param $name name of checkbox
		* @param $value O or 1
		*/
		public static function clickRadioButton($name = null, $value = null){
			//Core::$driver->waitForXPath("//input[@name=".$name."]");
			sleep(2);
			
			// revome class
			Core::$driver->execute(array(
	            'script' => 'jQuery(\'input[name="'.$name.'"]\').removeAttr("class")',
	            'args' => array(),
	        ));

			// remove YES | NO label
			Core::$driver->execute(array(
	            'script' => "jQuery('span.lbl').removeAttr('class')",
	            'args' => array(),
	        ));

	        // revome type to make it as textbox
	        Core::$driver->execute(array(
	            'script' => 'jQuery(\'input[name="'.$name.'"]\').css("opacity","100 !important")',
	            'args' => array(),
	        ));

			// click radio button now
	        Core::$driver->byXPath('//input[@name="'.$name.'"][@value="'.$value.'"]')->click();

		}

		public static function selfLinkTarget(){
			Core::$driver->execute(array(
			 'script' => 'jQuery("a").removeAttr("target")',
			 'args' => array(),
	        ));
		}

		public static function selfLinkTargetFrontend(){
			Core::$driver->execute(array(
			 'script' => 'jQuery("a").removeAttr("target")',
			 'args' => array(),
	        ));
		}

		public static function showFieldHidden(){
			Core::$driver->execute(array(
			 'script' => 'jQuery("input[type=\'hidden\']").attr("type","text")',
			 'args' => array(),
	        ));
		}


		/**
		* this is for J3.3 config
		*/
		public static function removeYesNoStyle($id){
			Core::$driver->execute(array(
			 'script' => 'jQuery("#'.$id.'").removeAttr("class")',
			 'args' => array(),
	        ));
		}

		public static function scrollToBottom(){
			Core::$driver->execute(array(
			 'script' => 'jQuery("html, body").animate({ scrollTop: jQuery(document).height() }, 100)',
			 'args' => array(),
	        ));
		}

		public static function scrollToTop(){
			Core::$driver->execute(array(
			 'script' => 'jQuery("html, body").animate({ scrollTop: 0 }, 100)',
			 'args' => array(),
	        ));
		}
	}
?>