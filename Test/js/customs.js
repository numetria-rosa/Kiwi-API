
(function($){



	"use strict";
	
	
	
	/**
	 * Page Preloader
	 */
	$("#introLoader").introLoader({
			animation: {
					name: 'counterLoader',
					options: {
							ease: "easeOutSine",
							style: 'style-01',
							animationTime: 1000
					}
			}
	});

	

	/**
	 * Sticky Header
	 */
	$(".container-wrapper").waypoint(function() {
		$(".navbar.navbar-fixed-top").toggleClass("navbar-sticky");
		return false;
	}, { offset: "-20px" });
	

	
	/**
	 * Main Menu Slide Down Effect
	 */
	// Mouse-enter dropdown
	$('#navbar li').on("mouseenter", function() {
			$(this).find('ul').first().stop(true, true).delay(350).slideDown(500, 'easeInOutQuad');
	});
	// Mouse-leave dropdown
	$('#navbar li').on("mouseleave", function() {
			$(this).find('ul').first().stop(true, true).delay(100).slideUp(150, 'easeInOutQuad');
	});
	
	
	
	/**
	 * Navbar Vertical - For example: use for dashboard menu
	 */
	if (screen && screen.width > 767 ) {
		// Mouse-enter dropdown
		$('.navbar-vertical li').on("mouseenter", function() {
		$(this).find('ul').first().stop(true, true).delay(450).slideDown(500, 'easeInOutQuad');
		});

		// Mouse-leave dropdown
		$('.navbar-vertical li').on("mouseleave", function() {
				$(this).find('ul').first().stop(true, true).delay(0).slideUp(150, 'easeInOutQuad');
		});
	}
	
	/**
	 * Slicknav - a Mobile Menu
	 */
	var $slicknav_label;
	$('#responsive-menu').slicknav({
		duration: 500,
		easingOpen: 'easeInExpo',
		easingClose: 'easeOutExpo',
		closedSymbol: '<i class="fa fa-plus"></i>',
		openedSymbol: '<i class="fa fa-minus"></i>',
		prependTo: '#slicknav-mobile',
		allowParentLinks: true,
		label:"" 
	});
	
	/**
	 * Slicknav - a Dashboard Mobile Menu
	 */
	var $slicknav_label;
	$('#responsive-menu-dashboard').slicknav({
		duration: 500,
		easingOpen: 'easeInExpo',
		easingClose: 'easeOutExpo',
		closedSymbol: '<i class="fa fa-plus"></i>',
		openedSymbol: '<i class="fa fa-minus"></i>',
		prependTo: '#slicknav-mobile-dashboard',
		allowParentLinks: true,
		label:"Dashboard Menu" 
	});
	
	
	
	/**
	 * Smooth scroll to anchor
	 */
	$(function() {
		$('a.anchor[href*=#]:not([href=#])').on("click",function() {
			if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
				if (target.length) {
					$('html,body').animate({
						scrollTop: (target.offset().top - 70) // 70px offset for navbar menu
					}, 1000);
					return false;
				}
			}
		});
	});
	
	
	
	/**
	 * Effect Bootstrap Dropdown
	 */
	$('.bt-dropdown-click').on('show.bs.dropdown', function(e) {   
		$(this).find('.dropdown-menu').first().stop(true, true).slideToggle(350); 
	}); 
	$('.bt-dropdown-click').on('hide.bs.dropdown', function(e) { 
		$(this).find('.dropdown-menu').first().stop(true, true).slideToggle(350); 
	});
	
	$(document).on('click', '.dropdown-menu-form', function (e){
		e.stopPropagation();
	});
	 
	$(document).on('click', '.dropdown-menu-form button', function (e){
		closeDropDownForm(e);
	});
	 

	
	/**
	 * Another Bootstrap Toggle
	 */
	$('.another-toggle').each(function(){
		if( $('h4',this).hasClass('active') ){
			$(this).find('.another-toggle-content').show();
		}
	});
	$('.another-toggle h4').on("click",function() {
		if( $(this).hasClass('active') ){
			$(this).removeClass('active');
			$(this).next('.another-toggle-content').slideUp();
		} else {
			$(this).addClass('active');
			$(this).next('.another-toggle-content').slideDown();
		}
	});
	

	
	/**
	 *  Arrow for Menu has sub-menu
	 */
	if ($(window).width() > 992) {
		$(".navbar-arrow ul ul > li").has("ul").children("a").append("<i class='arrow-indicator fa fa-angle-right'></i>");
	}

	
	
	/**
	 * Back To Top
	 */
	$(window).scroll(function(){
		if($(window).scrollTop() > 500){
			$("#back-to-top").fadeIn(200);
		} else{
			$("#back-to-top").fadeOut(200);
		}
	});
	$('#back-to-top, .back-to-top').on("click",function() {
			$('html, body').animate({ scrollTop:0 }, '800');
			return false;
	});
	
	
	/**
	 * Equal Content and Sidebar by Js
	 */
	var widthForEqualContentSidebar = $(window).width();
	if ((widthForEqualContentSidebar > 768)) {
		
		// placing objects inside variables
		var content = $('.equal-content-sidebar-by-js .content-wrapper');
		var sidebar = $('.equal-content-sidebar-by-js .sidebar-wrapper');

		// get content and sidebar height in variables
		var getContentHeight = content.outerHeight();
		var getSidebarHeight = sidebar.outerHeight();

		// check if content height is bigger than sidebar
		if ( getContentHeight > getSidebarHeight ) {
			sidebar.css('min-height', getContentHeight);
		}

		// check if sidebar height is bigger than content
		if ( getSidebarHeight > getContentHeight ) {
			content.css('min-height', getSidebarHeight);
		}
	}
	
	

	/**
	 * Bootstrap Tooltip
	 */
	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	})
	
	
	
	/**
	 * Fancy - Custom Select
	 */
	$('.custom-select').fancySelect(); // Custom select
	
	
	
	/**
	 * Placeholder
	 */
	$("input, textarea").placeholder();

	
	
	/**
	 * Input Spinner
	 */
	$(".form-spin").TouchSpin({
		buttondown_class: 'btn btn-spinner-down',
		buttonup_class: 'btn btn-spinner-up',
		buttondown_txt: '<i class="ion-minus"></i>',
		buttonup_txt: '<i class="ion-plus"></i>'
	});
	
	
	
	/**
	 * Typeahead - autocomplete form
	 */
	var data = {
	countries: ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda",
			"Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh",
			"Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia",
			"Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burma",
			"Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Central African Republic", "Chad",
			"Chile", "China", "Colombia", "Comoros", "Congo, Democratic Republic", "Congo, Republic of the",
			"Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti",
			"Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador",
			"Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "Gabon",
			"Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guatemala", "Guinea",
			"Guinea-Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India",
			"Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan",
			"Kazakhstan", "Kenya", "Kiribati", "Korea, North", "Korea, South", "Kuwait", "Kyrgyzstan", "Laos",
			"Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg",
			"Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands",
			"Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Mongolia", "Morocco", "Monaco",
			"Mozambique", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger",
			"Nigeria", "Norway", "Oman", "Pakistan", "Panama", "Papua New Guinea", "Paraguay", "Peru",
			"Philippines", "Poland", "Portugal", "Romania", "Russia", "Rwanda", "Samoa", "San Marino",
			"Sao Tome", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone",
			"Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "Spain",
			"Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan",
			"Tajikistan", "Tanzania", "Thailand", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey",
			"Turkmenistan", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States",
			"Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"],
	capitals: ["Abu Dhabi", "Abuja", "Accra", "Adamstown", "Addis Ababa", "Algiers", "Alofi", "Amman", "Amsterdam",
			"Andorra la Vella", "Ankara", "Antananarivo", "Apia", "Ashgabat", "Asmara", "Astana", "Asunción", "Athens",
			"Avarua", "Baghdad", "Baku", "Bamako", "Bandar Seri Begawan", "Bangkok", "Bangui", "Banjul", "Basseterre",
			"Beijing", "Beirut", "Belgrade", "Belmopan", "Berlin", "Bern", "Bishkek", "Bissau", "Bogotá", "Brasília",
			"Bratislava", "Brazzaville", "Bridgetown", "Brussels", "Bucharest", "Budapest", "Buenos Aires", "Bujumbura",
			"Cairo", "Canberra", "Caracas", "Castries", "Cayenne", "Charlotte Amalie", "Chisinau", "Cockburn Town",
			"Conakry", "Copenhagen", "Dakar", "Damascus", "Dhaka", "Dili", "Djibouti", "Dodoma", "Doha", "Douglas",
			"Dublin", "Dushanbe", "Edinburgh of the Seven Seas", "El Aaiún", "Episkopi Cantonment", "Flying Fish Cove",
			"Freetown", "Funafuti", "Gaborone", "George Town", "Georgetown", "Georgetown", "Gibraltar", "King Edward Point",
			"Guatemala City", "Gustavia", "Hagåtña", "Hamilton", "Hanga Roa", "Hanoi", "Harare", "Hargeisa", "Havana",
			"Helsinki", "Honiara", "Islamabad", "Jakarta", "Jamestown", "Jerusalem", "Juba", "Kabul", "Kampala",
			"Kathmandu", "Khartoum", "Kiev", "Kigali", "Kingston", "Kingston", "Kingstown", "Kinshasa", "Kuala Lumpur",
			"Kuwait City", "Libreville", "Lilongwe", "Lima", "Lisbon", "Ljubljana", "Lomé", "London", "Luanda", "Lusaka",
			"Luxembourg", "Madrid", "Majuro", "Malabo", "Malé", "Managua", "Manama", "Manila", "Maputo", "Marigot",
			"Maseru", "Mata-Utu", "Mbabane Lobamba", "Melekeok Ngerulmud", "Mexico City", "Minsk", "Mogadishu", "Monaco",
			"Monrovia", "Montevideo", "Moroni", "Moscow", "Muscat", "Nairobi", "Nassau", "Naypyidaw", "N'Djamena",
			"New Delhi", "Niamey", "Nicosia", "Nicosia", "Nouakchott", "Nouméa", "Nukuʻalofa", "Nuuk", "Oranjestad",
			"Oslo", "Ottawa", "Ouagadougou", "Pago Pago", "Palikir", "Panama City", "Papeete", "Paramaribo", "Paris",
			"Philipsburg", "Phnom Penh", "Plymouth Brades Estate", "Podgorica Cetinje", "Port Louis", "Port Moresby",
			"Port Vila", "Port-au-Prince", "Port of Spain", "Porto-Novo Cotonou", "Prague", "Praia", "Cape Town",
			"Pristina", "Pyongyang", "Quito", "Rabat", "Reykjavík", "Riga", "Riyadh", "Road Town", "Rome", "Roseau",
			"Saipan", "San José", "San Juan", "San Marino", "San Salvador", "Sana'a", "Santiago", "Santo Domingo",
			"São Tomé", "Sarajevo", "Seoul", "Singapore", "Skopje", "Sofia", "Sri Jayawardenepura Kotte", "St. George's",
			"St. Helier", "St. John's", "St. Peter Port", "St. Pierre", "Stanley", "Stepanakert", "Stockholm", "Sucre",
			"Sukhumi", "Suva", "Taipei", "Tallinn", "Tarawa Atoll", "Tashkent", "Tbilisi", "Tegucigalpa", "Tehran",
			"Thimphu", "Tirana", "Tiraspol", "Tokyo", "Tórshavn", "Tripoli", "Tskhinvali", "Tunis", "Ulan Bator", "Vaduz",
			"Valletta", "The Valley", "Vatican City", "Victoria", "Vienna", "Vientiane", "Vilnius", "Warsaw",
			"Washington, D.C.", "Wellington", "West Island", "Willemstad", "Windhoek", "Yamoussoukro", "Yaoundé", "Yaren",
			"Yerevan", "Zagreb"]
	};

	$('#destination-search').typeahead({
			minLength: 2,
			order: "asc",
			group: true,
			maxItemPerGroup: 3,
			groupOrder: function () {

					var scope = this,
							sortGroup = [];

					for (var i in this.result) {
							sortGroup.push({
									group: i,
									length: this.result[i].length
							});
					}

					sortGroup.sort(
							scope.helper.sort(
									["length"],
									false, // false = desc, the most results on top
									function (a) {
											return a.toString().toUpperCase()
									}
							)
					);

					return $.map(sortGroup, function (val, i) {
							return val.group
					});
			},
			hint: true,
			emptyTemplate: 'No result for "{{query}}"',
			source: {
					country: {
							data: data.countries
					},
					capital: {
							data: data.capitals
					}
			},
			debug: true
	});

	
	
	/**
	 * Star rating
	 */
	$('.tripadvisor-by-attr').raty({
		path: '/images/raty',
		starHalf: 'tripadvisor-half.png',
		starOff: 'tripadvisor-off.png',
		starOn: 'tripadvisor-on.png',
		readOnly: true,
		round : { down: .2, full: .6, up: .8 },
		half: true,
		space: false,
		score: function() {
			return $(this).attr('data-rating-score');
		}
	});
	
	$('.star-rating-12px').raty({
		path: '/images/raty',
		starHalf: 'star-half-sm.png',
		starOff: 'star-off-sm.png',
		starOn: 'star-on-sm.png',
		readOnly: true,
		round : { down: .2, full: .6, up: .8 },
		half: true,
		space: false,
		score: function() {
			return $(this).attr('data-rating-score');
		}
	});
	
	

	/**
	 * Slider and Carousel by slick.js
	 */
	$('.slick-banner-slider').slick({
			dots: true,
			infinite: true,
			speed: 500,
			slidesToShow: 1,
			slidesToScroll: 1,
			centerMode: true,
			infinite: true,
			centerPadding: '0',
			focusOnSelect: true,
			adaptiveHeight: true,
			responsive: [ 
				{
					breakpoint: 767,
					settings: {
						dots: false,
					}
				}
			]
    });
		$('.slick-hero-slider').slick({
			dots: true,
			infinite: true,
			speed: 500,
			slidesToShow: 1,
			slidesToScroll: 1,
			centerMode: true,
			infinite: true,
			centerPadding: '0',
			focusOnSelect: true,
			adaptiveHeight: true,
			autoplay: true,
			autoplaySpeed: 4500,
			pauseOnHover: true,
    });
	$('.slick-testimonial').slick({
			slidesToShow: 1,
			slidesToScroll: 1,
			speed: 500,
			arrows: false,
			fade: false,
			asNavFor: '.slick-testimonial-nav',
			adaptiveHeight: true,
	});
	$('.slick-testimonial-nav').slick({
			slidesToShow: 3,
			slidesToScroll: 1,
			speed: 500,
			centerPadding: '0',
			asNavFor: '.slick-testimonial',
			dots: false,
			centerMode: true,
			focusOnSelect: true,
			infinite: true,
			responsive: [
				{
				breakpoint: 1199,
				settings: {
					slidesToShow: 3,
					}
				}, 
				{
				breakpoint: 991,
				settings: {
					slidesToShow: 3,
					}
				}, 
				{
				breakpoint: 767,
				settings: {
					slidesToShow: 3,
					}
				}, 
				{
				breakpoint: 480,
				settings: {
					slidesToShow: 3,
					}
				}
			]
	});
	

	
	/**
	 * ion Range Slider for price and star rating range slider
	 */
	$("#price_range").ionRangeSlider({
		grid: true, 
		type: "double", 
		min: 0, 
		max: 4000,
		from: 436, 
		to: 1443, 
		prefix: "TND",
	});
	$("#star_rating_range").ionRangeSlider({
		type: "double",
		min: 1,
		max: 5,
		step: 1,
		grid: true,
		grid_snap: true,
	});

	$("#transfers_range").ionRangeSlider({
		grid: true, 
		type: "double", 
		min: 0, 
		max: 1000,
		from: 290, 
		to: 580, 
		prefix: "TND",
	});

	$("#tours_range").ionRangeSlider({
		grid: true, 
		type: "double", 
		min: 0, 
		max: 1500,
		from: 455, 
		to: 1084, 
		prefix: "TND",
	});

	
	
	/**
	 * Show more-less content
	 */
	$('.btn-more-less').on("click",function() {
		$(this).text(function(i,old){
			return old=='Plus' ?  'Moins' : 'Plus';
		});
	});
	
	
	
	/**
	 * Sidebar sticky by Stickit 
	 */
	if ($(window).width() > 991) {
		 $('#sticky-sidebar').stickit({
			top: 95,
			bottom: 200,
		});
	}
	
	

	/**
	 * Responsive Tab by tabCollapse
	 */
	$('#detailTab, #responsiveTab').tabCollapse({
		tabsClass: 'hidden-sm hidden-xs',
		accordionClass: 'visible-sm visible-xs'
	});
	
	$('#detailTab, #responsiveTab').on('shown.bs.tab', function () {
		google.maps.event.trigger(map, "resize");
		map.setCenter(var_location);
	});
	$('#detailTab, #responsiveTab').on('shown-accordion.bs.tabcollapse', function(){
		google.maps.event.trigger(map, "resize");
		map.setCenter(var_location);
	});
	$('#detailTab, #responsiveTab').on('shown-tabs.bs.tabcollapse', function(){
		google.maps.event.trigger(map, "resize");
		map.setCenter(var_location);
	});
	
	
	
	/**
	 * Sign-in Modal
	 */
	var $formLogin = $('#login-form');
	var $formLost = $('#lost-form');
	var $formRegister = $('#register-form');
	var $divForms = $('#modal-login-form-wrapper');
	var $modalAnimateTime = 300;
	
	$('#login_register_btn').on("click", function () { modalAnimate($formLogin, $formRegister) });
	$('#register_login_btn').on("click", function () { modalAnimate($formRegister, $formLogin); });
	$('#login_lost_btn').on("click", function () { modalAnimate($formLogin, $formLost); });
	$('#lost_login_btn').on("click", function () { modalAnimate($formLost, $formLogin); });
	$('#lost_register_btn').on("click", function () { modalAnimate($formLost, $formRegister); });
	
	function modalAnimate ($oldForm, $newForm) {
		var $oldH = $oldForm.height();
		var $newH = $newForm.height();
		$divForms.css("height",$oldH);
		$oldForm.fadeToggle($modalAnimateTime, function(){
			$divForms.animate({height: $newH}, $modalAnimateTime, function(){
				$newForm.fadeToggle($modalAnimateTime);
			});
		});
	}
	
	
	
	/**
	 * Payment Method
	 */

	// $("div.payment-option-form").hide();
	
	$("input[name$='payments']").click(function() {
			var test = $(this).val();
			$("div.payment-option-form").hide();
			$("#" + test).show();
	});

	$("ul.options").live("click", function() {
		var selUl = $(this).parent().parent().find( '.form-control.selected' );
		var selVal = selUl.html();
		var selAttr = $(this).parent().parent().find( '.options' ).attr('id');
		numb = selAttr.substr(-1);
		if($.isNumeric(numb)){
			clss = attr.substring(0, attr.length - 1);
	    }else{
	      	clss = attr;
	    }


		if($('.form-search-main-01 form .'+selAttr+'').length == 0){
			$('.form-search-main-01 form').append('<input type="hidden" class="'+selAttr+'" name="'+selAttr+'" value="'+selVal+'">');
		}else{
			$('.form-search-main-01 form .'+selAttr+'').val(selVal);
		}

		if(selAttr == 'change-search-room'){
			var nbActiveRoom = $('.RoomCount').length;
			if(nbActiveRoom != selVal && selVal > 0){
				$('.RoomStart').empty();
				for (var i = 0; i < selVal; i++) {
					var j = i + 1;
					var defaultRoom = '<div class="col-xss-12 col-xs-12 col-sm-12 RoomCount"><div class="row gap-20"><div class="col-md-3"><div class="form-group form-spin-group"> <label for="change-search-adult'+i+'">Adultes</label> <select class="custom-select change-search-adult" id="change-search-adult'+i+'"name="change-search-adult[]"><option value="0">Adultes</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div></div><div class="col-md-3"><div class="form-group form-spin-group"> <label for="change-search-child'+i+'">Enfants</label> <select class="custom-select change-search-child" id="change-search-child'+i+'"name="change-search-child[]"><option value="0">Enfants</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></div></div><div id="content-search-age'+i+'" class="col-xss-12 col-md-6 col-sm-8"></div></div></div><div class="clear"></div>';
					$( ".RoomStart" ).append( defaultRoom );
					$( ".RoomStart select" ).fancySelect();
				}
			}
		}
	});

	$("ul.options.change-search-child").live("click", function() {
		var attri 	= $(this).attr('id');
		var nbChild = $('#'+attri).val();
		var nbRoom 	= attri.substr(-1);
		if($.isNumeric(nbRoom)){
			var targetDiv = 'content-search-age';
			$('#'+targetDiv+''+nbRoom+'').empty();
			for (var c = 0; c < nbChild; c++) {
				var defaultChild = '<div class="col-xss-12 col-xs-6 col-sm-3"><div class="form-group form-spin-group"><label for="change-search-age'+nbRoom+c+'">Ages</label><select class="custom-select change-search-age" id="change-search-age'+nbRoom+c+'" name="change-search-age'+nbRoom+'[]"><option value="0">Enfants</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option></select></div></div>';
				$('#'+targetDiv+''+nbRoom+'').append( defaultChild );
				$('#change-search-age'+nbRoom+c+'').fancySelect();
			}
		}else{
			var targetDiv = 'content-search-age';
			var nbRoom = '';
			$('#'+targetDiv+''+nbRoom+'').empty();
			for (var c = 0; c < nbChild; c++) {
				var defaultChild = '<div class="col-xss-12 col-xs-6 col-sm-3"><div class="form-group form-spin-group"><label for="change-search-age'+nbRoom+c+'">Ages</label><select class="custom-select change-search-age" id="change-search-age'+nbRoom+c+'" name="change-search-age'+nbRoom+'[]"><option value="0">Enfants</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option></select></div></div>';
				$('#'+targetDiv+''+nbRoom+'').append( defaultChild );
				$('#change-search-age'+nbRoom+c+'').fancySelect();
			}
		}
	});

	$(".detailFlight").hide();
	$(".detailTransfers").hide();

	$(".btn-detail").live("click", function() {
		var idacc = $(this).attr('idacc');
		$("#"+idacc).toggle('slow');
	});

	$( ".form-control.required" ).live("blur", function() {
		if($( this ).val() == ''){
			$( this ).addClass( "error" );
		}else{
			$( this ).removeClass( "error" );
		}
	});

	$('.date').mask('00/00/0000');
	$('.day').mask('00');
	$('.year').mask('0000');
	$('.card').mask('0000 0000 0000 0000');
	$('.ccv').mask('000');
	$('.phonemask').mask('(+000) 00 000 000');
	$('.mixed').mask('AAA000S0S');

	$(".confirmFlight").live("click", function() {
		$( ".form-control.required" ).each(function() {
			if($( this ).val() == ''){
				$( this ).addClass( "error" );
				$(this).focus();
				return false;
			}
		});
	});


	$(".flightSearchType").live("click", function() {
		var flightType = $(this).val();
		if(flightType == 'flightReturn'){
			if($('.flightEndDate').length==0){
				$('<div class="col-md-6 flightEndDate"><div class="form-group form-icon-right"><label>Date Retour</label><div class="form-icon-left"><input type="text" name="dateEnd-flight[]" class="dateEnd-flight form-control form-readonly-control" placeholder="dd/mm/yyyy"></div><i class="fa fa-calendar"></i></div></div>').insertAfter('.flightStartDate');
			}
			var nbDef = $('.flightSegment').length;
			if(nbDef > 1){
				for (var i = 1; i < nbDef; i++) {
					$('.flightSegment:last').remove();
				}
			}
			if($('.flightAddSegment').length > 0){
				$('.flightAddSegment').remove();
			}
		}
		if(flightType == 'flightSimple'){
			$('.flightEndDate').remove();
			var nbDef = $('.flightSegment').length;
			if(nbDef > 1){
				for (var i = 1; i < nbDef; i++) {
					$('.flightSegment:last').remove();
				}
			}
			if($('.flightAddSegment').length > 0){
				$('.flightAddSegment').remove();
			}
		}
		if(flightType == 'flightMulti'){
			if($('.flightEndDate').length==0){
				$('<div class="col-md-6 flightEndDate"><div class="form-group form-icon-right"><label>Date Retour</label><div class="form-icon-left"><input type="text" name="dateEnd-flight[]" class="dateEnd-flight form-control form-readonly-control" placeholder="dd/mm/yyyy"></div><i class="fa fa-calendar"></i></div></div>').insertAfter('.flightStartDate');
			}
			if($('.flightAddSegment').length==0){
				$('.flightSegment:last').append('<div class="clear"></div><div class="col-md-6 mb-15 flightAddSegment"><a href="#" class=" btn btn-link btn-block">Ajouter Segment</a></div><div class="clear"></div>');
			}
		}
	});

	$(".flightAddSegment").live("click", function() {
		var nbDef = $('.flightSegment').length;
		if(nbDef == 1){
			$('.flightAddSegment').remove();
			$(".flightSegment:last").clone().insertAfter(".flightSegment:last");

			$('.flightSegment:last .dateStart-flight').addClass('dateStart-flight1');
			$('.flightSegment:last .dateStart-flight1').removeClass('dateStart-flight');

			$('.flightSegment:last .dateEnd-flight').addClass('dateEnd-flight1');
			$('.flightSegment:last .dateEnd-flight1').removeClass('dateEnd-flight');

			$('.flightSegment:last').append('<div class="clear"></div><div class="col-md-6 mb-15 flightAddSegment"><a href="#" class=" btn btn-link btn-block">Ajouter Segment</a></div>');
			$('.flightSegment:last').append('<div class="col-md-6 mb-15 flightRemoveSegment"><a href="#" class=" btn btn-link btn-block">Enlever Segment</a></div><div class="clear"></div>');
			$('body').append('<script type=\'text/javascript\'>var nowTemp = new Date();var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);var checkin1=$(\'.dateStart-flight1\').datepicker({format:\'dd/mm/yyyy\',startDate:now,autoclose:!0,orientation:\'top left\'}).on(\'changeDate\',function(e){selStartDate=e.date;var nextDay=new Date(e.date);nextDay.setDate(nextDay.getDate()+1);$(\'.dateEnd-flight1\').datepicker(\'setStartDate\',nextDay);if(checkout1.val()==\'\')checkout1.focus();if(checkout1.datepicker(\'getDate\')==\'Invalid Date\'){var newDate=new Date(e.date);newDate.setDate(newDate.getDate()+1);checkout1.datepicker(\'update\',newDate);checkout1.focus()}});var checkout1=$(\'.dateEnd-flight1\').datepicker({format:\'dd/mm/yyyy\',startDate:now,autoclose:!0,orientation:\'top\'}).on(\'changeDate\',function(e){});</script>');
		}
		if(nbDef == 2){
			$('.flightAddSegment').remove();
			$('.flightRemoveSegment').remove();
			$(".flightSegment:last").clone().insertAfter(".flightSegment:last");

			$('.flightSegment:last .dateStart-flight1').addClass('dateStart-flight2');
			$('.flightSegment:last .dateStart-flight2').removeClass('dateStart-flight1');

			$('.flightSegment:last .dateEnd-flight1').addClass('dateEnd-flight2');
			$('.flightSegment:last .dateEnd-flight2').removeClass('dateEnd-flight1');

			$('.flightSegment:last').append('<div class="col-md-6 mb-15 flightRemoveSegment"><a href="#" class=" btn btn-link btn-block">Enlever Segment</a></div><div class="clear"></div>');
			$('body').append('<script type=\'text/javascript\'>var nowTemp = new Date();var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);var checkin2=$(\'.dateStart-flight2\').datepicker({format:\'dd/mm/yyyy\',startDate:now,autoclose:!0,orientation:\'top left\'}).on(\'changeDate\',function(e){selStartDate=e.date;var nextDay=new Date(e.date);nextDay.setDate(nextDay.getDate()+1);$(\'.dateEnd-flight2\').datepicker(\'setStartDate\',nextDay);if(checkout2.val()==\'\')checkout2.focus();if(checkout2.datepicker(\'getDate\')==\'Invalid Date\'){var newDate=new Date(e.date);newDate.setDate(newDate.getDate()+1);checkout2.datepicker(\'update\',newDate);checkout2.focus()}});var checkout2=$(\'.dateEnd-flight2\').datepicker({format:\'dd/mm/yyyy\',startDate:now,autoclose:!0,orientation:\'top\'}).on(\'changeDate\',function(e){});</script>');
		}
	});

	$(".flightRemoveSegment").live("click", function() {
		var nbDef = $('.flightSegment').length;
		if(nbDef == 2){
			$('.flightSegment:last').remove();
			$('.flightRemoveSegment').remove();
			$('.flightSegment:last').append('<div class="clear"></div><div class="col-md-6 mb-15 flightAddSegment"><a href="#" class=" btn btn-link btn-block">Ajouter Segment</a></div>');
		}
		if(nbDef == 3){
			$('.flightSegment:last').remove();
			$('.flightRemoveSegment').remove();
			$('.flightSegment:last').append('<div class="clear"></div><div class="col-md-6 mb-15 flightAddSegment"><a href="#" class=" btn btn-link btn-block">Ajouter Segment</a></div>');
			$('.flightSegment:last').append('<div class="col-md-6 mb-15 flightRemoveSegment"><a href="#" class=" btn btn-link btn-block">Enlever Segment</a></div><div class="clear"></div>');
		}
	});

	$(".searchButton").live("click", function() {
		var target 	= $(this).attr('frm');
		var form 	= $('#'+target+" form");
		var action 	= form.attr('action');
		window.location.href = action;
	});

})(jQuery);


