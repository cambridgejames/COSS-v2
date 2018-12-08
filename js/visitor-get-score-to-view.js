function getAllInformations() {
	var compsname = getPar('compsname');
	if (!compsname) { requestAborted(true); }
	try {
		compsname = Base.decode(decodeURIComponent(compsname));
	} catch(e) {
		requestAborted(true);
	}

	document.getElementById("comps-name").innerHTML = compsname;

	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "PHP/bin/visitor-get-all-information.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("compsname=" + compsname + "&randomNow=" + Math.random());

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304)) {
			if (xmlhttp.responseText === "failed") {
				returnBack();
			}
			else {
				var information = xmlhttp.responseText.split("@c@");
				if (information.length !== 3) { requestAborted(true); }
				
				document.getElementById("judge-numb").innerHTML = information[0];
				document.getElementById("groups").innerHTML = information[1];
				document.getElementById("scores").innerHTML = information[2];

				setIdforItems();

				var e = document.createEvent('MouseEvent');
				e.initEvent('click', false, false);
				setTimeout(document.getElementById("0").dispatchEvent(e),0);
			}
		}
		else {
			if (xmlhttp.readyState !== 4 && xmlhttp.status !== 200 && xmlhttp.status !== 304) {
				requestAborted(true);
			}
		}
	};

}

function setIdforItems() {
	var groups = document.getElementById("groups").children;
	var container = document.getElementById("score-container").getElementsByTagName("table");
	for (var i = 0; i < groups.length; i++) {
		groups[i].id = i;
		container[i].className = "dynamic-page-score-value";
		var row = container[i].getElementsByTagName("tr");
		for (var j = 0; j < row.length; j++) {
			row[j].className = j % 2 == 0 ? "double-line" : "single-line";
		}
	}
}

function clickGroups(elementId) {
	var groups = document.getElementById("groups").children;
	var container =  document.getElementById("score-container").getElementsByTagName("table");

	for (var i = 0; i < groups.length; i++) {	// 清空样式
		groups[i].className = "groupItem";
		container[i].style.display = "none";
	}

	groups[parseInt(elementId)].className = "groupItem-selected";
	container[parseInt(elementId)].style.display = "table";
}

function getPar(par){
	var local_url = document.location.href;
	var get = local_url.indexOf(par + "=");
	if (get === -1) {
		return false;
	}
	var get_par = local_url.slice(par.length + get + 1);
	var nextPar = get_par.indexOf("&");
	if (nextPar !== -1) {
		get_par = get_par.slice(0, nextPar);
	}
	return get_par;
}

function returnBack() {
	window.location.href = "visit.html";
}

function requestAborted(isback) {
	alert("请求失败，请稍后重试");
	if (isback) {
		returnBack();
	}
}