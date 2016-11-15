<?php
	include_once "connection.php";

	?>


<!DOCTYPE html>
<html>
<body>

<div class="state">
	<select name="state">
		<option value="">Select State</option>
		//populate value using php
		<?php
			$query = "SELECT * FROM state";
			$results = mysqli_query($con, $query);

			foreach ($results as $state) {
			?>
			<option value="<?php echo $state["cityid"]; ?>"><?php echo $state["state"]; ?></option>
			<?php
		    }
		?>
		
	</select>
</div>


<div class="city">
	<select name="city">
		<option value=""></option>
		

	</select>
</div>
</body>
</html>