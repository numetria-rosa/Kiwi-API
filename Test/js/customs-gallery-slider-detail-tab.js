$('.gallery-slideshow').slick({
	slidesToShow: 1,
	slidesToScroll: 1,
	speed: 500,
	arrows: true,
	fade: true,
	asNavFor: '.gallery-nav'
});
$('.gallery-nav').slick({
	slidesToShow: 7,
	slidesToScroll: 1,
	speed: 500,
	asNavFor: '.gallery-slideshow',
	dots: false,
	centerMode: true,
	focusOnSelect: true,
	infinite: true,
	responsive: [
		{
		breakpoint: 1199,
		settings: {
			slidesToShow: 7,
			}
		}, 
		{
		breakpoint: 991,
		settings: {
			slidesToShow: 5,
			}
		}, 
		{
		breakpoint: 767,
		settings: {
			slidesToShow: 5,
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