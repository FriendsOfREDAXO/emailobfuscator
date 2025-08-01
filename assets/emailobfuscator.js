// Initialize when DOM is ready
if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', function() {
		decryptEmailaddresses();
		deobfuscateXorEmails();
	});
} else {
	decryptEmailaddresses();
	deobfuscateXorEmails();
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

// XOR decryption functions
function base64UrlDecode(str) {
	str = (str + "===").slice(0, str.length + (str.length % 4));
	return atob(str.replace(/-/g, "+").replace(/_/g, "/"));
}

function xorDecrypt(encrypted, key) {
	var result = "";
	for (var i = 0; i < encrypted.length; i++) {
		result += String.fromCharCode(encrypted.charCodeAt(i) ^ key.charCodeAt(i % key.length));
	}
	return result;
}

function decryptEmailData(encryptedData, method, context) {
	var key;
	if (method === "xor-simple") {
		key = "EmailObfuscatorKey2024";
	} else {
		// Generate dynamic key using a simple hash
		var baseKey = "EmailObfuscator";
		var fullString = baseKey + (context || "");
		var hash = 0;
		for (var i = 0; i < fullString.length; i++) {
			var char = fullString.charCodeAt(i);
			hash = ((hash << 5) - hash) + char;
			hash = hash & 0xFFFFFFFF; // Convert to 32-bit integer
		}
		// Convert hash to hex and take first 16 characters
		var hashHex = Math.abs(hash).toString(16);
		key = (hashHex + hashHex + hashHex + hashHex).substring(0, 16);
	}
	
	var decoded = base64UrlDecode(encryptedData);
	return xorDecrypt(decoded, key);
}

function deobfuscateXorEmails() {
	var obfuscatedElements = document.querySelectorAll(".email-obfuscated");
	
	Array.prototype.forEach.call(obfuscatedElements, function(element) {
		var method = element.getAttribute("data-method");
		var context = element.getAttribute("data-context") || "";
		var encryptedEmail = element.getAttribute("data-email");
		var encryptedText = element.getAttribute("data-text");
		var encryptedAttributes = element.getAttribute("data-attributes");
		
		try {
			var email = decryptEmailData(encryptedEmail, method, context);
			var text = decryptEmailData(encryptedText, method, context);
			var attributes = encryptedAttributes ? decryptEmailData(encryptedAttributes, method, context) : "";
			
			var link = document.createElement("a");
			link.href = "mailto:" + email;
			link.textContent = text;
			
			if (attributes) {
				// Simple attribute parsing
				var attrParts = attributes.split(" ");
				Array.prototype.forEach.call(attrParts, function(attr) {
					var eqIndex = attr.indexOf("=");
					if (eqIndex > 0) {
						var attrName = attr.substring(0, eqIndex);
						var attrValue = attr.substring(eqIndex + 1).replace(/["\']/g, "");
						if (attrName && attrValue && attrName !== "href") {
							link.setAttribute(attrName, attrValue);
						}
					}
				});
			}
			
			element.parentNode.replaceChild(link, element);
		} catch (e) {
			console.warn("Failed to deobfuscate email:", e);
		}
	});
}