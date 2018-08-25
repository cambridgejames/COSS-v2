function postmessage() {
	'use strict';
	
	var username = document.getElementById("username").value;
	var password = document.getElementById("password").value;

	var xmlhttp = null;
	if (window.XMLHttpRequest) {
		// Code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {    
		// Code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
			if (xmlhttp.responseText === "succeed") {
				var exp = new Date();
				SetCookie(Base.encode("username"), Base.encode(username), exp, 7);
				if (document.getElementById("auto-login").checked) {
					// Set cookies (7 days)
					SetCookie(Base.encode("password"), Base.encode(password), exp, 7);
				}
				window.location.href = 'skiping.html';
			} else {
				document.getElementById("tips").innerHTML = xmlhttp.responseText;
			}
		}
	};

	xmlhttp.open("GET", "files/bin/login-checkout.php?username="+username+"&password="+password, true);
	xmlhttp.send();

}

function flushtip() {
	'use strict';
	document.getElementById("tips").innerHTML = "";
}

window.onload = function() {
	'use strict';
	if (GetCookieByName(Base.encode("username"))) {
		document.getElementById("username").value = Base.decode(GetCookieByName(Base.encode("username")));
		document.getElementById("auto-login").checked = true;
		if (GetCookieByName(Base.encode("password"))) {
			document.getElementById("password").value = Base.decode(GetCookieByName(Base.encode("password")));
			postmessage();
		}
	}
};

document.onkeyup = function (e) {
	'use strict';
	if (window.event) {
		// Code for IE6, IE5
		e = window.event;
	}
	var code = e.charCode || e.keyCode;
	if (code === 13) {
		postmessage();
	}
};
