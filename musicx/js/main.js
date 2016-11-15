$('form.ajax').on('submit', function() {
	var that = $(this), // curent object or index form
	url = that.attr('action'), //grabs action from index
	type = that.attr('method'), // grabs method from form
	data = {}; // holds our data

	that.find('[name]').each(function(index, value){ // find anything with a attribute of name
		var that = $(this),
		name = that.attr('name'),
		value = that.val();

		data[name] = value;
	});
	

	$.ajax({
		url: url,
		type: type,
		data: data,
		success: function(response){
			console.log(response);
			//var it = console.log(response);
			//echo 'ite'
			console.log(data);
			console.log('trigger2');
		}
	});
	
	console.log('trigger');
	return false;
});