<!DOCTYPE html>
<html>
<body>

<Head><script type= "text/javascript" src = "js/countries.js"></script></Head>
<form action="ajax/contact.php" method="post" class="ajax">
	

	<div>
	<input type="text" name="name" placeholder="first name">
	</div>
	<div>
	<input type="text" name="lname" placeholder="last name">
	</div>
	<div>
	<input type="text" name="email" placeholder="Your email">
	</div>
		<div>
	<input type="text" name="artistname" placeholder="Artist or Band Name">
	</div>
<!-- 	<div>
	 <input list="option" name="browser" placeholder="What are you?">
      <datalist id="option" name="browser">
    	<option value="Artist">
    	<option value="Venue">
    	<option value="Other">
      </datalist>
  </div>  -->
  <div>
     <!-http://abbeyworkshop.com/howto/lamp/php-listbox/index.html-->
<select name="generes">
<option value="Hip Hop" selected>Hip Hop</option>
<option value="Pop">Pop</option>
<option value="Country Music">Country Music</option>
<option value="rock">Rock</option>
<option value="r&b">R&B</option>
</select>
</p>
<div>
<select name="state"><?php echo StateDropdown('Alaska', 'states'); ?></select>
</div>
<!-- 
<div>
  <select id="country" name ="country"></select>
<select name ="state" id ="state"></select>
 <script language="javascript">
//populateCountries("country", "state");
 </script>
</div>
  </div> -->
  <!-- <div>
	<textarea name="message" placeholder="your message"></textarea>
	</div>
  -->
	<input type="submit" value="Send">




</form>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
 
 <script src="js/main.js"></script>



<!-- 
<script>

function loadDoc(){
var xhttp;
if (windo.XMLHttpRequest) {
	xhttp = new XMLHttpRequest();
	} else {
		//code for IE6, IE5
		xhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}



	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function(){
		if (xhttp.readState == 4 && xhttp.status == 200){
			document.getElementById("demo").innerHTML = xhttp.responseText;

		}
	};

	xhttp.open("GET", "")
</script>

 -->


</body>
<?php

/**
 * States Dropdown 
 *
 * @uses check_select
 * @param string $post, the one to make "selected"
 * @param string $type, by default it shows abbreviations. 'abbrev', 'name' or 'mixed'
 * @return string
 */
function StateDropdown($post=null, $type='abbrev') {
	$states = array(
		array('AK', 'Alaska'),
		array('AL', 'Alabama'),
		array('AR', 'Arkansas'),
		array('AZ', 'Arizona'),
		array('CA', 'California'),
		array('CO', 'Colorado'),
		array('CT', 'Connecticut'),
		array('DC', 'District of Columbia'),
		array('DE', 'Delaware'),
		array('FL', 'Florida'),
		array('GA', 'Georgia'),
		array('HI', 'Hawaii'),
		array('IA', 'Iowa'),
		array('ID', 'Idaho'),
		array('IL', 'Illinois'),
		array('IN', 'Indiana'),
		array('KS', 'Kansas'),
		array('KY', 'Kentucky'),
		array('LA', 'Louisiana'),
		array('MA', 'Massachusetts'),
		array('MD', 'Maryland'),
		array('ME', 'Maine'),
		array('MI', 'Michigan'),
		array('MN', 'Minnesota'),
		array('MO', 'Missouri'),
		array('MS', 'Mississippi'),
		array('MT', 'Montana'),
		array('NC', 'North Carolina'),
		array('ND', 'North Dakota'),
		array('NE', 'Nebraska'),
		array('NH', 'New Hampshire'),
		array('NJ', 'New Jersey'),
		array('NM', 'New Mexico'),
		array('NV', 'Nevada'),
		array('NY', 'New York'),
		array('OH', 'Ohio'),
		array('OK', 'Oklahoma'),
		array('OR', 'Oregon'),
		array('PA', 'Pennsylvania'),
		array('PR', 'Puerto Rico'),
		array('RI', 'Rhode Island'),
		array('SC', 'South Carolina'),
		array('SD', 'South Dakota'),
		array('TN', 'Tennessee'),
		array('TX', 'Texas'),
		array('UT', 'Utah'),
		array('VA', 'Virginia'),
		array('VT', 'Vermont'),
		array('WA', 'Washington'),
		array('WI', 'Wisconsin'),
		array('WV', 'West Virginia'),
		array('WY', 'Wyoming')
	);
	
	$options = '<option value=""></option>';
	
	foreach ($states as $state) {
		if ($type == 'abbrev') {
    	$options .= '<option value="'.$state[0].'" '. check_select($post, $state[0], false) .' >'.$state[0].'</option>'."\n";
    } elseif($type == 'states') {
    	$options .= '<option value="'.$state[1].'" '. check_select($post, $state[1], false) .' >'.$state[1].'</option>'."\n";
    } elseif($type == 'mixed') {
    	$options .= '<option value="'.$state[0].'" '. check_select($post, $state[0], false) .' >'.$state[1].'</option>'."\n";
    }
	}
		
	echo $options;
}

/**
 * Check Select Element 
 *
 * @param string $i, POST value
 * @param string $m, input element's value
 * @param string $e, return=false, echo=true 
 * @return string 
 */
function check_select($i,$m,$e=true) {
	if ($i != null) { 
		if ( $i == $m ) { 
			$var = ' selected="selected" '; 
		} else {
			$var = '';
		}
	} else {
		$var = '';	
	}
	if(!$e) {
		return $var;
	} else {
		echo $var;
	}
}
