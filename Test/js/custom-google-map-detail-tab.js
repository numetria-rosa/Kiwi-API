

	var map;
	var var_location = new google.maps.LatLng(48.913954, 2.263421);
		
	function map_init() {

		var var_mapoptions = {
			center: {lat:48.913954,lng:2.263421},
			zoom: 17,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			panControl: false,
			rotateControl: false,
			streetViewControl: true,
			scrollwheel: false,
		};
		
		map = new google.maps.Map(document.getElementById("map-and-friends"),var_mapoptions);
		
		var var_marker = new google.maps.Marker({
			position: var_location,
			map: map,
			icon: 'images/mapicon.png',
			maxWidth: 200,
			maxHeight: 200,
			clickable: false,
		});

		// a  div where we will place the buttons
		ctrl = $('<div/>').css({
			background: '#fff',
			border: '1px solid #000',
			padding: '4px',
			margin: '2px',
			textAlign: 'center',
			clear: 'both',
		})[0];
		
		map.controls[google.maps.ControlPosition.RIGHT_TOP].push(ctrl);
		ctrl=$(ctrl);

	var places = {
		 attraction: {
			label: 'attractions',
			//the category may be default-checked when you want to
			//uncomment the next line
			//checked:true,
			icon: 'images/map-marker/01.png',
			items: [
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28512, 103.86154],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28336, 103.85761],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28324, 103.85948],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28186, 103.86394]
			]
		},
		restaurant: {
			label: 'restaurants',
			//the category may be default-checked when you want to
			//uncomment the next line
			//checked:true,
			icon: 'images/map-marker/02.png',
			items: [
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28219, 103.85765],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28389, 103.85871],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28360, 103.85909],
				// ['Novotel Paris Sud Porte de Charenton', 48.913954, 2.263421],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28557, 103.86021],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28447, 103.86121],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28573, 103.85937]
			]
		},
		mall: {
			label: 'mall',
			//the category may be default-checked when you want to
			//uncomment the next line
			//checked:true,
			icon: 'images/map-marker/03.png',
			items: [
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28423, 103.86029],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28412, 103.86163],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28520, 103.85958],
				['<div class="map-detail-info-window"><div class="image"><img src="images/attraction/01-sq.jpg" alt="image"></div><div class="content"><h3>SkyPark at Marina Bay Sand</h3><p>300 metres far away</p><a href="#">more deatils</a></div></div>', 1.28521, 103.85861]
			]
		}
	},

	infowindow = new google.maps.InfoWindow();



	//clear all-button
	ctrl.append($('<input>', {
		type: 'button',
		value: 'clear all'
	})
		.click(function () {
		$(this).parent().find('input[type="checkbox"]')
			.prop('checked', false).trigger('change');
	}));
	ctrl.append($('<div style="clear:both"></div>'));

	//now loop over the categories
	$.each(places, function (c, category) {

		//a checkbox fo the category
		var cat = $('<input>', {
			type: 'checkbox'
		}).change(function () {
			$(this).data('goo').set('map', (this.checked) ? map : null);
		})
		//create a data-property with a google.maps.MVCObject
		//this MVC-object will do all the show/hide for the category 
		.data('goo', new google.maps.MVCObject)
			.prop('checked', !! category.checked)

		//this will initialize the map-property of the MVCObject
		.trigger('change')

		//label for the checkbox
		.appendTo($('<label>').css({
			whiteSpace: 'nowrap',
			textAlign: 'left'
		})
			.appendTo(ctrl))
			.after(category.label);
	  


		//loop over the items(markers)
		$.each(category.items, function (m, item) {
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(item[1], item[2]),
				icon: category.icon
			});

			//bind the map-property of the marker to the map-property
			//of the MVCObject that has been stored as checkbox-data 
			marker.bindTo('map', cat.data('goo'), 'map');
			google.maps.event.addListener(marker, 'click', function () {
				infowindow.setContent(item[0]);
				infowindow.open(map, this);
			});
		});


	});
	
	//show all-button
	ctrl.prepend($('<br/>'));
	$('<input>', {
		type: 'button',
		value: 'show all'
	})
		.click(function () {
			
		$(this).parent().find('input[type="checkbox"]')
			.prop('checked', true).trigger('change');
	}).prependTo(ctrl).click();
}
google.maps.event.addDomListener(window, 'load', map_init);
