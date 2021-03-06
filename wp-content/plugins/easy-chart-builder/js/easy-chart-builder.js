/**
 * Handle: easyChartBuilder
 * Version: 0.10.2
 * Enqueue: true
 *
 * Author: dyerware
 * Author URI: http://www.dyerware.com
 * Copyright © 2009, 2010  dyerware
 * Support: support@dyerware.com
 */
 

String.prototype.trim = function () 
{
    return this.replace(/^\s*/, "").replace(/\s*$/, ""); 
};


function easyChartBuilder()
{  	
}

function wpEasyChartToggle(id) {
    var e = document.getElementById(id);
    if(e.style.display == 'block')
        e.style.display = 'none';
    else
        e.style.display = 'block';
}
    
easyChartBuilder.prototype.pieChart = function(chartId, chartImg, chartWidth, chartHeight, chartHandle)
{
    var chartTitle = chartHandle["title"];
    
    var tempString = chartHandle["groupnames"];

    var tempGroupNames = tempString.split(",");
    var chartGroups = tempGroupNames.length;
    var chartGroupNames = this.constructList(tempString, ",", "|",0, false);
    
    if (chartHeight >= chartWidth)     
        {chartHeight = parseInt(chartWidth / 2.0);}
        
    tempString = chartHandle["groupcolors"];
    var chartColors = this.constructList(tempString, ",", ",",tempGroupNames.length, false);

    var groupName;
    var chartValues = new Array(chartGroups);
    for (var i = 0; i < chartGroups; i++)
    {
        groupName = "group" + (i+1) + "values";
        tempString = chartHandle[groupName];
        
        chartValues[i] = this.extractValues(tempString, ",", 1); 
    }

    var hideChartData = chartHandle["hidechartdata"];
    if (hideChartData == false)
    {
        var tblIdContainer = document.getElementById(chartId + "_data");
        var tblId = document.getElementById(chartId + "_dataTable");
        if (tblId == undefined && tblIdContainer != undefined)
        {
            tblId = document.createElement('div');
            tblId.setAttribute('id',chartId + '_dataTable');  
                                
            tblId.innerHTML = this.buildChartDataPie(chartHandle, tempGroupNames, chartValues);
            tblIdContainer.appendChild(tblId);
        }
    }

    
    var maxMin = this.normalizeValues(chartValues, false, chartHandle, false);   
    var chartValuesString = this.encodeValues(chartValues, ""); 
    var chartColor = chartHandle["chartcolor"];    
    var chartFadeColor = chartHandle["chartfadecolor"];                  
	 	var url_src ="http://chart.apis.google.com/chart?cht=p&chbh=r,0.2,1.0&chs=" + chartWidth + "x" + chartHeight + 
 	"&chma=10,10,10,40" +
 	"&chf=c,lg,90," + chartColor + ",0.2," + chartFadeColor + ",0|bg,s,00000000" + 
 	"&chtt=" + chartTitle + 
 	"&chl=" + chartGroupNames +
 	"&chco=" + chartColors + "&chd=e:" + chartValuesString;
    
    chartImg.src = url_src;
};
    
    
 easyChartBuilder.prototype.groupChart = function(chartType, chartId, chartImg, chartWidth, chartHeight, chartHandle)
{
    var chartTitle = chartHandle["title"];
    
    var tempString = chartHandle["groupnames"];
    var tempGroupNames = tempString.split(",");
    var chartGroups = tempGroupNames.length;
    var chartGroupNames = this.constructList(tempString, ",", "|",0, false);
        
    tempString = chartHandle["groupcolors"];
    var chartColors = this.constructList(tempString, ",", ",",0, false);
    
    tempString = chartHandle["valuenames"];
    var order = false;
    if (chartType == "horizbar" || chartType == "horizbarstack") {order = true;}
    var chartValueNames = "|" + this.constructList(tempString, ",", "|",0, order);

    var groupName;
    var chartValues = new Array(chartGroups);
    for (var i = 0; i < chartGroups; i++)
    {
        groupName = "group" + (i+1) + "values";
        tempString = chartHandle[groupName];
        chartValues[i] = this.extractValues(tempString, ",", 0); 
    }
    
    var hideChartData = chartHandle["hidechartdata"];
    if (hideChartData == false)
    {
        var tblIdContainer = document.getElementById(chartId + "_data");
        var tblId = document.getElementById(chartId + "_dataTable");
        if (tblId == undefined && tblIdContainer != undefined)
        {
            tblId = document.createElement('div');
            tblId.setAttribute('id',chartId + '_dataTable');  
                                
            tblId.innerHTML = this.buildChartData(chartHandle, tempGroupNames, chartValues);
            tblIdContainer.appendChild(tblId);
        }
    }
    
    var isStacked = false;
    if (chartType == "horizbarstack" || chartType == "vertbarstack") 
    {
        isStacked = true;
    }
    
    var maxMin = this.normalizeValues(chartValues, true, chartHandle, isStacked);  
    var minAxis = chartHandle["minaxis"];
    if (minAxis == "")
    {
        if (maxMin[1] < 0)
             {minAxis = maxMin[1];}
        else {minAxis = "0";}
    }
        
    tempString = chartHandle["watermark"]; 
    var wmColor = chartHandle["watermarkcolor"];
    var wmPrep = this.buildWatermark(maxMin, tempString, wmColor, chartHandle["minaxis"]); 
         
    var chartValuesString = this.encodeValues(chartValues, ",");  
    var chartColor = chartHandle["chartcolor"];
    var chartFadeColor = chartHandle["chartfadecolor"];
    
    var chartMarkers = this.buildMarkers(chartHandle, tempGroupNames.length);
    
    var url_src = null;   
    var common_parts = "chs=" + chartWidth + "x" + chartHeight + 
     	"&chma=10,10,10,40" +
     	"&chf=c,lg,90," + chartColor + ",0.2," + chartFadeColor + ",0|bg,s,00000000" + 
     	"&chtt=" + chartTitle + 
     	"&chdl=" + chartGroupNames + "&chdlp=b" +
     	"&chco=" + chartColors + "&chd=e:" + chartValuesString + "&chxl=1:" + chartValueNames + wmPrep +
     	chartMarkers;
    
    if (chartType == "vertbar")
    {
        url_src ="http://chart.apis.google.com/chart?cht=bvg&chbh=r,0.2,1.0&" + common_parts +
     	"&chxt=y,x&chxr=0," + minAxis + "," + maxMin[0];
    }
    else if (chartType == "horizbar")
    {
    	url_src ="http://chart.apis.google.com/chart?cht=bhg&chbh=r,0.2,1.0&" + common_parts +
     	"&chxt=x,y&chxr=0," + minAxis + "," + maxMin[0];
    }
    else if (chartType == "vertbarstack")
    {
        url_src ="http://chart.apis.google.com/chart?cht=bvs&chbh=r,0.2,1.0&" + common_parts +
     	"&chxt=y,x&chxr=0," + minAxis + "," + maxMin[0];
    }
    else if (chartType == "horizbarstack")
    {
    	url_src ="http://chart.apis.google.com/chart?cht=bhs&chbh=r,0.2,1.0&" + common_parts +
     	"&chxt=x,y&chxr=0," + minAxis + "," + maxMin[0];
    }    
    else if (chartType == "line")
    {    
    	url_src ="http://chart.apis.google.com/chart?cht=lc&" + common_parts +
        "&chxt=y,x&chxr=0," + minAxis + "," + maxMin[0];
    }    
    else
    {
        alert("Unknown chart type: " + chartType);
        url_src = "NO_VALID_CHART_TYPE";
    }   

 
    chartImg.src = url_src;
};
 
easyChartBuilder.prototype.buildMarkers = function(handle, numGroups)
{
    var markColor = handle["markercolor"];
    var markStr = "";
    var letter = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var letterIndex = 0;
    
    for (var t=0;t<numGroups;t++)
    {
        var groupMarkStr = handle["group" + (t+1) + "markers"];
        
        if (groupMarkStr == '')
        {
            continue;
        }
        
        var marks = groupMarkStr.split(",");
        if (marks.length == 0 || marks == '')
        {
            continue;
        }
        
        for (var m = 0; m < marks.length; m++)
        {
            var prep = "y;s=map_xpin_letter;d=pin_sright," + letter.charAt(letterIndex) + "," + markColor + 
            ";dp=" + marks[m] + ";ds="+t+";of=12,0";
            
            if (letterIndex)
            {
                markStr = markStr + "|" + prep;
            }
            else
            {
                markStr = "&chem=" + prep;
            }
            
            letterIndex++;
        }
    }
    
    return markStr;
};  

easyChartBuilder.prototype.buildWatermark = function(maxMin, wmString, wmColor, minAxis)
{
    var wm = new Array();
    
    if (wmString.length == 0)
    {return "";} 
    
    wm = this.extractValues(wmString, ",", 2); 
    if (wm.length == 0)
    {return "";}
            
    if (wm.length == 1)
        wm[1] = maxMin[1];
       
    var maxit = maxMin[0];
    
    if (minAxis != "")
    {
        var lowValue = parseFloat(minAxis);

        if (wm[0] <= lowValue)
            {wm[0] = 0.0;}
        else
            {wm[0] -= lowValue;}
        if (wm[1] <= lowValue)
            {wm[1] = 0.0;}
        else
            {wm[1] -= lowValue;}   
            
        if (maxit <= lowValue)
            {maxit = 0.0;}
        else
            {maxit -= lowValue;}
    }
       
    // Normalize watermarks
    wm[0] = wm[0] / maxit;
    wm[1] = wm[1] / maxit;
    var trimMe = new Number(wm[0]);
    wm[0] = trimMe.toPrecision(2);
    trimMe = new Number(wm[1]);
    wm[1] = trimMe.toPrecision(2);
    
    return "&chm=r," + wmColor + ",0," + wm[0] + "," + wm[1];
}


easyChartBuilder.prototype.buildChartData = function(handle, headers, valueGrid)
{
    var output;
    var len = headers.length;

    var headerColors = handle["groupcolors"].split(",");
    var valueTitles = handle["valuenames"].split(",");
    var css = handle["datatablecss"];
    var currency = handle["currency"];
    var precision = handle["precision"];
    var precisionVal = 0;

    if (precision != "")
    {
        precisionVal = parseInt(precision);
    }
                  
    output = "<table class='" + css + "' border='0' style='text-align:center;' frame='box' align='center' bgcolor='FFFFFF'><thead><tr><td></td>";

    for (var h=0;h<len;h++)
        {output = output + "<th>" + headers[h].trim() + "</th>";}
    output = output + "</tr><tr style='height:4px;'><td></td>";

    for (var i=0;i<len;i++)
        {output = output + "<th style='background: #FFFFFF; background-color:#" + headerColors[i].trim() + "'></th>";}
    output = output + "</tr></thead>";
            
    for (var t = 0; t < valueTitles.length; t++)
    {       
        var tval = 0.0;
        
        output = output + "<tr><th align='right'>" + valueTitles[t].trim() + "</th>";
        for (var v = 0; v < valueGrid.length; v++)
            {
                if (precision == "")
                {tval = valueGrid[v][t];}
                else
                {tval = valueGrid[v][t].toFixed(precisionVal);}
                output = output + "<td>" + currency + tval + "</td>";
             }
        output = output + "</tr>";
    }   

    output = output + "</table>";
    return output;
};
     
easyChartBuilder.prototype.buildChartDataPie = function(handle, headers, valueGrid)
{
    var output;
    var len = headers.length;
    
    var headerColors = handle["groupcolors"].split(",");
    var valueTitles = handle["valuenames"].split(",");
    var css = handle["datatablecss"];
    var currency = handle["currency"];
    var precision = handle["precision"];
    var precisionVal = 0;

    if (precision != "")
    {
        precisionVal = parseInt(precision);
    }
        
    output = "<table  class='" + css + "' border='0' style='text-align:center;' frame='box' align='center' bgcolor='FFFFFF'><thead><tr><td></td>";

    for (var h=0;h<len;h++)
        {output = output + "<th>" + headers[h].trim() + "</th>";}
    output = output + "</tr><tr style='height:4px;'><td></td>";

    for (var i=0;i<len;i++)
        {output = output + "<th style='background-color:#" + headerColors[i].trim() + "'></th>";}
    output = output + "</tr></thead>";
        
    output = output + "<tr><td></td>";      
    for (var t = 0; t < valueGrid.length; t++)
        {
            if (precision == "")
                {tval = valueGrid[t];}
            else
                {tval = valueGrid[t].toFixed(precisionVal);}
            output = output + "<td>" + currency + tval + "</td>";
        }
    
    output = output + "</tr></table>";
    return output;
};
    
easyChartBuilder.prototype.encodeValues =  function(valueGrid, separator)
{
    var chartEncodeMap = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
    var output = "";
    var len = valueGrid.length;
    
    for (var t = 0; t < len; t++)
    {
        if (t > 0)
            {output = output + separator;}
            
        for (var v = 0; v < valueGrid[t].length; v++)
        {
            var quotient = Math.floor(valueGrid[t][v] / chartEncodeMap.length);
            var remainder = valueGrid[t][v] - chartEncodeMap.length * quotient;
            output = output + chartEncodeMap.charAt(quotient) + chartEncodeMap.charAt(remainder);
        }
    }   

    return output;
};
     
easyChartBuilder.prototype.normalizeValues = function(valueGrid, hasAxis, chartHandle, isStacked)
{
    var len = valueGrid.length;
    var maxMin = new Array();
    var t, v, ratio;
    var minAxis = chartHandle["minaxis"];
    var currMax;
    var currGroup;
    
    maxMin[0] = valueGrid[0][0];
    maxMin[1] = maxMin[0];
    maxMin[2] = 0;
    scaleMax = 0;
    
    if (isStacked)
    {
        for (v = 0; v < valueGrid[0].length; v++)
        {
            currGroup = 0;
            for (t = 0; t < len; t++)
            {
                currGroup += valueGrid[t][v];
            }
            
            if (v)
            {
                maxMin[0] = Math.max(currGroup , maxMin[0]);
                maxMin[1] = Math.min(currGroup, maxMin[1]);
            }
            else
            {
                maxMin[0] = currGroup;
                maxMin[1] = currGroup;
            }
        }        
    }
    else
    {       
        for (t = 0; t < len; t++)
        {
            for (v = 0; v < valueGrid[t].length; v++)
            {
                maxMin[0] = Math.max(valueGrid[t][v] , maxMin[0]);
                maxMin[1] = Math.min(valueGrid[t][v] , maxMin[1]);
            }
        }
    }
    
    scaleMax = maxMin[0];
    
    if (isStacked == false && hasAxis == true && minAxis != "")
    {
        var lowValue = parseFloat(minAxis);
       
        for (t = 0; t < len; t++)
        {
            for (v = 0; v < valueGrid[t].length; v++)
                {
                    if (valueGrid[t][v] < lowValue) 
                         {valueGrid[t][v]  = 0;}
                    else {valueGrid[t][v] -= lowValue;}
                }
        }     
        
        scaleMax -= lowValue;
    }
    
    ratio = 4095 / scaleMax;
    maxMin[2] = ratio;
    
    for (t = 0; t < len; t++)
    {
        for (v = 0; v < valueGrid[t].length; v++)
            {valueGrid[t][v] = parseFloat(valueGrid[t][v] * ratio);} //was parseInt
    }            
        
    return maxMin;
};
    
easyChartBuilder.prototype.extractValues = function(origString, separator, limit)
{
    var retString = new Array();
    var tempList = origString.split(separator);
    
    if (limit == 0 || limit > tempList.length)
        {limit = tempList.length;}        
        
    for (var t = 0; t < limit; t++)
    {
        retString[t] = parseFloat(tempList[t]);    
        if (isNaN(retString[t]))
            {retString[t] = 0;}
    } 
    
    return retString;        
};  
        
easyChartBuilder.prototype.constructList = function(origString, separator, joiner, limit, flipOrder)
{
    var retString = "";
    var tempList = origString.split(separator);
    
    if (limit == 0 || limit > tempList.length)
        {limit = tempList.length; }         
        
    for (var t = 0; t < limit; t++)
    {
        // Trim any excess spaces back
        tempList[t] = tempList[t].trim();
        if (t == 0)
            {retString = tempList[t];}
        else
        {
            if (flipOrder)
                {retString = tempList[t] + joiner + retString;}
            else
                {retString = retString + joiner + tempList[t];}
        }
    }
    
    return retString;
};
    
easyChartBuilder.prototype.wpNewChart= function(chartId, chartHandle)
{
    
    var chartWidth = chartHandle["width"];
    var chartHeight = chartHandle["height"];      
    var img_ratio = chartHeight / chartWidth;
    
    // Fit chart to size (bigger or smaller) 
    var mydiv = document.getElementById(chartId);
    var new_width = mydiv.offsetWidth;
    var new_height = chartHeight * (new_width / chartWidth);

   	if (new_height <= 0)
    {
        new_height = chartHeight;
    }
    
    if (new_width <= 0)
    {
        new_width = chartWidth;
    }
    
    
   	// Prune by maximum google chart size 
   	if (new_width * new_height > 300000)
   	{
   		var adjust = 300000 / (new_width * new_height);
   		adjust = Math.sqrt(adjust);
   		new_width = new_width * adjust;
   		new_height = new_height * adjust;
   	}
   	
    
   
    new_width = parseInt(new_width.toString());
   	new_height = parseInt(new_height.toString());

   	var imgId = document.getElementById(chartId+"_img");
   	
   	var chartType = chartHandle["type"]; 
    if (chartType == "pie")
    {
        this.pieChart(chartId, imgId, new_width, new_height, chartHandle);
    }
    else
    {
        this.groupChart(chartType, chartId, imgId, new_width, new_height, chartHandle);
    }
    
    /*
    else if (chartType == "vertbar")
    {
        this.vertBarChart(chartId, imgId, new_width, new_height, chartHandle);
    }
    else if (chartType == "horizbar")
    {
        this.horizBarChart(chartId, imgId, new_width, new_height, chartHandle);
    }
    else if (chartType == "line")
    {
        this.lineChart(chartId, imgId, new_width, new_height, chartHandle);
    }    
    else
    {
        alert("Unknown chart type: " + chartType);
        imgId.src = "NO_VALID_CHART_TYPE";
    }   
    */
};

var wpEasyChart = new easyChartBuilder();

