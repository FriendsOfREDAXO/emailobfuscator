// Initialize when DOM is ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', decryptEmailaddresses);
} else {
	decryptEmailaddresses();
}

function decryptEmailaddresses() {
	// Ersetze E-Mailadressen
	var unicornSpans = document.querySelectorAll('span.unicorn');
	Array.prototype.forEach.call(unicornSpans, function(span) {
		span.outerHTML = '@';
	});
	
	// Ersetze mailto-Links
	var emailLinks = document.querySelectorAll('a[href^="javascript:decryptUnicorn"]');
	Array.prototype.forEach.call(emailLinks, function(link) {
		// Selektiere Einhorn-Werte
		var emails = link.href.match(/\((.*)\)/)[1];
		
		emails = emails
			// ROT13-Transformation
			.replace(/[a-z]/gi, function (s) {
				return String.fromCharCode(s.charCodeAt(0) + (s.toLowerCase() < 'n' ? 13 : -13));
			})
			// Ersetze | durch @
			.replace(/\|/g, '@');
		
		// Ersetze Einhörner
		link.href = 'mailto:' + emails;
	});
}