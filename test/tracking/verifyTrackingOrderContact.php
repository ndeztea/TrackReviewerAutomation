<?php
	require_once (__DIR__.'/../../library/core.php');

	class verifyTrackingOrderContact extends Core
	{
		var $asinID = 'B012E9DMNQ';
		// master ASIN B01DJ7XNJA
		var $asinIDarr = array('B012E9DMNQ','B01DKQP5KK','B01DDQXRWU','B01DKQAXC0') ; //B01DKQJP2E (DONE)

		var $product = 'Deodorant';
		var $username = 'dimas@ijoomla.com';
		var $password = 'Soreang123!';
		var $advSearchPage = 'https://sellercentral.amazon.com/gp/orders-v2/search/ref=ag_myosearch_apsearch_myo';
		var $reviewPage = 'https://www.amazon.com/product-reviews/B01DKQJP2E/ref=cm_cr_arp_d_viewopt_srt?ie=UTF8&refRID=19FKR70FR24JJR7C415D&pageNumber=1&sortBy=recent&reviewerType=all_reviews&filterByStar=';
		var $rating;
		var $reviewID;

		var $arrReviewerId = array();

		var $connection;

		public function setUpPage(){
			// login first
			$this->url('https://sellercentral.amazon.com');
			$this->byId('ap_email')->value($this->username);
			$this->byId('ap_password')->value($this->password);
			$this->byId('signInSubmit')->click();
		}


		public function _getOrderId($review,$nextPage=false){
			// $this->connection =  mysqli_connect(MYSQL_HOST,MYSQL_USERNAME,MYSQL_PASSWORD);
			// if(!$this->connection){
			// 	echo 'cant connect';
			// 	exit;
			// }
			if(!mysqli_select_db($this->connection, MYSQL_DB)){
				echo 'cant select BD';
				exit;
			}

			$reviewerID = $review['reviewerID'];
			$reviewDate = $review['reviewDate'];
			$noNext = false;
			if($nextPage==false){
				// select adv search
				$this->url($this->advSearchPage);
				$this->select($this->byName('searchType'))->selectOptionByValue('ASIN');
				$this->byName('searchKeyword')->value($this->asinID);
				//$this->select($this->byName('preSelectedRange'))->selectOptionByValue('365');

				// calculate date
				//$reviewDateFormatBegin = date('Y-m-d',strtotime($reviewDate));
				$this->byId('exactDateBegin')->click();
				$this->byId('exactDateBegin')->clear();
				$beginDate = strtotime($reviewDate.'-6 months');
				$reviewDateFormatBegin =  date('n/j/y',$beginDate);
				$this->byId('exactDateBegin')->value($reviewDateFormatBegin);

				$reviewDateFormatEnd = date('n/j/y',strtotime($reviewDate));
				$this->byId('exactDateEnd')->click();
				$this->byId('exactDateEnd')->clear();
				$this->byId('exactDateEnd')->value($reviewDateFormatEnd);


				//$this->select($this->byId('_myoSO_statusFilterSelect'))->selectOptionByValue('Shipped');
				//$this->byId('_myoSO_ShowPendingCheckBox')->click();

				$this->byName('Search')->click();

				// select all item
				$this->select($this->byCssSelector('.tiny > select[name="itemsPerPage"]'))->selectOptionByValue('100');
				$this->byCssSelector('.myo_list_orders_search_form > table > tbody > tr > td:nth-child(4) input')->click();
			}else{
				//$this->byXPath('//a[@class="myo_list_orders_link"][text()="Next"]')->click();
				try{
					$this->byXPath('//a[@class="myo_list_orders_link"][text()="Next"]')->click();
				}catch(exception $e){
					$noNext = true;
				}
			}

			sleep(5);

			/*
			if ( strpos((string)$source,$reviewerID) == FALSE){
				$this->_getOrderId($reviewerID, true);
			}else{
				ScriptHelpers::execute('var OrderID = $(\'input[class="cust-id"][value="'.$reviewerID.'"]\').prev().prev().prev().attr(\'value\'); $(\'<input type="text" id="OrderID" value="\'+OrderID+\'">\').appendTo(\'body\')');
			}*/
			$orderID = 'undefined';
			if($noNext==false){
				try{
					sleep(5);
					ScriptHelpers::execute('var OrderID = $(\'input[class="cust-id"][value="'.$reviewerID.'"]\').prev().prev().prev().attr(\'value\'); $(\'<input type="text" id="OrderID" value="\'+OrderID+\'">\').appendTo(\'body\')');
					sleep(2);
					// click the orderID
					$orderID = $this->byId('OrderID')->attribute('value');
					$this->byXPath('//a/strong[text()="'.$orderID.'"]')->click();
				}catch(exception $e){
					ScriptHelpers::execute('$(\'#OrderID\').remove();');
					$this->_getOrderId($review, true);
				}
			}



			// now get the user information
			if($orderID!='undefined'){
				try{
					sleep(2);
					$record['reviewerID'] = $reviewerID;
					$record['orderID'] = $orderID;

					echo '####in-'.$reviewerID.'-'.$this->url().'#####';

					try{
						$record['orderDate'] = $this->byId('myo-order-details-purchase-date')->text();
					}catch(exception $e){
						echo '|||'.$reviewerID.'(order date empty)|||';
						$record['orderDate'] = '';
					}

					try{
						$record['address'] = $this->byId('myo-order-details-buyer-address')->text();
					}catch(exception $e){
						echo '|||'.$reviewerID.'(address empty)|||';
						$record['address'] = '';
					}

					try{
						$record['contact'] = $this->byId('contact_buyer_link')->attribute('href');
					}catch(exception $e){
						$record['contact'] = $this->url();
					}

					$sql = "UPDATE reviewer_contact SET ASIN='".$this->asinID."',orderID='".$record['orderID']."',orderDate='".$record['orderDate']."',contact='".$record['contact']."' WHERE reviewerID='".$record['reviewerID']."'	;";
					echo $sql;
					mysqli_query($this->connection, $sql);
				}catch(exception $e){
					//
					echo '!!ERRRO ('.$reviewerID.')!!';
				}
			}


		}



		public function testReviewerContactOneStar(){
			$this->startTestCase('testReviewerContactOneStar','Get Reviwer contact from 1 star');

			$this->connection =  mysqli_connect(MYSQL_HOST,MYSQL_USERNAME,MYSQL_PASSWORD);
			if(!$this->connection){
				echo 'cant connect';
				exit;
			}
			if(!mysqli_select_db($this->connection, MYSQL_DB)){
				echo 'cant select BD';
				exit;
			}

			$this->rating = 1;

			foreach ($this->asinIDarr  as $asin) {
				$this->asinID = $asin;
				$sql = mysqli_query($this->connection, 'SELECT reviewerID,reviewDate FROM reviewer_contact WHERE contact="" AND reviewDate>"2016-04-07" order BY reviewDate DESC') or die(mysqli_error());
				while ($row = mysqli_fetch_array($sql)) {
					try{
						$this->_getOrderId($row);
					}catch(exception $e){
						// nothing
					}

				}
			}


			/*$sql = mysqli_query('SELECT reviewerID FROM reviewer_contact WHERE orderID=0 AND rating=2');
			while ($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
			   $this->_getOrderId($row['reviewerID']);
			}

			$sql = mysqli_query('SELECT reviewerID FROM reviewer_contact WHERE orderID=0 AND rating=3');
			while ($row = mysqli_fetch_array($sql, MYSQL_ASSOC)) {
			   $this->_getOrderId($row['reviewerID']);
			}*/

		}
	}
?>
