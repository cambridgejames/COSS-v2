function showWellcomPage() {
	'use strict';

	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "PHP/bin/admins-compsname-inquirer.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send();

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304) && xmlhttp.responseText != "failed") {
			document.getElementById("home-container").innerHTML = xmlhttp.responseText;
			var compslist = document.getElementById("home-container").children;
			if (compslist.length > 0) { compslist[0].click(); }
		}
		else {
			if (xmlhttp.readyState !== 4 && (xmlhttp.status !== 200 && xmlhttp.status !== 304) || xmlhttp.responseText == "failed") {
				alert("请求失败，请重试！")
			}
		}
	};
}

function getPageByCompsname(compsname) {
	'use strict';

	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "files/ordinary-admins-management-page.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("compsname=" + compsname);

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304) && xmlhttp.responseText != "failed") {
			document.getElementById("public-content").innerHTML = xmlhttp.responseText;
			setIdforItems();
			document.getElementById("1").click();
		}
		else {
			if (xmlhttp.readyState !== 4 && (xmlhttp.status !== 200 && xmlhttp.status !== 304) || xmlhttp.responseText == "failed") {
				document.getElementById("public-content").innerHTML = "lalala";
			}
		}
	};
}

function setIdforItems() {
	var groups = document.getElementById("groups").children;
	for (var i = 0; i < groups.length; i++) { groups[i].id = i + 1; }
}

function clickGroups(elementId) {
	var groups = document.getElementById("groups").children;
	var container =  document.getElementById("score-container").children;

	for (var i = 0; i < groups.length; i++) {	// 清空样式
		groups[i].style.background = "#B6CFEC";
		container[i + 1].style.display = "none";
	}

	groups[parseInt(elementId) - 1].style.background = "#FFF";
	container[parseInt(elementId)].style.display = "block";
}