function logout() {
	'use strict';
	SetCookie(Base.encode("password"), "", 0, 0);
	window.location.href = 'login.html';
}

function compsNameInquirer() {
	'use strict';
	var xmlhttp = null;
	if (window.XMLHttpRequest) {
		// Code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else {
		// Code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 || xmlhttp.status === 200) {
			if (xmlhttp.responseText === "failed") {
				document.getElementById("panner-title").style.color = "#f00";
				document.getElementById("panner-title").innerHTML = "当前没有正在进行的活动";
				document.getElementById("panner-compsname-button").innerHTML = "";
			}
			else {
				document.getElementById("panner-title").style.color = "#58595b";
				document.getElementById("panner-title").innerHTML = "请选择活动名称";
				document.getElementById("panner-compsname-button").innerHTML = xmlhttp.responseText;
			}
		}
		else {
			document.getElementById("panner-title").style.color = "#f00";
			document.getElementById("panner-title").innerHTML = "请求失败，请稍后重试";
			document.getElementById("panner-compsname-button").innerHTML = "";
		}
	};

	xmlhttp.open("GET", "PHP/bin/compsname-inquirer.php", true);
	xmlhttp.send();
}

function compslogin(compsname) {
	'use strict';
	//httpPost("scomng.html", {compsname: encodeURIComponent(Base.encode(compsname))});
	window.location.href = 'scomng.html?compsname=' + encodeURIComponent(Base.encode(compsname));
}

function httpPost(url, params) {
	'use strict';
    var temp = document.createElement("form");
    temp.action = url;
    temp.method = "post";
    temp.style.display = "none";

    for (var current in params) {
        var opt = document.createElement("textarea");
        opt.name = current;
        opt.value = params[current];
        temp.appendChild(opt);
    }

    document.body.appendChild(temp);
    temp.submit();

    return temp;
}
