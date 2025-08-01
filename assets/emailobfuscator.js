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
	// Add proper padding
	var padding = str.length % 4;
	if (padding === 2) {
		str += "==";
	} else if (padding === 3) {
		str += "=";
	} else if (padding === 1) {
		throw new Error("Invalid base64url string");
	}
	
	// Replace URL-safe characters with standard base64 characters
	str = str.replace(/-/g, "+").replace(/_/g, "/");
	
	// Use a more robust base64 decoding approach
	try {
		// First try the standard atob approach
		var decoded = atob(str);
		
		// Convert to a proper binary string by ensuring each character is a proper byte
		var result = "";
		for (var i = 0; i < decoded.length; i++) {
			var charCode = decoded.charCodeAt(i);
			// Ensure the character code is in the valid byte range (0-255)
			if (charCode < 0 || charCode > 255) {
				throw new Error("Invalid character code: " + charCode);
			}
			result += String.fromCharCode(charCode & 0xFF);
		}
		return result;
	} catch (e) {
		throw new Error("Invalid base64 characters in: " + str + " (" + e.message + ")");
	}
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
			hash = hash >>> 0; // Convert to unsigned 32-bit integer
		}
		// Convert hash to hex and take first 16 characters
		var hashHex = (hash >>> 0).toString(16);
		key = (hashHex + hashHex + hashHex + hashHex).substring(0, 16);
	}
	
	try {
		var decoded = base64UrlDecode(encryptedData);
		return xorDecrypt(decoded, key);
	} catch (e) {
		throw new Error("Failed to decrypt data '" + encryptedData + "': " + e.message);
	}
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
				// Robust attribute parsing using regex
				var attrRegex = /([^\s=]+)\s*=\s*(['"])(.*?)\2|([^\s=]+)\s*=\s*([^\s"']+)/g;
				var match;
				while ((match = attrRegex.exec(attributes)) !== null) {
					var attrName = match[1] || match[4];
					var attrValue = match[3] || match[5];
					if (attrName && attrValue && attrName !== "href") {
						link.setAttribute(attrName, attrValue);
					}
				}
			}
			
			element.parentNode.replaceChild(link, element);
		} catch (e) {
			console.warn("Failed to deobfuscate email: " + e.message + " - Element data:", {
				method: method,
				context: context,
				encryptedEmail: encryptedEmail,
				encryptedText: encryptedText,
				encryptedAttributes: encryptedAttributes
			});
		}
	});
}