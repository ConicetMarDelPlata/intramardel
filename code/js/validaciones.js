// esto es para ordenar en la grilla
  // Estos separadores deberian ser seteados desde afuera, segun el formato predefinido del usuario
  var dateFormatPattern = "dd-MM-yyyy";
  var timeFormatPattern = "hh:mm:ss";
  var extendedTime = "hh:mm:ss";

/* funciones de validacion. */
/* fecha: 09-2003 */
var a_acute = String.fromCharCode(225);
var e_acute = String.fromCharCode(233);
var razonValid = "abcdefghijklmnopqrstuvwxyzñáéíóúüABCDEFGHIJKLMNOPQRSTUVWXYZÑÁÉÍÓÚÜ_-. 0123456789";
var identify = "abcdefghijklmnopqrstuvwxyzñáéíóúüABCDEFGHIJKLMNOPQRSTUVWXYZÑÁÉÍÓÚÜ_0123456789";
var nameValid = "abcdefghijklmnopqrstuvwxyzñáéíóúüABCDEFGHIJKLMNOPQRSTUVWXYZÑÁÉÍÓÚÜ_-. ";
var domain = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-.0123456789";
var telefonValid = "0123456789()-/ +";
var invalid = '"';
var bankAccountValid = "0123456789-/ ";

var separator = ",";  // use comma as 000's separator
var decpoint = ".";  // use period as decimal point
var percent = "%";

function isDigit(num) {
	if (num.length>1){return false;}
	var string="1234567890";
	if (string.indexOf(num)!=-1){return true;}
	return false;
}


function isBlank(val){
	if(val==null){return true;}
	for(var i=0;i<val.length;i++) {
		if ((val.charAt(i)!=' ')&&(val.charAt(i)!="\t")&&(val.charAt(i)!="\n")&&(val.charAt(i)!="\r")){return false;}
		}
	return true;
}

function isInteger(val){
	if (isBlank(val)){return false;}
	for(var i=0;i<val.length;i++){
		if(!isDigit(val.charAt(i))){return false;}
		}
	return true;
}


function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}


//función para controlar numeros positivos by Vanina Soprano
function funEsNumerico(valor)
{
	var correcto;
	var i;
	var longitud;
	if (valor != null)
		{
		longitud = valor.length;
		i=1;
		correcto = true;
		while ((i<= longitud) && correcto)
			if (valor.charAt(i-1) >= '0' && valor.charAt(i-1) <= '9')
				i++;
			else
				correcto = false;
		}
	else
		correcto = false;
	return(correcto);
}



function isInvalid(s, invalid){
  for (j = 0; j < s.length; j++) {
        if (invalid.indexOf(s.charAt(j)) != -1)
	  {
			alert (invalid.indexOf(s.charAt(j)) + ' ' + s.charAt(j) + ' ' + invalid);
		    return true}
	}
    return false;
}



function findInvalid(s, valid){
    var returnString = "";
    for (i = 0; i < s.length; i++){
       if (valid.indexOf(s.charAt(i)) == (-1)){
		 returnString += s.charAt(i);
		}
    }
    return returnString;
}


function findNameInvalid(s){
	return findInvalid(s,nameValid);
}

function findRazonInvalid(s){
  return findInvalid(s,razonValid);
}

function findCaracterInvalid(s){
   var returnString = "";
   for (i = 0; i < s.length; i++){
      if (invalid.indexOf(s.charAt(i)) != (-1)){
	      returnString += s.charAt(i);
		}
    }
    return returnString;
}

//numeros solamente
function findNumberInvalid(s){
   return findInvalid(s,"0123456789")
}

//número telefónico
function findPhoneNumberInvalid(s){
   return findInvalid(s,"0123456789()- /")
}

//numeros y -
function findNumberTributarioInvalid(s){
   return findInvalid(s,"-0123456789")
}

//para únicamente números, letras y _
function findIdentifyInvalid(s){
   return findInvalid(s,identify)
}


function findFloatInvalid(s){
   return findInvalid(s,"0123456789.")

}

function findBankAccountInvalid(s){
   return findInvalid(s,bankAccountValid)
}

function isValid(s, valid){
  for (k = 0; k < s.length; k++){
      if (valid.indexOf(s.charAt(k)) == (-1))
		  return false;
   }
  return true;
}

function isCaracterInvalid(s){
   return (isInvalid(s,'"'));
}


function isRazonValid(s){
   return (isValid(s,razonValid));
}

function isNameValid(s){
   return (isValid(s,nameValid));
}

function isPhoneNumberValid(s){
   return isValid(s,telefonValid);
}

function isNumberValid(s){
   return isValid(s,"0123456789");
}

function isNumberTributarioValid(s){
	return isValid(s,"01234569-");
}

function isFloatValid(s){
   return isValid(s,"0123456789.");
}


function isIdentifyValid(s){
   return isValid(s,identify);
}

function isBankAccountValid(s){
   return isValid(s,bankAccountValid);
}


function isFormatIdTributario(s){
  var c;
  if (s.length != 13)
     return false;
  for(i=0; i<s.length; i++){
	  c =s.charAt(i);
      if((i!=2)&&(i!=11)){
	     if (!(isValid(c,"0123456789")))
		     return false;}
   else {
		  if (!(c=="-")){
			  return false}
		 }
  }
return true;
}

// ===================================================================
// Author: Matt Kruse <matt@mattkruse.com>
// WWW: http://www.mattkruse.com/
//
// NOTICE: You may use this code for any purpose, commercial or
// private, without any further permission from the author. You may
// remove this notice from your final code if you wish, however it is
// appreciated by the author if at least my web site address is kept.
//
// You may *NOT* re-distribute this code in any way except through its
// use. That means, you can include it in your product, or your web
// site, or any other form where the code is actually being used. You
// may not put the plain javascript up on your site for download or
// include it in your javascript libraries for download.
// If you wish to share this code with others, please just point them
// to the URL instead.
// Please DO NOT link directly to my .js files from your site. Copy
// the files to your server and use them there. Thank you.
// ===================================================================

// HISTORY
// ------------------------------------------------------------------
// May 17, 2003: Fixed bug in parseDate() for dates <1970
// March 11, 2003: Added parseDate() function
// March 11, 2003: Added "NNN" formatting option. Doesn't match up
//                 perfectly with SimpleDateFormat formats, but
//                 backwards-compatability was required.

// ------------------------------------------------------------------
// These functions use the same 'format' strings as the
// java.text.SimpleDateFormat class, with minor exceptions.
// The format string consists of the following abbreviations:
//
// Field        | Full Form          | Short Form
// -------------+--------------------+-----------------------
// Year         | yyyy (4 digits)    | yy (2 digits), y (2 or 4 digits)
// Month        | MMM (name or abbr.)| MM (2 digits), M (1 or 2 digits)
//              | NNN (abbr.)        |
// Day of Month | dd (2 digits)      | d (1 or 2 digits)
// Day of Week  | EE (name)          | E (abbr)
// Hour (1-12)  | hh (2 digits)      | h (1 or 2 digits)
// Hour (0-23)  | HH (2 digits)      | H (1 or 2 digits)
// Hour (0-11)  | KK (2 digits)      | K (1 or 2 digits)
// Hour (1-24)  | kk (2 digits)      | k (1 or 2 digits)
// Minute       | mm (2 digits)      | m (1 or 2 digits)
// Second       | ss (2 digits)      | s (1 or 2 digits)
// AM/PM        | a                  |
//
// NOTE THE DIFFERENCE BETWEEN MM and mm! Month=MM, not mm!
// Examples:
//  "MMM d, y" matches: January 01, 2000
//                      Dec 1, 1900
//                      Nov 20, 00
//  "M/d/yy"   matches: 01/20/00
//                      9/2/00
//  "MMM dd, yyyy hh:mm:ssa" matches: "January 01, 2000 12:30:45AM"
// ------------------------------------------------------------------

var MONTH_NAMES=new Array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic');
var DAY_NAMES=new Array('Domingo','Lunes','Martes','Mi' + e_acute + 'rcoles','Jueves','Viernes','S' + a_acute + 'bado','Dom','Lun','Mar','Mi' + e_acute,'Jue','Vie','S' + a_acute + 'b');
function LZ(x) {return(x<0||x>9?"":"0")+x}

// ------------------------------------------------------------------
// isDate ( date_string, format_string )
// Returns true if date string matches format of format string and
// is a valid date. Else returns false.
// It is recommended that you trim whitespace around the value before
// passing it to this function, as whitespace is NOT ignored!
// ------------------------------------------------------------------
function isDate(val,format) {
	var date=getDateFromFormat(val,format);
	if (date==0) { return false; }
	return true;
	}

// -------------------------------------------------------------------
// compareDates(date1,date1format,date2,date2format)
//   Compare two date strings to see which is greater.
//   Returns:
//   1 if date1 is greater than date2
//   0 if date2 is greater than date1 of if they are the same
//  -1 if either of the dates is in an invalid format
// -------------------------------------------------------------------
function compareDates(date1,dateformat1,date2,dateformat2) {
	var d1=getDateFromFormat(date1,dateformat1);
	var d2=getDateFromFormat(date2,dateformat2);
	if (d1==0 || d2==0) {
		return -1;
		}
	else if (d1 < d2) {
		return 1;
		}
	return 0;
}

// -------------------------------------------------------------------
// compareDatesOk(date1,date1format,date2,date2format) //sigue la convencion usada en TableCtrl.js
//   Compare two date strings to see which is greater.
//   Returns:
//   1 if date1 is greater than date2 or if date2 is in an invalid format
//   0 if date1 and date2 are the same or if both dates are in an invalid format
//  -1 if date2 is greater than date1 or if date1 is in an invalid format
// -------------------------------------------------------------------
function compareDatesOk(date1,dateformat1,date2,dateformat2) {
	var d1=getDateFromFormat(date1,dateformat1);
	var d2=getDateFromFormat(date2,dateformat2);
	if (d1==0 && d2==0) {
		return 0;
	} else if (d1==0) {
		return -1;
	} else if (d2==0) {
		return 1;
	} else if (d1 < d2) {
		return -1;
	} else if (d1 > d2) {
		return 1;
	} else {
		return 0;
	}
}

// ------------------------------------------------------------------
// formatDate (date_object, format)
// Returns a date in the output format specified.
// The format string uses the same abbreviations as in getDateFromFormat()
// ------------------------------------------------------------------
function formatDate(date,format) {
	format=format+"";

	//if no am/pm assume time format is 24 hours based
	if(format.indexOf("a")==-1) {
		format=format.replace("hh", "HH");
	}
	
	var result="";
	var i_format=0;
	var c="";
	var token="";
	var y=date.getYear()+"";
	var M=date.getMonth()+1;
	var d=date.getDate();
	var E=date.getDay();
	var H=date.getHours();
	var m=date.getMinutes();
	var s=date.getSeconds();
	var yyyy,yy,MMM,MM,dd,hh,h,mm,ss,ampm,HH,H,KK,K,kk,k;
	// Convert real date parts into formatted versions
	var value=new Object();
	if (y.length < 4) {y=""+(y-0+1900);}
	value["y"]=""+y;
	value["yyyy"]=y;
	value["yy"]=y.substring(2,4);
	value["M"]=M;
	value["MM"]=LZ(M);
	value["MMM"]=MONTH_NAMES[M-1];
	value["NNN"]=MONTH_NAMES[M+11];
	value["d"]=d;
	value["dd"]=LZ(d);
	value["E"]=DAY_NAMES[E+7];
	value["EE"]=DAY_NAMES[E];
	value["H"]=H;
	value["HH"]=LZ(H);
	if (H==0){value["h"]=12;}
	else if (H>12){value["h"]=H-12;}
	else {value["h"]=H;}
	value["hh"]=LZ(value["h"]);
	if (H>11){value["K"]=H-12;} else {value["K"]=H;}
	value["k"]=H+1;
	value["KK"]=LZ(value["K"]);
	value["kk"]=LZ(value["k"]);
	if (H > 11) { value["a"]="PM"; }
	else { value["a"]="AM"; }
	value["m"]=m;
	value["mm"]=LZ(m);
	value["s"]=s;
	value["ss"]=LZ(s);
	while (i_format < format.length) {
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
			}
		if (value[token] != null) { result=result + value[token]; }
		else { result=result + token; }
		}
	return result;
	}

// ------------------------------------------------------------------
// Utility functions for parsing in getDateFromFormat()
// ------------------------------------------------------------------
function _isInteger(val) {
	var digits="1234567890";
	for (var i=0; i < val.length; i++) {
		if (digits.indexOf(val.charAt(i))==-1) { return false; }
		}
	return true;
	}
function _getInt(str,i,minlength,maxlength) {
	for (var x=maxlength; x>=minlength; x--) {
		var token=str.substring(i,i+x);
		if (token.length < minlength) { return null; }
		if (_isInteger(token)) { return token; }
		}
	return null;
	}

// ------------------------------------------------------------------
// getDateFromFormat( date_string , format_string )
//
// This function takes a date string and a format string. It matches
// If the date string matches the format string, it returns the
// getTime() of the date. If it does not match, it returns 0.
// ------------------------------------------------------------------
function getDateFromFormat(val,format) {
	val=val+"";
	format=format+"";

	//if no am/pm assume time format is 24 hours based
	if(format.indexOf("a")==-1) {
		format=format.replace("hh", "HH");
	}
		
	var i_val=0;
	var i_format=0;
	var c="";
	var token="";
	var token2="";
	var x,y;
	var now=new Date();
	var year=now.getYear();
	var month=1;
	var date=1;
	var hh=0;
	var mm=0;
	var ss=0;
	var ampm="";

	while (i_format < format.length) {
		// Get next token from format string
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
			}
		// Extract contents of value based on format token
		if (token=="yyyy" || token=="yy" || token=="y") {
			if (token=="yyyy") { x=4;y=4; }
			if (token=="yy")   { x=2;y=2; }
			if (token=="y")    { x=2;y=4; }
			year=_getInt(val,i_val,x,y);
			if (year==null) { return 0; }
			i_val += year.length;
			if (year.length==2) {
				if (year > 70) { year=1900+(year-0); }
				else { year=2000+(year-0); }
				}
			}
		else if (token=="MMM"||token=="NNN"){
			month=0;
			for (var i=0; i<MONTH_NAMES.length; i++) {
				var month_name=MONTH_NAMES[i];
				if (val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()) {
					if (token=="MMM"||(token=="NNN"&&i>11)) {
						month=i+1;
						if (month>12) { month -= 12; }
						i_val += month_name.length;
						break;
						}
					}
				}
			if ((month < 1)||(month>12)){return 0;}
			}
		else if (token=="EE"||token=="E"){
			for (var i=0; i<DAY_NAMES.length; i++) {
				var day_name=DAY_NAMES[i];
				if (val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()) {
					i_val += day_name.length;
					break;
					}
				}
			}
		else if (token=="MM"||token=="M") {
			month=_getInt(val,i_val,token.length,2);
			if(month==null||(month<1)||(month>12)){return 0;}
			i_val+=month.length;}
		else if (token=="dd"||token=="d") {
			date=_getInt(val,i_val,token.length,2);
			if(date==null||(date<1)||(date>31)){return 0;}
			i_val+=date.length;}
		else if (token=="hh"||token=="h") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>12)){return 0;}
			i_val+=hh.length;}
		else if (token=="HH"||token=="H") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>23)){return 0;}
			i_val+=hh.length;}
		else if (token=="KK"||token=="K") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>11)){return 0;}
			i_val+=hh.length;}
		else if (token=="kk"||token=="k") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>24)){return 0;}
			i_val+=hh.length;hh--;}
		else if (token=="mm"||token=="m") {
			mm=_getInt(val,i_val,token.length,2);
			if(mm==null||(mm<0)||(mm>59)){return 0;}
			i_val+=mm.length;}
		else if (token=="ss"||token=="s") {
			ss=_getInt(val,i_val,token.length,2);
			if(ss==null||(ss<0)||(ss>59)){return 0;}
			i_val+=ss.length;}
		else if (token=="a") {
			if (val.substring(i_val,i_val+2).toLowerCase()=="am") {ampm="AM";}
			else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") {ampm="PM";}
			else {return 0;}
			i_val+=2;}
		else {
			if (val.substring(i_val,i_val+token.length)!=token) {return 0;}
			else {i_val+=token.length;}
			}
		}
	// If there are any trailing characters left in the value, it doesn't match
	if (i_val != val.length) { return 0; }
	// Is date valid for month?
	if (month==2) {
		// Check for leap year
		if ( ( (year%4==0)&&(year%100 != 0) ) || (year%400==0) ) { // leap year
			if (date > 29){ return 0; }
			}
		else { if (date > 28) { return 0; } }
		}
	if ((month==4)||(month==6)||(month==9)||(month==11)) {
		if (date > 30) { return 0; }
		}
	// Correct hours value
	if (hh<12 && ampm=="PM") { hh=hh-0+12; }
	else if (hh>11 && ampm=="AM") { hh-=12; }
	var newdate=new Date(year,month-1,date,hh,mm,ss);
	return newdate.getTime();
	}

// ------------------------------------------------------------------
// parseDate( date_string [, prefer_euro_format] )
//
// This function takes a date string and tries to match it to a
// number of possible date formats to get the value. It will try to
// match against the following international formats, in this order:
// y-M-d   MMM d, y   MMM d,y   y-MMM-d   d-MMM-y  MMM d
// M/d/y   M-d-y      M.d.y     MMM-d     M/d      M-d
// d/M/y   d-M-y      d.M.y     d-MMM     d/M      d-M
// A second argument may be passed to instruct the method to search
// for formats like d/M/y (european format) before M/d/y (American).
// Returns a Date object or null if no patterns match.
// ------------------------------------------------------------------
function parseDate(val) {
	var preferEuro=(arguments.length==2)?arguments[1]:false;
	generalFormats=new Array('y-M-d','MMM d, y','MMM d,y','y-MMM-d','d-MMM-y','MMM d');
	monthFirst=new Array('M/d/y','M-d-y','M.d.y','MMM-d','M/d','M-d');
	dateFirst =new Array('d/M/y','d-M-y','d.M.y','d-MMM','d/M','d-M');
	var checkList=new Array('generalFormats',preferEuro?'dateFirst':'monthFirst',preferEuro?'monthFirst':'dateFirst');
	var d=null;
	for (var i=0; i<checkList.length; i++) {
		var l=window[checkList[i]];
		for (var j=0; j<l.length; j++) {
			d=getDateFromFormat(val,l[j]);
			if (d!=0) { return new Date(d); }
			}
		}
	return null;
	}

function formatNumber(number, format, patternSeparator, patternDecpoint, leftNegativeSymbol, rightNegativeSymbol, print) {  // use: formatNumber(number, "format")
    if (print) document.write("formatNumber(" + number + ", \"" + format + "\")<br>");

    if (number - 0 != number) return NaN;  // if number is NaN return null
    var useSeparator = format.indexOf(separator) != -1;  // use separators in number
    var usePercent = format.indexOf(percent) != -1;  // convert output to percentage
    var useCurrency = format.indexOf(currencySymbol) != -1;  // use currency format
    var isNegative = (number < 0);

    number = Math.abs (number);
    if (usePercent) number *= 100;
    format = strip(format, separator + percent + currencySymbol);  // remove key characters
    number = "" + number;  // convert number input to string
     // split input value into LHS and RHS using decpoint as divider
    var dec = number.indexOf(decpoint) != -1;
    var nleftEnd = (dec) ? number.substring(0, number.indexOf(".")) : number;
    var nrightEnd = (dec) ? number.substring(number.indexOf(".") + 1) : "";

     // split format string into LHS and RHS using decpoint as divider
    dec = format.indexOf(decpoint) != -1;
    var sleftEnd = (dec) ? format.substring(0, format.indexOf(".")) : format;
    var srightEnd = (dec) ? format.substring(format.indexOf(".") + 1) : "";
     // adjust decimal places by cropping or adding zeros to LHS of number
    if (srightEnd.length < nrightEnd.length) {
      var nextChar = nrightEnd.charAt(srightEnd.length) - 0;
      nrightEnd = nrightEnd.substring(0, srightEnd.length);
      if (nextChar >= 5) nrightEnd = "" + ((nrightEnd - 0) + 1);  // round up
 // patch provided by Patti Marcoux 1999/08/06
      while (srightEnd.length > nrightEnd.length) {
        nrightEnd = "0" + nrightEnd;
      }

      if (srightEnd.length < nrightEnd.length) {
        nrightEnd = nrightEnd.substring(1);
        nleftEnd = (nleftEnd - 0) + 1;
      }
    } else {
      for (var i=nrightEnd.length; srightEnd.length > nrightEnd.length; i++) {
        if (srightEnd.charAt(i) == "0") nrightEnd += "0";  // append zero to RHS of number
        else break;
      }
    }

     // adjust leading zeros
    sleftEnd = strip(sleftEnd, "#");  // remove hashes from LHS of format
    while (sleftEnd.length > nleftEnd.length) {
      nleftEnd = "0" + nleftEnd;  // prepend zero to LHS of number
    }
    if (useSeparator) nleftEnd = separate(nleftEnd, patternSeparator);  // add separator
    var output = nleftEnd + ((nrightEnd != "") ? patternDecpoint + nrightEnd : "");  // combine parts
    output = ((useCurrency) ? currencySymbol : "") + output + ((usePercent) ? percent : "");
    if (isNegative) {
      // patch suggested by Tom Denn 25/4/2001
      output = leftNegativeSymbol + output + rightNegativeSymbol;
    }
    return output;
}

function parseStringNumber(string, format, patternSeparator, patternDecpoint, leftNegativeSymbol, rightNegativeSymbol, print) {  // use: parseStringNumber(string, format)
    if (print) document.write("parseStringNumber(" + string + ", \"" + format + "\")<br>");

		if ((string == null)||(string == "")) return NaN;  // if string is NaN return NaN
    var useSeparator = format.indexOf(separator) != -1;  // use separators in number
    var hasSeparator = string.indexOf(patternSeparator) != -1;  // use separators in number

    var usePercent = format.indexOf(percent) != -1;  // convert output to percentage
    var hasPercent = string.indexOf(percent) != -1;  // convert output to percentage

    var useCurrency = format.indexOf(currencySymbol) != -1;  // use currency format
    var hasCurrency = string.indexOf(currencySymbol) != -1;  // use currency format

	if((usePercent) && (!(hasPercent))){
	  alert(jsNoPercentSymbol + ": " + percent);
	  return NaN;
	}

	if((useCurrency) && (!(hasCurrency))){
	  alert(jsNoCurrencySymbol+ ": " + currencySymbol + string);
	  return NaN;
	}
	var number = 0;
	var isNegative = false;

	string = Trim(string);

	if((leftNegativeSymbol.length == 0)&&(rightNegativeSymbol.length == 0)){
          isNegative = false;
	}
        else if(leftNegativeSymbol.length == 0){
          isNegative = (string.indexOf(rightNegativeSymbol) == (string.length-rightNegativeSymbol.length));
	}
        else if(rightNegativeSymbol.length == 0){
          isNegative = (string.indexOf(leftNegativeSymbol) == 0);
        }
        else{
          isNegative = (string.indexOf(leftNegativeSymbol) == 0) &&
                       (string.indexOf(rightNegativeSymbol) == (string.length-rightNegativeSymbol.length));
        }

       if((leftNegativeSymbol.length > 0)&&(string.indexOf(leftNegativeSymbol) > 0)){
	    alert (jsLeftNegativeSymbolAtBeginning + string);
            return NaN;
       }

       if((rightNegativeSymbol.length > 0)&&(string.indexOf(rightNegativeSymbol)!=-1)&&(string.indexOf(rightNegativeSymbol) != (string.length-rightNegativeSymbol.length))){
	    alert (jsRightNegativeSymbolAtEnd);
            return NaN;
       }

    if(isNegative){
      string = strip(string, rightNegativeSymbol + leftNegativeSymbol); //remove negative symbols
    }

    string = strip(string, percent + currencySymbol);  // remove key characters

    if(isNaN(string.charAt(0))){
      return NaN;
    }
     // split input value into LHS and RHS using decpoint as divider
    var dec = string.indexOf(patternDecpoint) != -1;
    var nleftEnd = (dec) ? string.substring(0, string.indexOf(patternDecpoint)) : string;
    var nrightEnd = (dec) ? string.substring(string.indexOf(patternDecpoint) + 1) : "";

     // split format string into LHS and RHS using decpoint as divider
    dec = format.indexOf(decpoint) != -1;
    var sleftEnd = (dec) ? format.substring(0, format.indexOf(".")) : format;
    var srightEnd = (dec) ? format.substring(format.indexOf(".") + 1) : "";

    if(hasSeparator){ //Verify correct place of thousand separator
      var thousandCount = 0;
      for (var i=(nleftEnd.length-1); i > 0; i--){
        if(thousandCount != 3){
          if(isDigit(nleftEnd.charAt(i))){
            thousandCount++;
          }
          else{
            alert(jsIncorrectThousandSeparator);
            return NaN;
          }
        }
        else{
          if (nleftEnd.charAt(i) == patternSeparator){
            thousandCount = 0;
          }
          else{
            alert(jsIncorrectThousandSeparator);
            return NaN;
          }
        }
      }
    }
	nleftEnd = strip(nleftEnd, patternSeparator); //Remove separator from integer part

	string = nleftEnd + decpoint + nrightEnd;

	if(isNegative){
	  string = "-" + string;
	}
	output = parseFloat(string);
	/*
    // adjust decimal places by cropping or adding zeros to LHS of number
    if (srightEnd.length < nrightEnd.length) {
      var nextChar = nrightEnd.charAt(srightEnd.length) - 0;
      nrightEnd = nrightEnd.substring(0, srightEnd.length);
      if (nextChar >= 5) nrightEnd = "" + ((nrightEnd - 0) + 1);  // round up

 // patch provided by Patti Marcoux 1999/08/06
      while (srightEnd.length > nrightEnd.length) {
        nrightEnd = "0" + nrightEnd;
      }

      if (srightEnd.length < nrightEnd.length) {
        nrightEnd = nrightEnd.substring(1);
        nleftEnd = (nleftEnd - 0) + 1;
      }
    } else {
      for (var i=nrightEnd.length; srightEnd.length > nrightEnd.length; i++) {
        if (srightEnd.charAt(i) == "0") nrightEnd += "0";  // append zero to RHS of number
        else break;
      }
    }

     // adjust leading zeros
    sleftEnd = strip(sleftEnd, "#");  // remove hashes from LHS of format
    while (sleftEnd.length > nleftEnd.length) {
      nleftEnd = "0" + nleftEnd;  // prepend zero to LHS of number
    }

    if (useSeparator) nleftEnd = separate(nleftEnd, separator);  // add separator
    var output = nleftEnd + ((nrightEnd != "") ? "." + nrightEnd : "");  // combine parts
    output = ((useCurrency) ? currency : "") + output + ((usePercent) ? percent : "");
    if (isNegative) {
      // patch suggested by Tom Denn 25/4/2001
      output = (useCurrency) ? "(" + output + ")" : "-" + output;
    }*/

    return output;
}

function Trim(TRIM_VALUE){
	if((TRIM_VALUE == null)||(TRIM_VALUE.length < 1)){
		return"";
	}
	TRIM_VALUE = RTrim(TRIM_VALUE);
	TRIM_VALUE = LTrim(TRIM_VALUE);

	return TRIM_VALUE;
} //End Function

function RTrim(VALUE){
	var w_space = 32;
	var w_other_space = 160;
	var w_tab = 9;
	var w_nl = 10;
	var w_cr = 13;
	var v_length = VALUE.length;
	var strTemp = "";
	if(v_length < 0){
		return"";
	}
	var iTemp = v_length -1;

	while(iTemp > -1){
		if(VALUE.charCodeAt(iTemp) == w_space || VALUE.charCodeAt(iTemp) == w_other_space || VALUE.charCodeAt(iTemp) == w_tab || VALUE.charCodeAt(iTemp) == w_nl || VALUE.charCodeAt(iTemp) == w_cr){
		} else{
			strTemp = VALUE.substring(0,iTemp +1);
			break;
		}
		iTemp = iTemp-1;
	} //End While
	return strTemp;
} //End Function

function LTrim(VALUE){
	var w_space = 32;
	var w_other_space = 160;
	var w_tab = 9;
	var w_nl = 10;
	var w_cr = 13;

	if(v_length < 1){
		return"";
	}
	var v_length = VALUE.length;
	var strTemp = "";

	var iTemp = 0;

	while(iTemp < v_length){
		if(VALUE.charCodeAt(iTemp) == w_space || VALUE.charCodeAt(iTemp) == w_other_space || VALUE.charCodeAt(iTemp) == w_tab || VALUE.charCodeAt(iTemp) == w_nl || VALUE.charCodeAt(iTemp) == w_cr){
		} else{
			strTemp = VALUE.substring(iTemp,v_length);
			break;
		}
		iTemp = iTemp + 1;
	} //End While
  	return strTemp;
} //End Function


function strip(input, chars) {  // strip all characters in 'chars' from input
    var output = "";  // initialise output string
    for (var i=0; i < input.length; i++)
      if (chars.indexOf(input.charAt(i)) == -1)
        output += input.charAt(i);
    return output;
}

function getDecimalSymbols(decimalDigits){
    var output = "";  // initialise output string
    for (var i=0; i < decimalDigits; i++)
      output += "0";
    return output;
}

function getCurrencyPatternString(){
  return (currencySymbol + "###,###." + getDecimalSymbols(currencyDecimalDigits));
}

function getNumberPatternString(){
  return ("###,###." + getDecimalSymbols(numberDecimalDigits));
}

function getNumberPatternStringFixedDecimals(fixedDecimals){
	return ("###,###." + getDecimalSymbols(fixedDecimals));
}

function getCurrencyFormatString(neg){
	if(typeof(neg)=="undefined")
		neg=false;
	return ((neg? currencyLeftNegativeSymbol : "") + currencySymbol + "###" + currencyPatternSeparator + "###" +
		currencyPatternDecpoint + getDecimalSymbols(currencyDecimalDigits) + (neg? currencyRightNegativeSymbol : ""));
}

function getNumberFormatString(neg){
	if(typeof(neg)=="undefined")
		neg=false;
	return ((neg? numberLeftNegativeSymbol : "") + "###" + numberPatternSeparator+ "###" +
		numberPatternDecpoint + getDecimalSymbols(numberDecimalDigits) + (neg? numberRightNegativeSymbol : ""));
}

function separate(input, separator) {  // format input using 'separator' to mark 000's
    input = "" + input;
    var output = "";  // initialise output string
    for (var i=0; i < input.length; i++) {
      if (i != 0 && (input.length - i) % 3 == 0) output += separator;
      output += input.charAt(i);
    }
    return output;
}

function convertValueToFormattedString(inputValue, dataType, customDatePattern){
    if(dataType == "number"){
	  return formatNumber(inputValue, getNumberPatternString(), numberPatternSeparator, numberPatternDecpoint, numberLeftNegativeSymbol, numberRightNegativeSymbol);
	}
	else if (dataType == "plainnumber"){
		return formatNumber(inputValue, getNumberPatternString(), '', numberPatternDecpoint, numberLeftNegativeSymbol, numberRightNegativeSymbol);
	}
	else if (dataType == "plainpercentage"){
		return formatNumber(inputValue, getNumberPatternStringFixedDecimals(2), '', numberPatternDecpoint, numberLeftNegativeSymbol, numberRightNegativeSymbol);
	}
	else if (dataType == "currency"){
	  return formatNumber(inputValue, getCurrencyPatternString(), currencyPatternSeparator, currencyPatternDecpoint, currencyLeftNegativeSymbol, currencyRightNegativeSymbol);
	}
	else if (dataType == "customDate"){
          value = new Date(inputValue);
	  return formatDate(value, customDatePattern);
	}
	else if (dataType == "date"){
          value = new Date(inputValue);
	  return formatDate(value, dateFormatPattern + " " + timeFormatPattern);
	}
        else if (dataType == "dateOnly"){
          value = new Date(inputValue);
          return formatDate(value, dateFormatPattern);
        }
        else if (dataType == "time"){
          value = new Date(inputValue);
          return formatDate(value, timeFormatPattern);
        }
}

function convertFormattedStringToValue(formattedString, dataType, datesToMilliseconds, customDatePattern){
	if(typeof(datesToMilliseconds)=="undefined")
		datesToMilliseconds=false;
    if(dataType == "number"){
	  return parseStringNumber(formattedString, getNumberPatternString(), numberPatternSeparator, numberPatternDecpoint, numberLeftNegativeSymbol, numberRightNegativeSymbol);
	}
	else if (dataType == "currency"){
	  return parseStringNumber(formattedString, getCurrencyPatternString(), currencyPatternSeparator, currencyPatternDecpoint, currencyLeftNegativeSymbol, currencyRightNegativeSymbol);
	}
	else if (dataType == "customDate"){
		if(datesToMilliseconds) {
			return getDateFromFormat(Trim(formattedString), customDatePattern);
		} else {
          return Trim(formattedString); //Returns the same formatted string because it must be converted to java.util.Date anyway
		}
	}
	else if (dataType == "someDate"){
		if(isDataFormatValid(formattedString, "dateOnly"))
			return convertFormattedStringToValue(formattedString, "dateOnly", datesToMilliseconds);
		else
			return convertFormattedStringToValue(formattedString, "date", datesToMilliseconds);
	}
	else if (dataType == "date"){
		if(datesToMilliseconds) {
			return getDateFromFormat(Trim(formattedString), dateFormatPattern + " " + timeFormatPattern);
		} else {
          return Trim(formattedString); //Returns the same formatted string because it must be converted to java.util.Date anyway
		}
	}
	else if (dataType == "dateOnly"){
		if(datesToMilliseconds) {
			return getDateFromFormat(Trim(formattedString), dateFormatPattern);
		} else {
          return Trim(formattedString);
		}
    }
    else if (dataType == "time"){
		if(datesToMilliseconds) {
			return getDateFromFormat(Trim(formattedString), timeFormatPattern);
		} else {
          return Trim(formattedString);
		}
    }
}

function isDataFormatValid(formattedString, dataType, customDatePattern) {
    var valid = false;
	if(dataType == "number"){
	  result = parseStringNumber(formattedString, getNumberPatternString(), numberPatternSeparator, numberPatternDecpoint, numberLeftNegativeSymbol, numberRightNegativeSymbol);
	  if(isNaN(result)){
	    valid = false;
	  }
	  else {
	    valid = true;
	  }
	}
	if(dataType == "currency"){
	  result = parseStringNumber(formattedString, getCurrencyPatternString(), currencyPatternSeparator, currencyPatternDecpoint, currencyLeftNegativeSymbol, currencyRightNegativeSymbol);
	  if(isNaN(result)){
	    valid = false;
	  }
	  else {
        valid = true;
	  }
	}
	else if (dataType == "customDate"){
	  valid = isDate(Trim(formattedString), customDatePattern);
	}
	else if (dataType == "someDate"){
	  valid = isDataFormatValid(formattedString, "date") || isDataFormatValid(formattedString, "dateOnly");
	}
	else if (dataType == "date"){
	  valid = isDate(Trim(formattedString), dateFormatPattern + " " + timeFormatPattern);
        }
        /*Igual que "date" pero sin considerar la hora. Lo correcto sería llamarle "timeStamp" al "date", pero
          implica cambiar varias paginas*/
        else if (dataType == "dateOnly"){
          valid = isDate(Trim(formattedString), dateFormatPattern);
        }
        else if (dataType == "time"){
          valid = isDate(Trim(formattedString), timeFormatPattern);
        }
	return valid;
}

// comodines
// ?     letra
// #     digito
// *     cualquiera
// otro  caracter exacto
function acceptsPattern(str, pattern, errors) {
	var nameForChar=function(chr) {
		if(chr=='?') {
			return 'letra';
		} else if(chr=='#') {
			return 'digito';
		} else if(chr=='*') {
			return 'cualquiera';
		} else {
			return chr;
		}
	};

	for(var i=0; i<Math.min(pattern.length, str.length); i++) {
		if(pattern.charAt(i)=='?') {
			if((str.charCodeAt(i)<'A'.charCodeAt(0)  || str.charCodeAt(i)>'Z'.charCodeAt(0)) && (str.charCodeAt(i)<'a'.charCodeAt(0)  || str.charCodeAt(i)>'z'.charCodeAt(0))) {
				errors[errors.length]="Se esperaba: '" + nameForChar('?') + "', se encontro: '" + str.charAt(i) + "'\nCaracter " + (i+1);
			}
		} else if(pattern.charAt(i)=='#') {
			if(str.charCodeAt(i)<'0'.charCodeAt(0)  || str.charCodeAt(i)>'9'.charCodeAt(0)) {
				errors[errors.length]="Se esperaba: '" + nameForChar('#') + "', se encontro: '" + str.charAt(i) + "'\nCaracter " + (i+1);
			}
		} else if(pattern.charAt(i)=='*') {
			//everything allowed
		} else {
			if(str.charAt(i)!=pattern.charAt(i)) {
				errors[errors.length]="Se esperaba: '" + pattern.charAt(i) + "', se encontro: '" + str.charAt(i) + "'\nCaracter " + (i+1);
			}
		}
	}

	if(str.length<pattern.length) {
		errors[errors.length]="Se esperaba '" + nameForChar(pattern.charAt(str.length)) + "', se encontro: 'fin de cadena'\nCaracter " + (str.length+1);
	} else 	if(str.length>pattern.length) {
		errors[errors.length]="Se esperaba 'fin de cadena', se encontro: '" + nameForChar(str.charAt(pattern.length)) + "'\nCaracter " + (pattern.length+1);
	}

	if(errors.length>0)
		return false;
	return true;
}

function checkMaxDecimalsQty(number, qty){
	if (number.indexOf(decpoint)==-1) return true;
	parts = number.split(decpoint);
	return (parts[1].length<=qty);
}
function emailcheck(str) {

		var at="@";
		var dot=".";
		var lat=str.indexOf(at);
		var lstr=str.length;
		var ldot=str.indexOf(dot);
		if (str.indexOf(at)==-1){
		   return false;
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   return false;
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    return false;
		}

		 if (str.indexOf(at,(lat+1))!=-1){
		    return false;
		 }

		 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    return false;
		 }

		 if (str.indexOf(dot,(lat+2))==-1){
		    return false;
		 }
		
		 if (str.indexOf(" ")!=-1){
		    return false;
		 }

 		 return true;					
}

var imgRe = /^.+\.(jpg|jpeg|gif|png)$/i;

function imgCheck(pathField)
{
var path = pathField.value;
return (path.search(imgRe) != -1)
}
