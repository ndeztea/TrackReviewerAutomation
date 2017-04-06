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
			$this->connection =  mysql_connect(MYSQL_HOST,MYSQL_USERNAME,MYSQL_PASSWORD);
			if(!$this->connection){
				echo 'cant connect';
				exit;
			}
			if(!mysql_select_db(MYSQL_DB)){
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
				
				sleep(4);

				// each page
				$countElement = count($this->elements($this->using('css selector')->value('#cm_cr-review_list .review')));
				$index = 0; 
				for($a=1;$a<=$countElement;$a++){
					// get the review ID
					//try{
						$xPath = '//div[@id="cm_cr-review_list"]/div['.$a.']';
						$reviewID = $this->byXPath($xPath)->attribute('id');
						if(!empty($reviewID)){
							$css = '#'.$reviewID .' .review-byline a.a-size-base.a-link-normal.author';
							$reviewIDHref = $this->byCssSelector($css)->attribute('href');
							$arr = explode('/',$reviewIDHref);
							print_r($arr);

							// collect data
							$name = $this->byCssSelector($css)->text();
							$reviewDate = $this->byCssSelector('#'.$reviewID .' .review-date')->text();
							$reviewDate = str_replace('on ', '', $reviewDate);
							$reviewDateFormat = date('Y-m-d',strtotime($reviewDate));

							$asinID = $this->arrReviewerId[$index]['asinID']  = $this->asinID;
							$reviewerID = $this->arrReviewerId[$index]['reviewID']  = $arr[6];
							$reviewDate = $this->arrReviewerId[$index]['reviewDate']  = $reviewDateFormat;
							$reviewContent = $this->arrReviewerId[$index]['reviewContent']  = $this->byCssSelector('#'.$reviewID .' .review-data .review-text')->text();
							$rating = $this->arrReviewerId[$index]['rating']  = $this->rating;
							
							$sql = "REPLACE INTO  reviewer_contact(reviewerID,name,ASIN,reviewDate,reviewContent,rating,product) VALUES('$reviewerID','".addslashes($name)."','$asinID','$reviewDate','".addslashes($reviewContent)."','$rating','deodorant');";
							//echo $sql;
							mysql_query($sql);

							$index++;
						}
					//}catch(exception $e){
						//
					//}
				}
				$css = '#cm_cr-pagination_bar > ul.a-pagination > li.a-last a';
				$this->byCssSelector($css)->click();
			}

			// save to DB
			//print_r($this->arrReviewerId);
			
		}	

		
		/**
		* Get Reviwer contact and then save to database (one star)
		*
		*/
		public function testGetReviewerContact(){
			$this->rating = 2;
			$this->_getReviewID();
		}

		
		/*public function testOneStartReviwerContact(){
			$this->startTestCase('testOneStartReviwerContact','Get Reviwer contact from 1 star');

			$this->testGetReviewerContact('one_star');
		}*/
	}
?>