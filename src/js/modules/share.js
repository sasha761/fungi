const $ = require('jquery');

if ($('.js-share').length) {
	const shareButton = $('.js-share'),
        thisUrl = window.location.href,
        thisTitle = document.title;	

	$('.js-share').click(function() {
		console.log('share');
		if (navigator.share) {
			// alert('dsfsdfsdfsdf');
		  navigator.share({
		    title: thisTitle,
		    url: thisUrl
		  })
		    .then(() => console.log('Successful share'))
		    .catch((error) => console.log('Error sharing', error));
			}

	});
}	