'use strict';

var compsname = "";
var competitor = "";

var worksnumber = "";
var competitorinfo = "";
var scorerubic = "";
var current = "";
var scorelimit = null;
var issubmitting = false;

window.onresize = function() {
	changeCompetitorScoreSize();
};

function compsNameInquirer() {
	changeCompetitorScoreSize();
	var name = getPar('compsname');
	if (name) {
		try {
			compsname = Base.decode(decodeURIComponent(name));
		}
		catch(e) {
			requestAborted(true);
		}
		
		var xmlhttp = null;
		if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
		else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

		xmlhttp.open("POST", "files/bin/comps-info-query.php", true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("compsname=" + compsname);

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304)) {
				if (xmlhttp.responseText === "failed") {
					returnBack();
				}
				else {
					var solution = xmlhttp.responseText.split("@c@");
					worksnumber = parseInt(solution[0]);
					competitorinfo = solution[1];
					scorerubic = solution[2];
					current = parseInt(solution[3]) - 1;
					if (current + 1 === worksnumber) {
						markingEnd();
					}
					document.getElementById("public-header-title").innerHTML = compsname;
					changeCompetitorInfo(parseInt(solution[3]));
				}
			}
			else {
				if (xmlhttp.readyState !== 4 && xmlhttp.status !== 200 && xmlhttp.status !== 304) {
					requestAborted(true);
				}
			}
		};
	}
	else {
		returnBack();
	}
}

function changeCompetitorScoreSize() {
	var winHeight = 0;
	if (window.innerHeight) {
		winHeight = window.innerHeight;
	}
	else if ((document.body) && (document.body.clientHeight)) {
		winHeight = document.body.clientHeight;
	}
	document.getElementById("competitorScore").style.maxHeight = Math.max(winHeight - 320, 50) + "px";
}

function changeCompetitorInfo(index) {
	var i = 0;
	var input = document.getElementsByName("score");
	var plus = document.getElementsByName("plus");
	var minus = document.getElementsByName("minus");
	
	if (index < 0 || index >= worksnumber || index > current + 1) {
		return false;
	}
	else {
		competitor = getCompetitorByIndex(index);
		document.getElementById("workName").innerHTML = competitor[1];
		document.getElementById("captainName").innerHTML = competitor[0];
		document.getElementById("groupName").innerHTML = competitor[2];
		
		var rubic = getRubricByGroup(competitor[2]);
		scorelimit = getLimitsByInfo(rubic);
		
		var scoreitem = "";
		for (i = 0; i < scorelimit.length; i++) {
			scoreitem += "<li class=\"competitor-score-item\"><div class=\"competitor-score-inputm public-horizontally-container\"><div class=\"competitor-iteml\">" + scorelimit[i][0] + "（" + scorelimit[i][2] + "分）" + "</div><div class=\"competitor-itemr\"><input class=\"itemr-scoreInput\" type=\"text\" name=\"score\" value=\"0\" onChange=\"overflowRemove(this, " + scorelimit[i][1] + ", " + scorelimit[i][2] + ")\" onKeyUp=\"positiveIntOnly(this)\"><input class=\"itemr-scorePlus\" type=\"button\" name=\"plus\" onClick=\"scoreChange(this, " + scorelimit[i][1] + ", " + scorelimit[i][2] + ")\"><input class=\"itemr-scoreMinus\" type=\"button\" name=\"minus\" onClick=\"scoreChange(this, " + scorelimit[i][1] + ", " + scorelimit[i][2] + ")\"></div></div></li>";
		}
		document.getElementById("competitorScore").innerHTML = scoreitem;
		
		if (index < current) {
			for (i = 0; i < input.length; i++) {
				input[i].style.background = "#f0f0f0";
				input[i].disabled = plus[i].disabled = minus[i].disabled = true;
			}
			document.getElementById("submit").disabled = document.getElementById("recharge").disabled = true;
		}
		else {
			for (i = 0; i < input.length; i++) {
				input[i].value = scorelimit[i][1];
				input[i].style.background = "#f8f8f8";
				input[i].disabled = plus[i].disabled = minus[i].disabled = false;
			}
			document.getElementById("submit").disabled = document.getElementById("recharge").disabled = false;
			current = index;
		}
		return true;
	}
}

function competitorInfoSubmit() {
	if (issubmitting) {
		return false;
	}
	
	issubmitting = true;
	
	var scoreInputer = document.getElementsByName("score");
	var minnumber = 0;
	var maxnumber = 0;
	var scoredetailed = "";
	var minSum = 0;
	var maxSum = 0;
	var scoreSum = 0;
	
	for (var i = 0; i < scoreInputer.length; i++) {
		minnumber += 1 * (scoreInputer[i].value === scorelimit[i][1]);
		maxnumber += 1 * (scoreInputer[i].value === scorelimit[i][2]);
		scoredetailed += "@c@" + scorelimit[i][0] + "@r@" + scoreInputer[i].value;
		minSum += parseInt(scorelimit[i][1]);
		maxSum += parseInt(scorelimit[i][2]);
		scoreSum += parseInt(scoreInputer[i].value);
	}
	if (minnumber === scoreInputer.length || maxnumber === scoreInputer.length) {
		alert("所给分值不能全为最低分或最高分！");
		issubmitting = false;
		return false;
	}
	else if (!confirm("您本次给出的总成绩为：" + scoreSum + "分\n\n最低分：" + minSum + "分，最高分：" + maxSum + "分\n\n是否继续？")) {
		issubmitting = false;
		return false;
	}
	
	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "files/bin/competitor-info-submit.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("compsname=" + encodeURIComponent(compsname) + "&playername=" + encodeURIComponent(competitor[0]) + "&playergroup=" + encodeURIComponent(competitor[2]) + "&workname=" + encodeURIComponent(competitor[1]) + "&judgename=" + encodeURIComponent(Base.decode(GetCookieByName(Base.encode("username")))) + "&scoredetailed=" + encodeURIComponent(scoredetailed.slice(3)) + "&scoresum=" + encodeURIComponent(scoreSum) + "&current=" + encodeURIComponent(current));

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4) {
			issubmitting = false;
			if (xmlhttp.status === 200 || xmlhttp.status === 304) {
				if (parseInt(xmlhttp.responseText) >= 0 && parseInt(xmlhttp.responseText) < worksnumber) {
					changeCompetitorInfo(parseInt(xmlhttp.responseText));
					return true;
				}
				else if (xmlhttp.responseText === "finished") {
					markingEnd();
				}
				else if (xmlhttp.responseText === "failed") {
					requestAborted(false);
					return false;
				}
				else {
					requestAborted(false);
					//alert("Ajax.responseText:\n" + xmlhttp.responseText);
					return false;
				}
			}
			else if (xmlhttp.status !== 200 && xmlhttp.status !== 304) {
				requestAborted(false);
				return false;
			}
		}
	};
	
	return true;
}

function competitorInfoRecharge() {
	return changeCompetitorInfo(current);
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

function overflowRemove(element, minScore, maxScore) {
	if (element.value.length === 0) {
		element.value = "0";
	}
	element.value = Math.max(parseInt(element.value), minScore);
	element.value = Math.min(parseInt(element.value), maxScore);
}

function positiveIntOnly(element) {
	if (element.value.length === 1) {
		element.value = element.value.replace(/[^0-9]/g,'');
	}
	else {
		element.value = element.value.replace(/\D/g,'');
	}
}

function scoreChange(element, minScore, maxScore) {
	var scoreInputer = element.parentNode.getElementsByTagName("input");
	if (element.name === "plus") {
		scoreInputer[0].value = Math.min(parseInt(scoreInputer[0].value) + 1,  maxScore);
	}
	else if (element.name === "minus") {
		scoreInputer[0].value = Math.max(parseInt(scoreInputer[0].value) - 1,  minScore);
	}
	else {
		scoreInputer[0].value = "error in this.name";
	}
}

function getCompetitorByIndex(index) {
	index = Math.floor(Math.abs(index));
	var group = 1;
	var solution = null;
	var competitorgroup = competitorinfo.split("@g@");
	while (group < competitorgroup.length) {
		solution = competitorgroup[group].split("@r@");
		if (index > solution.length / 2 - 1) {
			index -= solution.length / 2;
			group += 2;
		}
		else {
			return Array(solution[index * 2], solution[index * 2 + 1], competitorgroup[group - 1]);
		}
	}
	return false;
}

function getCompetitorByGroup(group, index) {
	group = Math.floor(Math.abs(group));
	index = Math.floor(Math.abs(index));
	var competitorgroup = competitorinfo.split("@g@");
	if (group > (competitorgroup.length / 2) - 1) { return false; }
	var competitorindex = competitorgroup[group * 2 + 1].split("@r@");
	if (index > (competitorindex.length / 2) - 1) { return false; }
	return Array(competitorindex[index * 2], competitorindex[index * 2 + 1], competitorgroup[group * 2]);
}

function getRubricByGroup(group) {
	var competitorgroup = scorerubic.split("@g@");
	for (var i = 0; i < competitorgroup.length; i += 2) {
		if (competitorgroup[i] === group) {
			return competitorgroup[i + 1];
		}
	}
	return false;
}

function getLimitsByRubic(rubic, info) {
	var competitorrubic = info.split("@r@");
	for (var i = 0; i < competitorrubic.length; i += 2) {
		if (competitorrubic[i] === rubic) {
			return Array(competitorrubic[i + 1].split("@s@")[0], competitorrubic[i + 1].split("@s@")[1]);
		}
	}
	return false;
}

function getLimitsByInfo(info) {
	var competitorrubic = info.split("@r@");
	var limits = new Array(competitorrubic.length / 2);
	for (var i = 0; i < competitorrubic.length / 2; i++) {
		limits[i] = Array(competitorrubic[i * 2], competitorrubic[i * 2 + 1].split("@s@")[0], competitorrubic[i * 2 + 1].split("@s@")[1]);
	}
	return limits;
}

function returnBack() {
	window.location.href = "skiping.html";
}

function requestAborted(isback) {
	alert("请求失败，请稍后重试");
	if (isback) {
		returnBack();
	}
}

function markingEnd() {
	alert("评分已结束！");
	returnBack();
}
