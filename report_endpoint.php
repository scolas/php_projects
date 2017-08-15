<?

	require_once( '/home/DBConnect.php' );



	$return_data = array();
	$order_data = array();
	$sku_data = array(
		'small'=> 0,
		'large'=> 0,
		'bundle'=> 0
	);
	$json_data = array(
		'message' => "",
		'count' => 0,
		'data' => array(),
		'action' => '',
		'counter' => '',
		'orders' =>'',
		'order_sum' => 0,
		'date_status' => '',
		'sub'=> array(),
		'sku'=>array(),
		'sku2'=>array(),
		'date'=>array(),
		'date2'=>array(),
		'digi'=>array(),
		'linecount' => 0
	);

	$action = isset( $_GET['action'] ) ? $_GET['action'] : "";

	$json_data['action'] = $action;





	if( $action == 'date_entered' ) {
		$d =$_GET['start'];
		$e =$_GET['end'];
		$arr = explode("/", $d);
		$arr2 = explode("/", $e);


		//date validation
		if(checkdate($arr[0],$arr[1],$arr[2]) && checkdate($arr2[0],$arr2[1],$arr2[2])){
			$json_data['date_status'] = true;
			$start_date = isset( $_GET['start'] ) ? date('Y-m-d', strtotime($_GET['start'])).' 00:00:00' : "";
			$end_date = isset( $_GET['end'] ) ? date('Y-m-d', strtotime($_GET['end'])).' 23:59:59' : "";
		}
        else{
			$json_data['date_status'] = "false";
		}


		if( !empty( $start_date) && !empty( $end_date) ) {

			//$query = DB_query("SELECT orders.id, orders_item.subt FROM orders, orders_item WHERE (orders.enter_date >="1 /01 /2017 AND orders.enter_date <=1 /07 /2017) AND orders.id = orders_item.order_ID LIMIT 30");

			$query = DB_query("SELECT orders.id, orders.enter_date , orders_item.subt, orders_item.sku
			FROM orders, orders_item
			WHERE (
		    orders.enter_date >='". mysql_real_escape_string( $start_date )."'
			AND orders.enter_date <='". mysql_real_escape_string( $end_date )."'
			)
			AND orders.id = orders_item.order_ID
			AND orders.sale = '1'
			AND orders.cancelled = '0'
			AND orders.billed = '1'
			AND orders_item.product_ID = '22445'

			");



			if( $query['number'] ){

				$json_data['message'] = "found matches";
				//$json_data['data'] = $start_date;
				while ( $row = DB_array( $query['result'])){
					$json_data['data'][] = $row;
					$json_data['sku2'][] = $row['sku'];

					if(isset($return_data[$row['subt']])){
						$return_data[$row['subt']] +=1;
					}
					else{
						$return_data[$row['subt']] = 1;
					}


					if(isset($order_data[$row['id']])){
						$order_data[$row['id']] +=1;
					}
					else{
						$order_data[$row['id']] = 1;
						$json_data['order_sum']++;
					}


				$p1 = "/DigiFeeLarge/";
				$p2 = "/DigiFeeBundle/";
				$p3 = "/ DigiFeeSmall/";
				$rex ="(([12][0-9](([02468][048])|([13579][26])))[-/](02[-/](0[1-9]|1[0-9]|2[0-9]))|(([12][0-9][0-9][0-9])[-/]((02[-/](0[1-9]|1[0-9]|2[0-8]))|((0[13578]|10|12)[-/](0[1-9]|[12][0-9]|3[01]))|((0[469]|11)[-/](0[1-9]|[12][0-9]|30)))))";

				//$current = '';
				$current_date = '';
				if(preg_match($rex,$row['enter_date'],$matches, PREG_OFFSET_CAPTURE )){
					$current_date = date('U',strtotime($matches[0][0]));
					if(!isset($json_data['date'][$current_date])){

						if(isset($json_data['date'][$current_date][(int)$row['subt']])){
							$json_data['date'][$current_date][(int)$row['subt']] ++;
						}
						else{
							$json_data['date'][$current_date][(int)$row['subt']] =1;
						}

					}
					else{
						if(isset($json_data['date'][$current_date][(int)$row['subt']])){
							$json_data['date'][$current_date][(int)$row['subt']] ++;
						}
						else{
							$json_data['date'][$current_date][(int)$row['subt']] = 1;
						}

					}

					if(isset($json_data['date'][$current_date][9999])){
						$json_data['date'][$current_date][9999] ++;
					}
					else{
						$json_data['date'][$current_date][9999] = 1;
					}

				}


				$current_date = date('Y-m-d', $current_date);
				if(preg_match($p3,$row['sku'],$matches, PREG_OFFSET_CAPTURE )){
					$sku_data['small'] ++;

					if(!isset($json_data['digi'][$current_date]['small'])){

						$json_data['digi'][$current_date]['small'] =1;
					}
					else{
						$json_data['digi'][$current_date]['small'] ++;
					}

				}
				else if(preg_match($p2,$row['sku'],$matches, PREG_OFFSET_CAPTURE )){
					$sku_data['bundle'] ++;

					if(!isset($json_data['digi'][$current_date]['bundle'])){
						$json_data['digi'][$current_date]['bundle'] =1;
					}
					else{
						$json_data['digi'][$current_date]['bundle'] ++;
					}
					//$json_data['date'][$current]['bundle'] ++;
				}
				else if(preg_match($p1,$row['sku'],$matches, PREG_OFFSET_CAPTURE )){
					$sku_data['large'] ++;

					if(!isset($json_data['digi'][$current_date]['large'])){
						$json_data['digi'][$current_date]['large'] = 1;
					}
					else{
						$json_data['digi'][$current_date]['large'] ++;
					}
				}

					$json_data['sub'][]= number_format($row['subt'],2,'.',',');
				}
			}

			else {
				$json_data['message'] = "No order";
			}
		} else {
			$json_data['message'] = "empty";
		}
	}



	else {
		$json_data['message'] = "action not recognized";
	}



		$date_size= '';


 					$inside = array( );
					foreach ($json_data['date'] as $date =>$item_val ){
						foreach($return_data as $key => $val){
							if(!isset($json_data['date'][$date][(int)$key])){
								$json_data['date'][$date][(int)$key] = 0;

							}
							else{
								$json_data['date'][$date][(int)$key] = $json_data['date'][$date][(int)$key];


							}
							if(!isset($inside[(int)$key])){
								$inside[(int)$key] = 0;
							}
						}
						$date_size = $date;


						ksort($json_data['date'][$date]);

					}

					foreach($json_data['date'] as $key=>$val){
						$current_date = date('Y-m-d', $key);
						$json_data['date2'][$current_date]=$val;
					}



					$graph_line = count($inside);
					$inside[9999] = 0;


				ksort($inside);



	foreach($json_data['digi'] as $key => $val){
		foreach($sku_data as $k => $v){
			if(isset($json_data['digi'][$key][$k])){
				//if small, or large , or bundle not in the digi date add and set to 0
			}
			else{
				$json_data['digi'][$key][$k] = 0;
			}
		}
		ksort($json_data['digi'][$key]);
	}





	$json_data['count'] = count( $json_data['data'] );
	$json_data['counter'] = $return_data;
	$json_data['orders'] = $order_data;
	$json_data['sku'] = $sku_data;
	$json_data['type'] = $inside;
	$json_data['linecount'] = $graph_line;


	header('Content-type: application/json');
	//ra($json_data['type']);
	echo json_encode( $json_data );

	?>
