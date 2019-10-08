function searchCompsByID() {
	document.getElementById("ajax-data-container").innerHTML = "";
	var compsID = document.getElementById("search").value;
	if (compsID.length < 1) { alert("输入的ID长度过短"); return; }

	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "PHP/bin/visitor-get-comps-by-id.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("compsID=" + compsID + "&randomNow=" + Math.random());

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304)) {
			if (xmlhttp.responseText != "failed") {
				document.getElementById("ajax-data-container").innerHTML = xmlhttp.responseText;
			}
		} else {
			if (xmlhttp.readyState !== 4 && (xmlhttp.status !== 200 && xmlhttp.status !== 304)) {
				alert("请求失败，请重试！");
			}
		}
	};
}

function getScores(compsname) {
	'use strict';

	var xmlhttp = null;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();	// Code for IE7+, Firefox, Chrome, Opera, Safari
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");	// Code for IE6, IE5
	}

	xmlhttp.open("POST", "PHP/bin/get-if-comps-underway.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send("compsname=" + compsname + "&randomNow=" + Math.random());

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && xmlhttp.status === 200 && xmlhttp.responseText === "succeed") {
			window.location.href = 'scovie.html?compsname=' + encodeURIComponent(Base.encode(compsname));
		}
		else if ((xmlhttp.readyState !== 4 && xmlhttp.status !== 200) || xmlhttp.responseText === "failed") {
			alert("当前竞赛尚未开始");
		}
	};
}
