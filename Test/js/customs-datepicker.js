$(document).ready(function () {
	
	//disable typing in date picker input
	$('#checkIn,#checkOut,#dpdFlight1,#dpdFlight2,#start_date,#end_date,.dateStart-flight,.dateStart-flight1,.dateStart-flight2,.dateEnd-flight,.dateEnd-flight1,.dateEnd-flight2,.pickup-datepicker,.dropoff-datepicker,.tour-datepicker').keydown(function(e) {
		 e.preventDefault();
		 return false;
	});
	//disable typing in date picker input end

	//main date picker
	var nowTemp = new Date();
	// nowTemp.setDate(nowTemp.getDate() + 1);
	var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

	var checkin = $('#checkIn,#dpdFlight1').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top left'
	})
	.on('changeDate', function(e){	
		selStartDate = e.date;
		var nextDay = new Date(e.date);
		nextDay.setDate(nextDay.getDate() + 1);
		$('#checkOut,#dpdFlight2').datepicker('setStartDate', nextDay);
		if(checkout.val() == '') checkout.focus();	

		if (checkout.datepicker('getDate') == 'Invalid Date') {
			var newDate = new Date(e.date)
			newDate.setDate(newDate.getDate() + 1);
			checkout.datepicker('update',newDate);
			checkout.focus();	
		}

	});	    

	var checkout = $('#checkOut,#dpdFlight2').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top'
	})
	.on('changeDate', function(e){					
	});	
	//main date picker end

	var checkin0 = $('.dateStart-flight').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top left'
	})
	.on('changeDate', function(e){	
		selStartDate = e.date;
		var nextDay = new Date(e.date);
		nextDay.setDate(nextDay.getDate() + 1);
		$('.dateEnd-flight').datepicker('setStartDate', nextDay);
		if(checkout0.val() == '') checkout0.focus();	

		if (checkout0.datepicker('getDate') == 'Invalid Date') {
			var newDate = new Date(e.date)
			newDate.setDate(newDate.getDate() + 1);
			checkout0.datepicker('update',newDate);
			checkout0.focus();	
		}

	});	    

	var checkout0 = $('.dateEnd-flight').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top'
	})
	.on('changeDate', function(e){					
	});	
	//main date picker end

	var checkin1 = $('.dateStart-flight1').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top left'
	})
	.on('changeDate', function(e){	
		selStartDate = e.date;
		var nextDay = new Date(e.date);
		nextDay.setDate(nextDay.getDate() + 1);
		$('.dateEnd-flight1').datepicker('setStartDate', nextDay);
		if(checkout1.val() == '') checkout1.focus();	

		if (checkout1.datepicker('getDate') == 'Invalid Date') {
			var newDate = new Date(e.date)
			newDate.setDate(newDate.getDate() + 1);
			checkout1.datepicker('update',newDate);
			checkout1.focus();	
		}

	});	    

	var checkout1 = $('.dateEnd-flight1').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top'
	})
	.on('changeDate', function(e){					
	});	
	//main date picker end

	var checkin2 = $('.dateStart-flight2').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top left'
	})
	.on('changeDate', function(e){	
		selStartDate = e.date;
		var nextDay = new Date(e.date);
		nextDay.setDate(nextDay.getDate() + 1);
		$('.dateEnd-flight2').datepicker('setStartDate', nextDay);
		if(checkout2.val() == '') checkout2.focus();	

		if (checkout2.datepicker('getDate') == 'Invalid Date') {
			var newDate = new Date(e.date)
			newDate.setDate(newDate.getDate() + 1);
			checkout2.datepicker('update',newDate);
			checkout2.focus();	
		}

	});	    

	var checkout2 = $('.dateEnd-flight2').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top'
	})
	.on('changeDate', function(e){					
	});	
	//main date picker end

	var checkin3 = $('.pickup-datepicker').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top left'
	})
	.on('changeDate', function(e){	
		selStartDate = e.date;
		var nextDay = new Date(e.date);
		nextDay.setDate(nextDay.getDate() + 1);
		$('.dropoff-datepicker').datepicker('setStartDate', nextDay);
		if(checkout3.val() == '') checkout3.focus();	

		if (checkout3.datepicker('getDate') == 'Invalid Date') {
			var newDate = new Date(e.date);
			newDate.setDate(newDate.getDate() + 1);
			checkout3.datepicker('update',newDate);
			checkout3.focus();	
		}

	});

	var checkout4 = $('.dropoff-datepicker').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top'
	})
	.on('changeDate', function(e){					
	});	
	//main date picker end

	var checkout5 = $('.tour-datepicker').datepicker({
		format: 'dd/mm/yyyy',
		startDate: now,
		autoclose: true,
		orientation: 'top'
	})
	.on('changeDate', function(e){					
	});	
	//main date picker end

});