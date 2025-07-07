

	(function(A) {

	if (!Array.prototype.forEach)
		A.forEach = A.forEach || function(action, that) {
			for (var i = 0, l = this.length; i < l; i++)
				if (i in this)
					action.call(that, this[i], i, this);
			};

		})(Array.prototype);

	var
	mapObject,
	markers = [],
	markersData = {
		'result-map': [
			{
				id: '1',
				location_latitude: 1.302111, 
				location_longitude: 103.836111,
				image_url: 'images/singapore-hotel/01.jpg',
				star_point: '<div class="star-rate rated-40 mb-10"></div>',
				name_point: 'Hotel Metropolitan Tokyo',
				location_point: 'Tokyo, Japan',
				tripadvisor_rating_point: 'Good',
				review_count_point: '34',
				url_point: '#',
				tripadvisor_url_point: '#',
			},
			{
				id: '2',
				location_latitude: 1.290882, 
				location_longitude: 103.844443,
				image_url: 'images/singapore-hotel/02.jpg',
				star_point: '<div class="star-rate rated-40 mb-10"></div>',
				name_point: 'Holiday Inn Express Singapore Clarke Quay',
				location_point: 'Magazine Road, Singapore',
				tripadvisor_rating_point: 'Exellent',
				review_count_point: '34',
				url_point: '#',
				tripadvisor_url_point: '#',
			},
			{
				id: '3',
				location_latitude: 1.301545, 
				location_longitude: 103.838328,
				image_url: 'images/singapore-hotel/03.jpg',
				star_point: '<div class="star-rate rated-40 mb-10"></div>',
				name_point: 'Holiday Inn Express Singapore Orchard Road',
				location_point: 'Bideford Road, Singapore',
				tripadvisor_rating_point: 'Very Well',
				review_count_point: '34',
				url_point: '#',
				tripadvisor_url_point: '#',
			},
			{
				id: '4',
				location_latitude: 1.304842, 
				location_longitude: 103.831824,
				image_url: 'images/singapore-hotel/04.jpg',
				star_point: '<div class="star-rate rated-40 mb-10"></div>',
				name_point: 'Holiday Inn Singapore Orchard City Centre',
				location_point: 'Cavenagh Rd, Singapore',
				tripadvisor_rating_point: 'Good',
				review_count_point: '34',
				url_point: '#',
				tripadvisor_url_point: '#',
			},
			{
				id: '5',
				location_latitude: 1.286035, 
				location_longitude: 103.853489,
				image_url: 'images/singapore-hotel/05.jpg',
				star_point: '<div class="star-rate rated-40 mb-10"></div>',
				name_point: 'The Fullerton Hotel Singapore',
				location_point: 'Fullerton Square, Singapore',
				tripadvisor_rating_point: 'Very Well',
				review_count_point: '34',
				url_point: '#',
				tripadvisor_url_point: '#',
			},
			{
				id: '6',
				location_latitude: 1.307422, 
				location_longitude: 103.832655,
				image_url: 'images/singapore-hotel/06.jpg',
				star_point: '<div class="star-rate rated-40 mb-10"></div>',
				name_point: 'Royal Plaza On Scotts',
				location_point: 'Scotts Rd, Singapore',
				tripadvisor_rating_point: 'Good',
				review_count_point: '34',
				url_point: '#',
				tripadvisor_url_point: '#',
			}
		]

	};


		var mapOptions = {
			zoom: 13,
			center: new google.maps.LatLng(1.292489,103.847602),
			mapTypeId: google.maps.MapTypeId.ROADMAP,

			mapTypeControl: false,
			mapTypeControlOptions: {
				style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
				position: google.maps.ControlPosition.LEFT_CENTER
			},
			panControl: false,
			panControlOptions: {
				position: google.maps.ControlPosition.TOP_RIGHT
			},
			zoomControl: true,
			zoomControlOptions: {
				style: google.maps.ZoomControlStyle.LARGE,
				position: google.maps.ControlPosition.TOP_RIGHT
			},
			scrollwheel: false,
			scaleControl: false,
			scaleControlOptions: {
				position: google.maps.ControlPosition.TOP_LEFT
			},
			streetViewControl: true,
			streetViewControlOptions: {
				position: google.maps.ControlPosition.LEFT_TOP
			},
			styles: [/*map styles*/]
		};
		var marker;
		
		mapObject = new google.maps.Map(document.getElementById('bali-map'), mapOptions);
		
		for (var key in markersData)
			markersData[key].forEach(function (item) {
				marker = new google.maps.Marker({
					position: new google.maps.LatLng(item.location_latitude, item.location_longitude),
					map: mapObject,
					icon: 'images/map-marker/' + key + '.png',
				});

				if ('undefined' === typeof markers[key])
					markers[key] = [];
				markers[key].push(marker);
				google.maps.event.addListener(marker, 'click', (function () {
  closeInfoBox();
  getInfoBox(item).open(mapObject, this);
  mapObject.setCenter(new google.maps.LatLng(item.location_latitude, item.location_longitude));
 }));

});

	function hideAllMarkers () {
		for (var key in markers)
			markers[key].forEach(function (marker) {
				marker.setMap(null);
			});
	};

	function closeInfoBox() {
		$('div.infoBox').remove();
	};

	function getInfoBox(item) {
		return new InfoBox({
			content:
			'<div class="infobox-hotel-item hotel-item-grid">' +
			'<a href="' + item.url_point + '">' +
			'<div class="image">' + '<img src="' + item.image_url + '" alt="Image"/>' + '</div>' +
			'<div class="heading">' + item.star_point + 
			'<h4>' + item.name_point + '</h4>' +
			'<p class="font400"> <i class="fa fa-map-marker text-primary"></i> ' + item.location_point + '</p>' +
			'</div>' +
			'<div class="content"> <div class="row gap-5"> ' +
			'<div class="col-xs-6 col-sm-6">' +
			'<div class="tripadvisor-module">'+
			'<div class="texting font700 line14" style="font-size: 15px; margin-bottom: 3px;">'+ item.tripadvisor_rating_point +
			'</div>'+
			'<div class="hover-underline">'+ item.review_count_point +
			' reviews </div>'+
			'</div>' +
			'</div>' +
			'<div class="col-xs-6 col-sm-6">' +
			'<p class="price"><span class="block">start from</span><span class="number">$187</span> / night</p>' +
			'</div>' +
			'</div></div>' +
			'</a>' +
			'</div>',
			disableAutoPan: true,
			maxWidth: 0,
			pixelOffset: new google.maps.Size(-130, -385),
			closeBoxMargin: '5px -20px 2px 2px',
			closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
			isHidden: false,
			pane: 'floatPane',
			enableEventPropagation: true
		});


	};
