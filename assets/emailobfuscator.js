if (typeof jQuery !== 'undefined') {
	$(function () {
		decryptEmailaddresses();
	});
} else {
	console.warn('Email obfuscator addon requires jQuery.');
}

function decryptEmailaddresses() {
	// Ersetze E-Mailadressen
	$('span.unicorn').each(function () {
		$(this).replaceWith('@');
	});
	
	// Ersetze mailto-Links
	$('a[href^="javascript:decryptUnicorn"]').each(function () {
	
		// Selektiere Einhorn-Werte
		var emails = $(this).attr('href').match(/\((.*)\)/)[1];
		
		emails = emails
			// ROT13-Transformation
			.replace(/[a-z]/gi, function (s) {
				return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13))
			})
			// Ersetze # durch @
			.replace(/\#/g, '@');
		
		// Ersetze Einhörner
		$(this).attr('href', 'mailto:' + emails);
	});
}