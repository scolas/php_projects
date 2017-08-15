<?
	/*AUTHOR:Scott Colas   */








	require_once( 'Database_connect.php' );

	/*! Output */
	$PAGETITLE = substr( $_SERVER['SCRIPT_NAME'], strrpos( $_SERVER['SCRIPT_NAME'], '/' ) + 1 );
	$PAGETITLE = str_replace( '.php', '', $PAGETITLE );
	$PAGETITLE = str_replace("_"," ",$PAGETITLE);
	$PAGETITLE = ucwords( $PAGETITLE );
	?>

<?php header('X-UA-Compatible: IE=edge'); // prevents IE from buggering up locally ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<title><?php echo $PAGETITLE; ?></title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
		 google.charts.load('current', {'packages':['corechart']});



			/* INLINE JS */
			jQuery( function(){
				jQuery( "#datepicker" ).datepicker();
				jQuery( "#datepicker_end" ).datepicker();
			});





			jQuery(document).ready( function(){

				$('#submit').on('click', function(){

						var start_date = jQuery.trim($( '#datepicker' ).val());
						var end_date = jQuery.trim($( '#datepicker_end' ).val());



						if(validate()){
						jQuery.ajax({
							url: 'report_endpoint.php' ,
							method: 'GET',
							cache: false,
							data:{
								action: 'date_entered',
								start: start_date,
								end: end_date
							},
								success: function ( results ){
								if(results.message == "found matches"){
									jQuery( '.table' ).empty();
									jQuery('.table').append('<tr><th>Order ID</th><th>SKU</th><th>Sub-Total</th></tr>');

									//loop for all order id and sub-total
									for (i in results.data){
										jQuery('.table').append('<tr><td ><div class="node"><a href="/home/finder/view.php?order_id='+results.data[i].id+'"target="_blank">'+results.data[i].id+'</a></td> <td> <span>'+results.data[i].sku+'</span></div></td>  <td> <span class="subt">$'+results.sub[i]+'</span></div></td></tr>');
									}

									jQuery( '#json_output' ).empty();
									jQuery( '.table2' ).empty();
									jQuery('.table2').append('<tr><th>Category</th><th>Total</th></tr>');

									// loop through counter array in results
									jQuery.each(results.counter, function(key, value){
										jQuery('.table2').append('<tr><td >Line item price $'+key+'</td><td>'+value+'</td></tr>');

									});
									jQuery('.table2').append('<tr><td>DigiFeeSmall</td><td>'+results.sku['small']+'</td></tr>');
									jQuery('.table2').append('<tr><td>DigiFeeLarge</td><td>'+results.sku['large']+'</td></tr>');
									jQuery('.table2').append('<tr><td>DigiFeeBundleSmallLargeC</td><td>'+results.sku['bundle']+'</td></tr>');
									jQuery('.table2').append('<tr><td>Total line items</td><td>'+results['count']+'</td></tr><tr><td>Total orders</td><td>'+results['order_sum']+'</td></tr>');










									var data = new google.visualization.DataTable();
									data.addColumn('string',"Month");

									var data_bundle = new google.visualization.DataTable();
									data_bundle.addColumn('string',"Month");
									data_bundle.addColumn('number',"Bundle");
									data_bundle.addColumn('number',"Large");
									data_bundle.addColumn('number',"Small");

									var type = [];
									var digi_price = [];
									var dates = [];
									var vals = [];
									var count  = 0;
									var data_row = [];




									jQuery.each(results.type, function(key, value){
										digi_price.push(key);
									});

									jQuery.each(digi_price, function(key, value){
										type.push(key);
										if(value == 9999){
											data.addColumn('number', 'Total');
										}
										else{
											data.addColumn('number', value);
										}
									});







									jQuery.each(results.date2, function(key, value){
											dates.push(key);
											vals.push(value);
									});





									var counter2 = 0;

									jQuery.each(results.digi, function(key, value){
										var bundle_vals = [];
										bundle_vals.push(key);
										jQuery.each(value, function(key, val){
											bundle_vals.push(val);
										});
										console.log(bundle_vals);
										data_bundle.addRow(bundle_vals);
										counter ++;
									});


									jQuery.each(vals, function(val, i){
										var data_row_tmp = [];
										jQuery.each(i, function(key, value){
												data_row_tmp.splice(count, 0 , value);
												count ++;

											});
											data_row.push(data_row_tmp);
									});


									var counter = 0;
									jQuery.each(results.date2, function(key, value){
										var date_vals = [];
										date_vals.push(key.toString());

 											jQuery.each(data_row[counter], function(key, val){
												date_vals.push(val);
											});
										data.addRow(date_vals);
										counter ++;
									});






									google.charts.load('current', {'packages':['corechart']});
									google.charts.setOnLoadCallback(drawVisualization(data, data_bundle));

									function drawVisualization(data, data_bundle, line) {

										var line_count = results.linecount;
										var ser = {};
										ser[line_count]= {type: 'line'};
										var options = {
										  title : 'Digitization Fees By Price',
										  vAxis: {title: 'Total'},
										  hAxis: {title: 'Month'},
										  seriesType: 'bars',
										  series: {[line_count]: {type: 'line'}}
										};

										var options2 = {
										  title : 'Digitization ',
										  vAxis: {title: 'Total'},
										  hAxis: {title: 'Month'},
										  seriesType: 'bars',
										  series: {4: {type: 'line'}}
										};

										var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
										chart.draw(data, options);

										var chart2 = new google.visualization.ComboChart(document.getElementById('chart_div2'));
										chart2.draw(data_bundle, options2);



									}

								}
								else if (results.message == "No order"){
									alert("No order");
								}
								else if (results.message == "empty"){
									alert("empty");
							    }
								else {
										alert("error");
								}
							}
						});
						}
						else{

						}


				});





			function validate(){
				var pattern = /^(0|1)?\d\/(0|1|2|3)?\d\/\d{4}$/;
				if(jQuery('#datepicker').val() == "" || jQuery('#datepicker_end').val() == "" ){

					alert('enter date');
					return false;
				}
				else if( !pattern.test(jQuery('#datepicker').val())){
					alert('Enter correct start date format');
					return false;
				}
				else if( !pattern.test(jQuery("#datepicker_end").val())) {
					alert('Enter correct end date format');
					return false;
				}


					return true;

			}

			});


		</script>

		<style type="text/css">

			.table_1{
				float: left;
				padding: 5px;
			}
			.table_2{
				float: left;
				padding: 5px;
			}

			table,th, td {
				border: 1px solid black;
				border-collapse: collapse;
			}


			th, td{
				padding: 5px;
				text-align: left;
			}
			th{
				background-color: #dae2ef;
			}
			html, body{
				font-family:sans-serif;
			}

		</style>
	</head>
	<body>


		<h1>Digitization Fee Report</h1>
		<div>
		<form name="report_form" onsubmit="return validate();">
			Start Date: <input type="text" id="datepicker" value="<?php echo date("m/"."01"."/Y"); ?>" required>
			End Date: <input type="text" id="datepicker_end" value="<?php echo date("m/t/Y"); ?>" reqrequireduierd>
			<input type="button" id="submit" value="submit" />
		</form>
		</div>
	<div id="chart_div" style="width: 1600px; height: 600px;"></div>
	<div id="chart_div2" style="width: 1600px; height: 400px;"></div>
	<div class="table_1">
		<table class="table">
				<tr>
					<th>Order ID</th>
					<th>SKU</th>
					<th>Sub-Total</th>
				</tr>
		</table>
	</div>
	<div class="table_2">
		<table class="table2">
				<tr>
					<th>Category</th>
					<th>Total</th>
				</tr>

		</table>
	</div>


	</body>
 </html>
