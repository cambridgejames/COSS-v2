var startDate = new Date();
startDate.setDate(startDate.getDate() + 1);
var timeLaster;

function showWellcomPage() {
	'use strict';

	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "PHP/bin/admins-compsname-inquirer.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("randomNow=" + Math.random());

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304) && xmlhttp.responseText != "failed") {
			var strs = xmlhttp.responseText.split("@c@");
			document.getElementById("admin-name").innerHTML = strs[0] + " ◢";
			document.getElementById("home-container").innerHTML = strs[1];

			var compslist = document.getElementById("home-container").children;
			if (compslist.length > 0) {	// 响应元素的click()事件，兼容Safari浏览器的写法
				var e = document.createEvent('MouseEvent');
				e.initEvent('click', false, false);
				setTimeout(compslist[0].dispatchEvent(e),0);
			}
		}
		else {
			if (xmlhttp.readyState !== 4 && (xmlhttp.status !== 200 && xmlhttp.status !== 304) || xmlhttp.responseText == "failed") {
				alert("请求失败，请重试！");
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
	xmlhttp.send("compsname=" + compsname + "&randomNow=" + Math.random());

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304) && xmlhttp.responseText != "failed") {
			document.getElementById("public-content").innerHTML = xmlhttp.responseText;
			setIdforItems();

			var e = document.createEvent('MouseEvent');
			e.initEvent('click', false, false);
			setTimeout(document.getElementById("0").dispatchEvent(e),0);

			getStartTime();
			if (document.getElementById("stratOrEnd").value == "立即开始") {
				setStartTimeLaster(false);
			} else {
				document.getElementById("timeLast").innerHTML = "竞赛正在进行";
				swapElements(document.getElementById("ordinary-body").children[1], document.getElementById("ordinary-body").children[2]);
			}
			
		}
		else {
			if (xmlhttp.readyState !== 4 && (xmlhttp.status !== 200 && xmlhttp.status !== 304) || xmlhttp.responseText == "failed") {
				alert("请求失败，请重试！");
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

function getStartTime() {
	'use strict';

	var timeBox = document.getElementById("time");
	var compsname = document.getElementById("compsName").innerHTML;

	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "PHP/bin/get-comps-start-time.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("compsname=" + compsname + "&randomNow=" + Math.random());

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304) && xmlhttp.responseText != "failed") {
			// 为了兼容闲的蛋疼的低版本Safari浏览器，需要用formatStr()转换从数据库中取出的时间的格式
			startDate = new Date(formatStr(xmlhttp.responseText));
		}
		else {
			if (xmlhttp.readyState !== 4 && (xmlhttp.status !== 200 && xmlhttp.status !== 304) || xmlhttp.responseText == "failed") {
				alert("请求失败，请重试！");
			}
		}
	};
}

function getDifference(startTime) {
	// 计算传入的时间和当前时间的差值
	// 若当前时间不早于传入的时间则返回0
	var timeNow = new Date();
	if (timeNow >= startTime) {return false;}
	var differenceValue = startTime - timeNow;						//时间差的毫秒数
	var days = Math.floor(differenceValue / (24 * 3600 * 1000));	//计算出相差天数
	var leave1 = differenceValue % (24 * 3600 * 1000);				//计算天数后剩余的毫秒数
	var hours = Math.floor(leave1 / (3600 * 1000));					//计算出小时数
	var leave2 = leave1 % (3600 * 1000);							//计算小时数后剩余的毫秒数
	var minutes = Math.floor(leave2 / (60 * 1000));					//计算相差分钟数
	var leave3 = leave2 % (60 * 1000);								//计算分钟数后剩余的毫秒数
	var seconds = Math.floor(leave3 / 1000);						//计算相差秒数
	return new Array(days, hours, minutes, seconds);
}

function setTimeLast() {
	var last = getDifference(startDate);
	if (last) {
		document.getElementById("timeLast").innerHTML = "距竞赛开始还有：" + last[0] + "天" + last[1] + "小时" + last[2] + "分" + last[3] + "秒";
	} else {
		window.clearInterval(timeLaster);
		makeCompStartOrEnd();
	}
}

function setStartTimeLaster(isget = true) {
	// 设置每秒钟更新剩余时间
	if (isget) { getStartTime(); }
	setTimeLast();
	window.clearInterval(timeLaster);
	timeLaster = window.setInterval(setTimeLast, 1000);
}

function makeCompStartOrEnd() {
	var isstart = document.getElementById("stratOrEnd").value == "立即开始" ? 1 : 0;	// 将要转换成的状态
	var xmlhttp = null;
	if (window.XMLHttpRequest) { xmlhttp = new XMLHttpRequest(); }
	else { xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); }

	xmlhttp.open("POST", "PHP/bin/set-comps-start-or-end.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send("compsname=" + document.getElementById("compsName").innerHTML + "&isstartsign=" + isstart + "&randomNow=" + Math.random());

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4 && (xmlhttp.status === 200 || xmlhttp.status === 304) && xmlhttp.responseText == "succeed") {
			if (isstart) {
				// 点击了立即开始
				document.getElementById("stratOrEnd").value = "立即结束";
				window.clearInterval(timeLaster);
				swapElements(document.getElementById("ordinary-body").children[1], document.getElementById("ordinary-body").children[2]);
				document.getElementById("timeLast").innerHTML = "竞赛正在进行";
			} else {
				// 点击了立即结束
				document.getElementById("stratOrEnd").value = "立即开始";
				document.getElementById("timeLast").innerHTML = "竞赛已结束";
				// swapElements(document.getElementById("time"), document.getElementById("score"));
				// setStartTimeLaster();
			}
		}
		else {
			if (xmlhttp.responseText == "failed" || xmlhttp.readyState !== 4 && (xmlhttp.status !== 200 && xmlhttp.status !== 304)) {
				alert("请求失败，请重试！");
			}
		}
	};
}

function swapElements(a, b){
	// 交换两个DOM元素

	// 约束条件：若第一个元素是score则不交换
	if (a.id == "score" || b.id == "time") return;

	if(a == b) return;
	//记录父元素
	var bp = b.parentNode, ap = a.parentNode;
	//记录下一个同级元素
	var an = a.nextElementSibling, bn = b.nextElementSibling;
	//如果参照物是邻近元素则直接调整位置
	if(an == b)return bp.insertBefore(b, a);
	if(bn == a)return ap.insertBefore(a, b);
	if(a.contains(b)) //如果a包含了b
		return ap.insertBefore(b, a), bp.insertBefore(a, bn);
	else
		return bp.insertBefore(a, b), ap.insertBefore(b, an);
}

function formatStr(str) {
	// 将时间转换为兼容Safari的格式
	str = str.replace(/-/g,"/");
	return str;
};