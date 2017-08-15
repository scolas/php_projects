<?php


require_once("/home/DBConnect.php");




class customerReview{

	var $myVar = '';
	var $get_reviews = '';

	public function filter_reviews($input_array){

		global $DB;
		$go_time = time();
		$sql_array = array();
		$subs_temp = array();
		$posts = array();
		$optional_response = "9,10,11,17,18";
		$brand_name='';
		$commaSubSep = '';
		$brands = false;
		$productName = !empty($input_array['pName'])  ? true : false ;
		$arrayFull = (!empty( $input_array['array']) ) ? true : false;
		$bool = !empty($input_array['array']) ? true : false;
		$pName = (!empty($input_array['pName'] )) ? $input_array['pName'] : '' ;
		$temp = $arrayFull ? ((array)$input_array['array']) : '';
		$commaSep = $arrayFull ? implode("," , $temp ) : '';
		$brands = !empty($input_array['brand_id'] ) ? true : false;
		$brand_name = !empty($input_array['brand_id'] ) ? $input_array['brand_id'] : '';
		$subs = isset($input_array['sub']) ? $input_array['sub'] : 0 ;
		$p_type = isset($input_array['type']) ? $input_array['type'] : '';
		$p_type_bool = isset($input_array['type']) ? true : false;
		$classifiers = false;
/*  	$classifiers = !empty( $input_array['classifier'])  ? true : false;

		if($classifiers){
			$key_holder = $this->classifier_id($input_array['classifier']);
			$classifier_temp = $classifiers ? $key_holder : '';
			$classifier_List = $classifiers ? implode("," , $classifier_temp ) : '';

		} */
		if($subs && $arrayFull && false){
			$arrayFull =  false;
			$bool = !empty( $input_array['array']) ;
			$subs_temp = $this->subId($temp);
			$commaSubSep = $subs ? implode(",", $subs_temp) : '';
		}


		$date_bool = !empty($input_array['start']) && !empty($input_array['end']);
		$start_date = $date_bool ? date('U', strtotime($input_array['start'])): "";
		$end_date = $date_bool ? date('U', strtotime($input_array['end'])): "";

 		$fday = date( "m/"."01"."/Y");
		$lday = date("m/t/Y");
		$first_day = date('U', strtotime($fday));
		$last_day = date('U', strtotime($lday));
		$active = true;
		$limit = $input_array['limit'];
		$limit1 = (int) $limit	;

		$filterBy =  (in_array($input_array['filter'], Array(1, 2))) ? true : false;
		//$filterBy = false;
		//echo 'test'.$filterBy;
		$fil = $filterBy ? $input_array['filter'] : 1;
		if(!$input_array['filter']){
			$filterBy = true;
			$fil = 0;
		}

		$gt = " SELECT lpt.post_id, customer_post.post_time, lpt.post_subject, lpt.comments, lpt.nav_rating, lpt.od_rating, lpt.pv_rating, lpt.pq_rating,
			lpt.pz_rating, lpt.finalAvg, lpt.buy_again, lpt.order_num, lpt.market_type, lpt.product_type, lpt.active, lpt.market_type,
			o_items.product_name, o_items.product_ID, o_items.sku, o_items.product_ID,
			product_type.friendly_name, orders.id, orders.fname, orders.lname, orders.email1
				FROM customer_post AS lpt, customer_post
				INNER JOIN o_items, products_main, product_type, orders
				WHERE customer_post.post_id = lpt.post_id
				AND o_items.order_ID = orders.id
				AND lpt.order_num = o_items.order_ID
				".( $date_bool ? "AND lpt.display_time > '".mysql_clean($start_date)."'" : "AND lpt.display_time >=  '".mysql_clean($first_day)."'")."
				".( $date_bool ? "AND lpt.display_time < '".mysql_clean($end_date)."'" : "AND lpt.display_time <= '".mysql_clean($last_day)."' ")."
				AND customer_post.forum_id =9
				AND lpt.publish =1
				AND o_items.product_ID = p_main.id
				AND product_type.ID = p_main.brand_classifier_ID
				".( $productName ? "AND o_items.product_name LIKE '%".mysql_clean($pName)."%'" : "" )."
				".( $brands ? "AND product_type.friendly_name LIKE '%".mysql_clean($brand_name)."%'" : "" )."
				".( $arrayFull ? "AND o_items.product_ID IN (".mysql_clean($commaSep).")" : "" )."
				".( ($subs && $bool) ? "AND o_items.product_ID IN (".mysql_clean($commaSubSep).")" : "" )."
				".( $filterBy ? " AND lpt.active = ".$fil." " : "AND (lpt.active = 1 OR lpt.active = 0)" )."
				".( ($p_type_bool)  ? "AND (lpt.product_type LIKE '".mysql_clean($p_type)."' or lpt.market_type LIKE '".mysql_clean($p_type)."')  " : "" )."
				ORDER BY lpt.display_time DESC
				".( (isset($input_array['limit']) && !empty($input_array['limit']))  ? "LIMIT ".mysql_clean($limit1)." ,2 " : "LIMIT 700" )."
		";

		$get_filtered = DB_query($gt, $DB['link_phpBB']);

		//ra($gt);
		$posts = array();
		$orders_tracker = array();

		while($posts_tmp = DB_array($get_filtered['result']))
		{
			if( isset( $orders_tracker[ $posts_tmp['id'] ] ) ) {
				continue;
			}
			$orders_tracker[ $posts_tmp['id'] ] = $posts_tmp['id'];

			$time  = gmdate("m-d-Y", $posts_tmp['post_time']);

			$nv = round($posts_tmp['nav_rating']);
			if(($nv == 6)  || ($nv == 7)){$nv = 5;} elseif(($nv == 4) || ($nv ==5)){$nv = 4;}

			// designers
			$od = round($posts_tmp['od_rating']);
			if(($od == 6) || ($od == 7)){$od = 5;} elseif(($od == 4) || ($od == 5)){$od = 4;}


			// personalization
			$pq = round($posts_tmp['pq_rating']);
			if(($pq == 6) || ($pq ==7)){$pq = 5;} elseif(($pq == 4) || ($pq ==5)){$pq = 4;}

			// value
			$pv = round($posts_tmp['pv_rating']);
			if(($pv == 6) || ($pv == 7)){$pv = 5;} elseif(($pv == 4) || ($pv == 5)){$pv = 4;}

			// turn around
			$pz = round($posts_tmp['pz_rating']);
			if(($pz == 6) || ($pz == 7)){$pz = 5;} elseif(($pz == 4) || ($pz == 5)){$pz = 4;}

			// turn around
			$av = round($posts_tmp['finalAvg']);
			if(($av == 6) || ($av == 7)){$av = 5;} elseif(($av == 4) || ($av == 5)){$av = 4;}


			$posts_tmp['nav_rating']=$nv;
			$posts_tmp['od_rating']=$od;
			$posts_tmp['pq_rating']=$pq;
			$posts_tmp['pv_rating']=$pv;
			$posts_tmp['pz_rating']=$pz;
			$posts_tmp['finalAvg']=$av;
			$posts_tmp['post_time']=$time;

 			if($classifiers ){
				if(isset($key_holder[$posts_tmp['product_ID']])){
					$posts[] = $posts_tmp;
				}
			}
			else{
				$posts[] = $posts_tmp;
			}

		}
		return $posts;
	}

	public function getsurvey1($input_array){
		global $DB;
		$active = true;
		$p_type = isset($input_array['type']) ? $input_array['type'] : '';
		$p_type_bool = isset($input_array['type']) ? true : false;
		$limit = $input_array['limit'];
		$limit1 = (int) $limit;

		$filterBy =  (in_array($input_array['filter'], Array(1, 2))) ? true : false;
		$fil = $filterBy ? $input_array['filter'] : 1;
		if(!$input_array['filter']){
			$filterBy = true;
			$fil = 0;
		}

		 $rp1 = "
			SELECT csr.identifier AS order_ID, csr.timestamp, csr.question, csr.response, o.fname, o.lname, o.email1, csr.active, csr.product_type, csr.market_type, o_items.product_ID
				FROM survey_r AS csr
				INNER JOIN orders AS o, o_items
				WHERE csr.survey_ID =2
					AND csr.identifier = o.id
					AND o.id = o_items.order_ID
					AND csr.identifier
					IN (
						SELECT identifier
						FROM survey_r
						WHERE survey_ID =2
						AND question =11
						AND response !=  ''
					)
					AND o.id = csr.identifier
					AND (
						csr.question =8
						OR csr.question =11
					)
					".( $filterBy ? " AND csr.active = ".$fil." " : "AND (csr.active = 1 OR csr.active = 0 OR csr.active = 2)" )."
					".( ($p_type_bool)  ? "AND (csr.product_type LIKE '".mysql_clean($p_type)."' or csr.market_type LIKE '".mysql_clean($p_type)."')  " : "" )."
					".( (isset($input_array['limit']) && !empty($input_array['limit']) )  ? "LIMIT ".mysql_clean($limit1)." ,2 " : "LIMIT 500" )."

		";


		//ra($rp1);
		$reviews_product = DB_query( $rp1, $connection);

		$sql_array = array();
		while($arr = DB_array($reviews_product['result'])){
			if(isset($sql_array[$arr['order_ID']])){
				$sql_array[$arr['order_ID']]['question2'] = $arr['question'];
				$sql_array[$arr['order_ID']]['response2'] = $arr['response'];
			}
			else{
				$sql_array[$arr['order_ID']] = $arr;

  				$rating = $arr['response'];
				if(($rating == 10) || ($rating == 9)){$rating = 5;}
				elseif(($rating == 8) || ($rating == 7)){$rating = 4;}
				elseif(($rating == 6) || ($rating == 5)){$rating = 3;}
				elseif(($rating == 4) || ($rating == 3)){$rating = 2;}
				elseif(($rating == 2) || ($rating == 1)){$rating = 1;}

				$sql_array[$arr['order_ID']]['response'] = $rating;
			}
			$sql_array[$arr['order_ID']]['source'] = 'getSurvey1';
		}

		return($sql_array);
	}







	public function getsurvey($input_array){
		$sql_array = array();
		$option = isset($input_array['option']) ? $input_array['option'] : 0 ;
		$optional_response = "9,10,11,17,18";
		global $DB;
		$subs_temp = array();
		$brands = false;
		$brand_name='';
		$commaSubSep = '';
		$bool = !empty($input_array['array']) ? true : false;
		$productName = !empty($input_array['pName'])  ? true : false;
		$arrayFull = (!empty( $input_array['array']) ) ? true : false;
		$pName = (!empty($input_array['pName'] )) ? $input_array['pName'] : '';
		$temp = $arrayFull ? ((array)$input_array['array']) : '';
		$commaSep = $arrayFull ? implode("," , $temp ) : '';
		$brands = !empty($input_array['brand_id'] ) ? true : false;
		$brand_name = !empty($input_array['brand_id'] ) ? $input_array['brand_id'] : '';
		$subs = isset($input_array['sub']) ? $input_array['sub'] : 0;
		$classifiers = false;
		$filterBy = (isset($input_array['filter']) && $input_array['filter'] != "all") ? true : false;
		$fil = $filterBy ? $input_array['filter'] : 1;
		ra($input_array['filter']);
/* 		$classifiers = (!empty( $input_array['classifier']) ) ? true : false;

		if($classifiers){
			$key_holder = $this->classifier_id($input_array['classifier']);
			$classifier_temp = $classifiers ? $key_holder : '';
			$classifier_List = $classifiers ? implode("," , $classifier_temp ) : '';

		}	 */
		if($subs && $arrayFull && false){
			$arrayFull =  false;
			$bool = !empty( $input_array['array']) ;
			$subs_temp = $this->subId($temp);
			$commaSubSep = $subs ? implode(",", $subs_temp) : '';
		}

		$date_bool = !empty($input_array['start']) && !empty($input_array['end']);
		$start_date = $date_bool ?  date('Y-m-d', strtotime($input_array['start'])).' 00:00:00' : "";
		$end_date = $date_bool ?  date('Y-m-d', strtotime($input_array['end'])).' 24:59:59' : "";

		$day1 = date("m/"."01"."/Y");
		$lday=  date("m/t/Y");
		$first_day = date('Y-m-d', strtotime($day1) ).' 00:00:00' ;
		$last_day = date('Y-m-d', strtotime($lday) ) .' 24:59:59' ;

		$rp ="
			SELECT csr.ID, csr.identifier, csr.timestamp, csr.question, csr.response , orders.id, orders.customer_ID, o_items.product_name , customersurvey__surveys_questions.ID, customersurvey__surveys_questions.question, o_items.product_ID, p_main.brand_classifier_ID, product_type.friendly_name
				FROM  `survey_r` AS csr
				INNER JOIN orders, o_items, customersurvey__surveys_questions, p_main, product_type
				WHERE
				".( $date_bool ? "csr.timestamp >= '".mysql_clean($start_date)."'" : "csr.timestamp >= '".mysql_clean($first_day)."'")."
				".( $date_bool ? "AND csr.timestamp <= '".mysql_clean($end_date)."'" : "AND csr.timestamp <= '".mysql_clean($last_day)."'")."
				AND csr.identifier = orders.id
				AND orders.id = o_items.order_ID
				AND csr.question = customersurvey__surveys_questions.ID
				AND o_items.product_ID = p_main.id
				AND product_type.ID = p_main.brand_classifier_ID
				".( $productName ? "AND o_items.product_name LIKE '%".mysql_clean($pName)."%'" : "" )."
				".( $arrayFull ? "AND o_items.product_ID IN (".mysql_clean($commaSep).")" : "" )."
				".( $brands ? "AND product_type.friendly_name LIKE '%".mysql_clean($brand_name)."%'" : "" )."
				".( $option ? "AND customersurvey__surveys_questions.ID IN (".mysql_clean($optional_response).")" : "" )."
				".( ($subs && $bool) ? "AND o_items.product_ID IN (".mysql_clean($commaSubSep).")" : "" )."
				LIMIT 15
				";

 		$reviews_product = DB_query($rp, $DB_conn);

		while($arr = DB_array($reviews_product['result'])){
			$arr['survey_review'] = 'survey';
			$time1  = strtotime($arr['timestamp']);
			$time  = date("m-d-Y", $time1);
			$arr['timestamp'] = $time;

 			if($classifiers ){
				if(isset($key_holder[$arr['product_ID']])){
					if($option){
						$sql_array[] = $arr;
					}
					else{
						$arr['response'] =  $this->score_convert($arr['response']);
						$sql_array[] = $arr;
					}

				}
			}
			else{
				if($option){
					$sql_array[] = $arr;
				}
				else{
					$arr['response'] =  $this->score_convert($arr['response']);
					$sql_array[] = $arr;
				}
			}
		}

		return($sql_array);
	}




	public function product_Feed($input_array){

		global $DB;
		$brands = false;
		$brand_name='';
		$commaSubSep = '';
		$bool = !empty($input_array['array']);
		$productName = !empty($input_array['pName'])  ? true : false ;
		$arrayFull = (!empty( $input_array['array']) ) ? true : false;
		$pName = (!empty($input_array['pName'] )) ? $input_array['pName'] : '' ;
		$temp = $arrayFull ? ((array)$input_array['array']) : '';
		$commaSep = $arrayFull ? implode("," , $temp ) : '';
		$brands = !empty($input_array['brand_id'] ) ? true : false;
		$brand_name = !empty($input_array['brand_id'] ) ? $input_array['brand_id'] : '';
		$subs = isset($input_array['sub']) ? $input_array['sub'] : 0;
		$p_type = isset($input_array['type']) ? $input_array['type'] : '';
		$p_type_bool = isset($input_array['type']) ? true : false;
		$limit = $input_array['limit'];
		$limit1 = (int) $limit;

		$classifiers = false;
/*  	$classifiers = (!empty( $input_array['classifier']) ) ? true : false;
		if($classifiers){
			$key_holder = $this->classifier_id($input_array['classifier']);
			$classifier_temp = $classifiers ? $key_holder : '';
			$classifier_List = $classifiers ? implode("," , $classifier_temp ) : '';
		}  */
		//ra($input_array['classifier']);
		if($subs && $arrayFull && false){
			$arrayFull =  false;
			$bool = !empty( $input_array['array']) ;
			$subs_temp = $this->subId($temp);
			$commaSubSep = $subs ? implode(",", $subs_temp) : '';
		}


		$date_bool = !empty($input_array['start']) && !empty($input_array['end']);
		$start_date = $date_bool ?  date('Y-m-d', strtotime($input_array['start'])).' 00:00:00' : "";
		$end_date = $date_bool ?    date('Y-m-d', strtotime($input_array['end'])).' 24:59:59' : "";

 		$day1 = date("m/"."01"."/Y");
		$lday=  date("m/t/Y");
		$first_day = date('Y-m-d', strtotime($day1) ).' 00:00:00' ;
		$last_day = date('Y-m-d', strtotime($lday) ) .' 24:59:59' ;
		$active = true;

		$filterBy =  (in_array($input_array['filter'], Array(1, 2))) ? true : false;
		$fil = $filterBy ? $input_array['filter'] : 1;
		if(!$input_array['filter']){
			$filterBy = true;
			$fil = 0;
		}

		$rt = "	SELECT pf.review_source, pf.ID ,pf.status, pf.createddate, pf.overallrating, pf.headline, pf.comments, pf.pros, pf.product_id, pf.product_name, pf.brand, pf.reviewer_name, pf.sizing, pf.active, pf.product_type, pf.market_type
				FROM p_reviews AS pf
				".( $date_bool ? "WHERE pf.enter_date >= '".mysql_clean($start_date)."'" : "WHERE pf.enter_date >= '".mysql_clean($first_day)."'")."
				".( $date_bool ? "AND pf.enter_date <= '".mysql_clean($end_date)."'" : "AND pf.enter_date <= '".mysql_clean($last_day)."'")."
				".( $productName ? "AND pf.product_name LIKE '%".mysql_clean($pName)."%'" : "" )."
				".( $arrayFull ? " AND  pf.product_id IN (".mysql_clean($commaSep).")" : "" )."
				".( $brands ? " AND pf.brand LIKE '%".mysql_clean($brand_name)."%'" : "" )."
				".( $filterBy ? " AND pf.active = ".$fil." " : "AND (pf.active = 1 OR pf.active = 0 OR pf.active = 2)" )."
				".( ($subs && $bool)  ? "AND pf.product_id IN (".mysql_clean($commaSubSep).")" : "" )."
				".( ($p_type_bool)  ? "AND (pf.product_type LIKE '".mysql_clean($p_type)."' or pf.market_type LIKE '".mysql_clean($p_type)."')  " : "" )."
				".( (isset($input_array['limit']) && !empty($input_array['limit']))  ? "LIMIT ".mysql_clean($limit1)." ,2 " : "LIMIT 500" )."
				";

		$review_feed = DB_query($rt, $DBconn);
		//ra($rt);
		while($reviews = DB_array($review_feed['result'])) {
			$time1  = strtotime($reviews ['createddate']);
			$time  = date("m-d-Y", $time1);
			$reviews['createddate'] = $time;
 			if($classifiers ){
				if(isset($key_holder[$reviews['product_ID']])){
					$review_feed[] = $reviews;

				}
			}
			else{
				$review_feed[] = $reviews;
			}

		}
		unset($review_feed['result']);
		unset($review_feed['number']);
		return($review_feed);
	}



	public function print_count(){
		global $DB;
		$nv = $od = $cs = $pq = $pv = $pz = $website = $service = $turnaround = 0;
		$go_time = time();

		// get reviews -- insert new query here
		$get_reviews = DB_query("
			SELECT customer_post_text.* FROM customer_post_text,customer_post
			WHERE customer_post.post_id = customer_post_text.post_id
			AND customer_post_text.display_time < '".mysql_clean($go_time)."'
			AND customer_post_text.display_time != 0
			AND customer_post.forum_id = 9
			AND customer_post_text.publish = 1
			ORDER BY customer_post_text.display_time DESC LIMIT 5
		", $DB['link_phpBB']);

		while($posts_tmp = DB_array($get_reviews['result']))
		{
			$posts[] = $posts_tmp;
		}

		$output_index = 0;
		foreach($posts as $key => $val){
			$nv = round($val['nav_rating']);
			if(($nv == 6) || ($nv == 7)){$nv = 5;} elseif(($nv == 4) || ($nv ==5)){$nv = 4;}

			// designers
			$od = round($val['od_rating']);
			if(($od == 6) || ($od ==7)){$od = 5;} elseif(($od == 4) || ($od ==5)){$od = 4;}

			// service
			$cs = round($val['cs_rating']);
			if(($cs == 6) || ($cs == 7)){$cs = 5;} elseif(($cs == 4 )|| ($cs == 5)){$cs = 4;}

			// personalization
			$pq = round($val['pq_rating']);
			if(($pq == 6) || ($pq ==7)){$pq = 5;} elseif(($pq == 4 )|| ($pq == 5)){$pq = 4;}

			// value
			$pv = round($val['pv_rating']);
			if(($pv == 6) || ($pv ==7)){$pv = 5;} elseif(($pv == 4) || ($pv ==5)){$pv = 4;}

			// turn around
			$pz = round($val['pz_rating']);
			if(($pz == 6) || ($pz ==7)){$pz = 5;} elseif(($pz == 4 )|| ($pz==5)){$pz = 4;}

 			$output_array[$output_index]['date'] = date("n/j/y, h:i a",$val['display_time']);
			$output_array[$output_index]['comments'] = strip_tags(nl2br(stripslashes($val['comments']))) ;
			$output_array[$output_index]['website'] = $nv;
			$output_array[$output_index]['value' ] = $od;
			$output_array[$output_index]['designer' ] = $pv;
			$output_array[$output_index]['Service'] = $cs;
			$output_array[$output_index]['personalization'] =$pq;
			$output_array[$output_index]['turn'] = $pz;
			$output_index ++;
		}
		return $output_array;
	}




	public function all_reviews($input_args){

   		$classifiers = (!empty( $input_args['classifier']) ) ? true : false;
		$key_holder = $this->classifier_id($input_args['classifier']);
		$input_args['classifier']= null;
 		$out['post'] = $this->filter_reviews($input_args);
		$out['feed'] = $this->product_Feed($input_args);
		$out['survey'] = $this->getSurvey1($input_args);

 		$out1 = array_merge($out['post'],$out['feed'] );
		$out2 =  array_merge($out1, $out['survey']);

  		$classifier_array = array();

		if($classifiers){

			foreach($out2 as $index => $value){

				if(isset($value['product_id']) && isset($key_holder[$value['product_id']])){
					$classifier_array[] =  $value;
				}
				if(isset($value['product_ID']) && isset($key_holder[$value['product_ID']])){
					$classifier_array[] =  $value;
				}

			}

			return $classifier_array;

		}
		else{
			return $out2;
		}
	}








	public function score_convert($myscore = ''){

		if($myscore=='Yes' || $myscore =='No'){
			return $myscore;
		}
		else{
			return ceil($myscore / 2);
		}

	}


	public function classifier_id($class = ""){
		$classSys = new Classifier('both');
		$class_ids = $classSys->product_reference;

		foreach ($class_ids as $key=>$val){
			$keys[] = $key;
		}

		//$key_holder = (array) $keys;
		$key_holder = array_flip($keys);

		return $key_holder;
	}






	public function subId($product_id = ''){
		global $DB;
		$output_key = array();
		$brand = '';
		$temp = (array)$product_id;

		$product_id = (isset($product_id) && $product_id!=0 ) ? $product_id : '' ;
  		if(empty($product_id)){
			return;
		}
		foreach($temp as $val){
			$product_details = new Product($val);

			$arr = (array) $product_details;

			if($arr['productData']['parent_ID'] == 0){
				//notsub
				$output_key[] = $arr['productData']['id'];
			}
			else{
				//search for subs
				$output_key[] = $arr['productData']['parent_ID'];
				$product_details = new Product($arr['productData']['parent_ID']);
				$arr = (array) $product_details;

			}

			foreach($arr['subproducts'] as $key => $val){
					$output_key[] =$key;
			}

		}
		return $output_key;
	}


}

/*Scott Colas */
?>
