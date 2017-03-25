<?php 
	require_once (__DIR__.'/../../library/core.php');

	class verifyTrackingReviewer extends Core
	{	
		var $asinID = 'B01DKQJP2E';
		var $reviewPage = 'https://www.amazon.com/product-reviews/B01DKQJP2E/ref=cm_cr_arp_d_viewopt_srt?ie=UTF8&refRID=19FKR70FR24JJR7C415D&pageNumber=1&sortBy=recent&reviewerType=all_reviews&filterByStar=';
		var $rating;
		var $reviewID;
		
		var $arrReviewerId = array();

		var $connection;

		public function setUpPage(){

			$this->url($this->reviewPage);
		}


		/**
		* get review ID based on position
		*
		*/
		public function _getReviewID(){
			// connect to mysql
			$this->connection =  mysql_connect(':/Applications/MAMP/tmp/mysql/mysql.sock','root','root');
			if(!$this->connection){
				echo 'cant connect';
				exit;
			}
			if(!mysql_select_db('db_amazon_tracking')){
				echo 'cant select BD';
				exit;
			}

			// go to url
			switch ($this->rating) {
				case '1':
					$this->url($this->reviewPage.'one_star');
					break;
				case '2':
					$this->url($this->reviewPage.'two_star');
					break;
				case '3':
					$this->url($this->reviewPage.'three_star');
					break;
				case '4':
					$this->url($this->reviewPage.'four_star');
					break;
				case '5':
					$this->url($this->reviewPage.'five_star');
					break;
				
				default:
					# code...
					break;
			}
			

			// check ccount page 
			$css = '#cm_cr-pagination_bar > ul.a-pagination > li:nth-child(7) a';
			$countPage = $this->byCssSelector($css)->text();
			$this->arrReviewerId = array();
			for($page=1;$page<$countPage;$page++){
				$css = '#cm_cr-pagination_bar > ul.a-pagination > li.a-last a';
				$this->byCssSelector($css)->click();
				sleep(4);

				// each page
				$countElement = count($this->elements($this->using('css selector')->value('#cm_cr-review_list .review')));
				$index = 0; 
				for($a=1;$a<=$countElement;$a++){
					// get the review ID
					try{
						$xPath = '//div[@id="cm_cr-review_list"]/div['.$a.']';
						$reviewID = $this->byXPath($xPath)->attribute('id');
						if(!empty($reviewID)){
							$css = '#'.$reviewID .' .author';
							$reviewIDHref = $this->byCssSelector($css)->attribute('href');
							$arr = explode('/',$reviewIDHref);

							// collect data
							$reviewDate = $this->byCssSelector('#'.$reviewID .' .review-date')->text();
							$reviewDate = str_replace('on ', '', $reviewDate);
							$reviewDateFormat = date('Y-m-d',strtotime($reviewDate));

							$asinID = $this->arrReviewerId[$index]['asinID']  = $this->asinID;
							$reviewerID = $this->arrReviewerId[$index]['reviewID']  = $arr[6];
							$reviewDate = $this->arrReviewerId[$index]['reviewDate']  = $reviewDateFormat;
							$reviewContent = $this->arrReviewerId[$index]['reviewContent']  = $this->byCssSelector('#'.$reviewID .' .review-data .review-text')->text();
							$rating = $this->arrReviewerId[$index]['rating']  = $this->rating;
							
							$sql = "REPLACE INTO  reviewer_contact(reviewerID,ASIN,reviewDate,reviewContent,rating,product) VALUES('$reviewID','$asinID','$reviewDate','$reviewContent','$rating','deodorant');";
							echo $sql;
							mysql_query($sql);

							$index++;
						}
					}catch(exception $e){
						//
					}
				}
			}

			// save to DB
			print_r($this->arrReviewerId);
			
		}	

		public function _getOrderId($reviewerID,$nextPage=false){
			if($nextPage==false){
				// select adv search
				$this->url($this->advSearchPage);
				$this->select($this->byName('searchType'))->selectOptionByValue('ASIN');
				$this->byName('searchKeyword')->value($this->asinID);
				$this->select($this->byName('preSelectedRange'))->selectOptionByValue('7');
				$this->byName('Search')->click();

				// select all item
				$this->select($this->byCssSelector('.tiny < select.itemsPerPage'))->selectOptionByValue('100');
				$this->byCssSelector('.myo_list_orders_search_form > table > tbody > tr > td:nth-child(4) input')->click();
			}else{
				$this->byXPath('//a[@class="myo_list_orders_link"][text()="Next"]')->click();
			}
			
			$this->waitForIdGone('_myoLO_searchOrdersInProgressLoadingImage',10);

			$source = $this->source();
			$source = strtolower($source);
			$text = strtolower($texts);
			if ( strpos((string)$source,$reviewerID) == FALSE){
				$this->_getOrderId($reviewerID, true);
			}else{
				ScriptHelpers::execute('var OrderID = $(\'input[class="cust-id"][value="'.$reviewerID.'"]\').prev().prev().prev().attr(\'value\'); $(\'<input type="text" id="OrderID" value="\'+OrderID+\'">\').appendTo(\'body\')');
			}

			// click the orderID
			$orderID = $this->byId('orderID')->attribute('value');
			$this->byXPath('//a/strong[text()="'.$orderID.'"]')->click();

			// now get the user information
			$record['ASIN'] = $this->asinID;
			$record['product'] = $this->product;
			$record['rating'] = $this->rating;
			$record['reviewID'] = $this->reviewID;
			$record['reviewerID'] = $this->reviewerID;

			print_r($record);


		}

		/**
		* Get Reviwer contact and then save to database (one star)
		*
		*/
		public function testGetReviewerContactOneStar(){
			$this->rating = 1;
			$this->_getReviewID();
		}

		/**
		* Get Reviwer contact and then save to database (two star)
		*
		*/
		public function testGetReviewerContactTwoStar(){
			$this->rating = 2;
			$this->_getReviewID();
		}

		/**
		* Get Reviwer contact and then save to database (three star)
		*
		*/
		public function testGetReviewerContactThreeStar(){
			$this->rating = 3;
			$this->_getReviewID();
		}

		/**
		* Get Reviwer contact and then save to database (four star)
		*
		*/
		public function testGetReviewerContactFourStar(){
			$this->rating = 4;
			$this->_getReviewID();
		}

		/*public function testOneStartReviwerContact(){
			$this->startTestCase('testOneStartReviwerContact','Get Reviwer contact from 1 star');

			$this->testGetReviewerContact('one_star');
		}*/
	}
?>