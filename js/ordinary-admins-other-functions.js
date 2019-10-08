function flushTable() {
	alert("请点击左侧竞赛名称刷新");
}

function returnBack() {
	'use strict';
	SetCookie(Base.encode("password"), "", 0, 0);
	window.location.href = 'login.html';
}

/*function explorCurrentTable() {
	var isSelected = false;
	var tables = document.getElementById("score-container").getElementsByTagName("table");
	for (var i = 0; i < tables.length; i++) {
		if (tables[i].style.display == "table") { isSelected = true; exportScoreToExcel(tables[i].id); break; }
	}
	if (!isSelected) { alert("当前没有可以导出的数据表"); }
}

var idTmr;

function getExplorer() {
	var explorer = window.navigator.userAgent;
	if (explorer.indexOf("MSIE") >= 0) { return 'ie'; }	//ie
	else if (explorer.indexOf("Firefox") >= 0) { return 'Firefox'; }	//firefox
	else if(explorer.indexOf("Chrome") >= 0){ return 'Chrome'; }	//Chrome
	else if(explorer.indexOf("Opera") >= 0){ return 'Opera'; }	//Opera
	else if(explorer.indexOf("Safari") >= 0){ return 'Safari'; }	//Safari
}

function exportScoreToExcel(tableid) {
	if(getExplorer() == 'ie') {
		var curTbl = document.getElementById(tableid);
		var oXL = new ActiveXObject("Excel.Application");
		var oWB = oXL.Workbooks.Add();
		var xlsheet = oWB.Worksheets(1);
		var sel = document.body.createTextRange();
		sel.moveToElementText(curTbl);
		sel.select();
		sel.execCommand("Copy");
		xlsheet.Paste();
		oXL.Visible = true;

		try {
			var fname = oXL.Application.GetSaveAsFilename("Excel.xlsx", "Excel Spreadsheets (*.xlsx), *.xlsx");
		} catch (e) {
			print("Nested catch caught " + e);
		} finally {
			oWB.SaveAs(fname);
			oWB.Close(savechanges = false);
			oXL.Quit();
			oXL = null;
			idTmr = window.setInterval("Cleanup();", 1);
		}
	} else {
		tableToExcel(tableid, '总分');
	}
}

function Cleanup() {
	window.clearInterval(idTmr);
	CollectGarbage();
}

var tableToExcel = (function() {  
	var uri = 'data:application/vnd.ms-excel;base64,';
	var template = '<html><head><meta charset="UTF-8"></head><body><table>{table}</table></body></html>';
	var base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) };
	var format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) };

	return function(table, name) {
		if (!table.nodeType) { table = document.getElementById(table); }
		var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML};
		window.location.href = uri + base64(format(template, ctx));
	}
})();*/



// 另一个保存前端table的方法，据说能保存多个table到同一个Excel表中去

function explorCurrentTable() {
	var name = document.getElementById("compsName").innerHTML;
	var content = document.getElementById("score-container").childNodes;
	var group = document.getElementById("groups").childNodes;

	var ids = [];
	var names = [];
	var current = 0;

	for(var index = 0; index < content.length && current < group.length; index++) {
		if(content[index].nodeName !== "TABLE") {
			continue;
		}
		if(group[current].innerHTML === "总分") {
			ids.unshift(content[index].id);
			names.unshift(group[current].innerHTML);
		} else {
			ids.push(content[index].id);
			names.push(group[current].innerHTML);
		}
		current++;
	}
	//tablesToExcel(['table-2', 'table-0', 'table-1'], ['总分', '班徽组', '班旗组'], name + ".xls", "Excel");
	tablesToExcel(ids, names, name + ".xls", "Excel");
}

function tablesToExcel(tables, wsnames, wbname, appname){
	//导出excel包含多个sheet
	//tables:tableId的数组;wsbames:sheet的名字数组;wbname:工作簿名字;appname:Excel

	var uri = 'data:application/vnd.ms-excel;base64,';
	var tmplWorkbookXML = '<?xml version="1.0"?><?mso-application progid="Excel.Sheet"?><Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' + '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office"><Author>Axel Richter</Author><Created>{created}</Created></DocumentProperties>' + '<Styles>' + '<Style ss:ID="Currency"><NumberFormat ss:Format="Currency"></NumberFormat></Style>' + '<Style ss:ID="Date"><NumberFormat ss:Format="Medium Date"></NumberFormat></Style>' + '</Styles>' + '{worksheets}</Workbook>';
	var tmplWorksheetXML = '<Worksheet ss:Name="{nameWS}"><Table>{rows}</Table></Worksheet>';
	var tmplCellXML = '<Cell{attributeStyleID}{attributeFormula}><Data ss:Type="{nameType}">{data}</Data></Cell>';
	var base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
	var format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }

	var ctx = "";
	var workbookXML = "";
	var worksheetsXML = "";
	var rowsXML = "";

	for (var i = 0; i < tables.length; i++) {
		if (!tables[i].nodeType) tables[i] = document.getElementById(tables[i]);

		// 控制要导出的行数
		for (var j = 0; j < tables[i].rows.length; j++) {
			rowsXML += '<Row>';

			for (var k = 0; k < tables[i].rows[j].cells.length; k++) {
				var dataType = tables[i].rows[j].cells[k].getAttribute("data-type");
				var dataStyle = tables[i].rows[j].cells[k].getAttribute("data-style");
				var dataValue = tables[i].rows[j].cells[k].getAttribute("data-value");
				dataValue = (dataValue)?dataValue:tables[i].rows[j].cells[k].innerHTML;
				var dataFormula = tables[i].rows[j].cells[k].getAttribute("data-formula");
				dataFormula = (dataFormula)?dataFormula:(appname=='Calc' && dataType=='DateTime')?dataValue:null;
				ctx = {  attributeStyleID: (dataStyle=='Currency' || dataStyle=='Date')?' ss:StyleID="'+dataStyle+'"':'' , nameType: (dataType=='Number' || dataType=='DateTime' || dataType=='Boolean' || dataType=='Error')?dataType:'String' , data: (dataFormula)?'':dataValue , attributeFormula: (dataFormula)?' ss:Formula="'+dataFormula+'"':''};
				rowsXML += format(tmplCellXML, ctx);
			}
			rowsXML += '</Row>'
		}
		ctx = {rows: rowsXML, nameWS: wsnames[i] || 'Sheet' + i};
		worksheetsXML += format(tmplWorksheetXML, ctx);
		rowsXML = "";
	}

	ctx = {created: (new Date()).getTime(), worksheets: worksheetsXML};
	workbookXML = format(tmplWorkbookXML, ctx);

	// 查看后台的打印输出
	// console.log(workbookXML);

	var link = document.createElement("A");
	link.href = uri + base64(workbookXML);
	link.download = wbname || 'Workbook.xls';
	link.target = '_blank';
	document.body.appendChild(link);
	link.click();
	document.body.removeChild(link);
}