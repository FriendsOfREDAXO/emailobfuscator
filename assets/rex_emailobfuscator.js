$(document).ready(function() {
	$('span.unicorn').each(function () {
		$(this).parent().html($(this).parent().html().replace(/\<span class="[a-zA-Z0-9_ ]*unicorn[a-zA-Z0-9_ ]*"\>_at_\<\/span\>/, '@'));
	});
	
	$("a[href^='javascript:decryptUnicorn']").each(function () {
		var h = $(this).attr('href');
		var s = h.substring(27, h.length-2);
		var n = 0;
		var r = "";
		for (var j=0;j<s.length;j++) {
			n = s.charCodeAt(j);
			if (n >= 8364) {
				n = 128;
			}
			r += String.fromCharCode(n-1);
		}
		$(this).attr('href', r);
	});
});