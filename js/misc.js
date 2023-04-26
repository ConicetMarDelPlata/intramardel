// esto es para ordenar en la grilla
  // Estos separadores deberian ser seteados desde afuera, segun el formato predefinido del usuario
  var dateFormatPattern = "dd-MM-yyyy";
  var timeFormatPattern = "hh:mm:ss";
  var extendedTime = "hh:mm:ss";

  var currencyDecimalDigits = 2;
  var currencyPatternSeparator = ",";
  var currencyPatternDecpoint = ".";
  var currencySymbol = "$";
  var currencyLeftNegativeSymbol = "-";
  var currencyRightNegativeSymbol = "-";

  var numberDecimalDigits = 2;
  var numberPatternSeparator = ",";
  var numberPatternDecpoint = ".";
  var numberLeftNegativeSymbol = "-";
  var numberRightNegativeSymbol = "-";

  var jsNoPercentSymbol = "jsNoPercentSymbol";
  var jsNoCurrencySymbol = "jsNoCurrencySymbol";
  var jsLeftNegativeSymbolAtBeginning = "jsLeftNegativeSymbolAtBeginning";
  var jsRightNegativeSymbolAtEnd = "jsRightNegativeSymbolAtEnd";
  var jsIncorrectThousandSeparator = "jsIncorrectThousandSeparator";



String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}

function verificar(inForm){
	//if (verificarObligatorios(inForm))
	  if (verificarDatos(inForm))
	     inForm.submit(); 
}

function isEmpty(campo){
   return(campo.value == "");
}


function existSelectRadio(inForm){
   for (var i = 0; i < inForm.elements.length; i++)
     if (inForm.elements[i].type=="radio" && inForm.elements[i].checked !="0"){
	   return true;
	 }
 return false;
}

function getRadioButtonSelectedValue(ctrl)
{
    for(i=0;i<ctrl.length;i++)
        if(ctrl[i].checked) return ctrl[i].value;
}


function existSelection(inForm, checkName){
	for (var i = 0; i < inForm.elements.length; i++) {
		if (inForm.elements[i].type=="checkbox"
                && inForm.elements[i].name.substring(0, checkName.length)==checkName
                && inForm.elements[i].checked){
			return true;
		}
	}
	return false;
}

function existSelect(inForm, checkName){
   for (var i = 0; i < inForm.elements.length; i++)
     if (inForm.elements[i].type=="checkbox" && inForm.elements[i].name.substring(0, checkName.length)==checkName && inForm.elements[i].checked){
	   return true;
	 }
 return false;
}

/*Verifica que haya al menos un checkbos seleccionado*/
/*Realiza el submit del formulario*/
/*checkName = comienzo del nombre de los checkbox a verificar*/
function submitDelete(inForm, checkName){
	if (existSelect(inForm, checkName) == false) {		
	//if (existOneSelect(inForm, checkName) == false) {
		alert("Seleccione al menos un registro para eliminar.");
	}
	else {
		if (confirm("Está seguro que desea eliminar los registros seleccionados?")) {
			inForm.submit();
		}		
	}
}

function submitStateChange(inForm, checkName){
	if (existSelect(inForm, checkName) == false) {		
	//if (existOneSelect(inForm, checkName) == false) {
		alert("Seleccione al menos un registro para cambiar el estado.");
	}
	else {
		if (confirm("Sólo se cambiará el estado de aquellos registros en estado 'Pendiente'. Está seguro que desea cambiar el estado de los registros seleccionados?")) {
			inForm.submit();
		}		
	}
}



/*Verifica que haya un y solo un checkbox seleccionado*/
/*Si lo encuentra devuelve true*/
/*Si no devuelve false*/
function existOneSelect(inForm, checkName){
	var cant = 0;
    for (var i = 0; i < inForm.elements.length; i++){
		if ((inForm.elements[i].type=="checkbox") && 
			(inForm.elements[i].name.substring(0, checkName.length)==checkName) && 
			(inForm.elements[i].checked))
			{
			cant = cant + 1; 
		}
	}
	return (cant == 1);
}

/*Verifica que haya un y solo un checkbox seleccionado*/
/*Realiza el submit del formulario*/
/*checkName = comienzo del nombre de los checkbox a verificar*/
function submitUpdate(inForm, checkName) {
	var val, exist;
	exist = existOneSelect(inForm, checkName);
	if (exist == false) {
      alert("Seleccione un y sólo un registro para modificar.");	
	}
	else{	
		inForm.submit();
	}
}



/*Verifica que haya un y solo un checkbox seleccionado*/
/*Realiza el submit del formulario*/
/*checkName = comienzo del nombre de los checkbox a verificar*/
function submitQuery(inForm, checkName) {
	var val, exist;
	exist = existOneSelect(inForm, checkName);
	if (exist == false) {
      alert("Seleccione un y sólo un registro para consultar.");	
	}
	else{	
		inForm.submit();
	}
}

/*Funcion no optima para lo que se esta usando (hacer visible un div)
Quitando la primer linea sirve para togglear el estado del div
Si se quiere hacer visible un div utilizar mostrarCapa*/
function visibilidadCapa(id){
	ocultarCapa(id);
	ns4 = (document.layers)?true:false;
	ie4 = (document.all)?true:false;
	ng5 = (document.getElementById)?true:false;
	if(ns4){
		if (document.layers[id].visibility == 'show'){			
			document.layers[id].visibility = 'hide';
		} else{
			document.layers[id].visibility = 'show';			
		}
	}
	else if(ie4){
		if (document.all[id].style.visibility == "visible"){
			document.all[id].style.visibility ="hidden";
			document.all[id].style.display ="none";
		}else{
			document.all[id].style.visibility = "visible";
			document.all[id].style.display = "block";			
		}
	}
	else if(ng5){
		if (document.getElementById(id).style.visibility == 'hidden' || 
			document.getElementById(id).style.visibility == ''){		
			document.getElementById(id).style.display = 'block';
			document.getElementById(id).style.visibility = 'visible';
		}else{
			document.getElementById(id).style.display = 'none';
			document.getElementById(id).style.visibility = 'hidden';
		}
	}
	return false;
}


function ocultarCapa(id){
	ns4 = (document.layers)?true:false;
	ie4 = (document.all)?true:false;
	ng5 = (document.getElementById)?true:false;
	
	if(ns4){
		document.layers[id].visibility = 'hide';
	}
	else if(ie4){
		document.all[id].style.visibility ="hidden";
		document.all[id].style.display ="none";
	}
	else if(ng5){
		document.getElementById(id).style.display = 'none';
		document.getElementById(id).style.visibility = 'hidden';
	}
	return false;
}

function mostrarCapa(id){
	ocultarCapa(id);
	ns4 = (document.layers)?true:false;
	ie4 = (document.all)?true:false;
	ng5 = (document.getElementById)?true:false;
	if(ns4){
		document.layers[id].visibility = 'show';			
	}
	else if(ie4){
		document.all[id].style.visibility = "visible";
		document.all[id].style.display = "block";			
	}
	else if(ng5){
		document.getElementById(id).style.display = 'block';
		document.getElementById(id).style.visibility = 'visible';
	}
	return false;
}


//-------------------------------------------------------------------
// Trim functions
//   Returns string with whitespace trimmed
//-------------------------------------------------------------------
function LTrim(str){
	if (str==null){return null;}
	for(var i=0;str.charAt(i)==" ";i++);
	return str.substring(i,str.length);
	}
function RTrim(str){
	if (str==null){return null;}
	for(var i=str.length-1;str.charAt(i)==" ";i--);
	return str.substring(0,i+1);
	}
function Trim(str){return LTrim(RTrim(str));}
//Setea el alto de element (parametro) al porc (parametro) relativo al alto de la pantalla
//El script determina el alto ya que el cliente puede tener distintas resoluciones
function setHeight(element, porc)
{
  H=document.documentElement.clientHeight;
  H=H*porc/100;
  document.getElementById(element).style.height = H+'px';
}

function setWidth(element, porc)
{
  H=document.documentElement.clientWidth;
  H=H*porc/100;
  document.getElementById(element).style.width = H+'px';
}

//Hace foco en el primer control del formulario
function doFocusFirstControl(inForm) {
  for (var i=0; (i<inForm.elements.length); i++) {
	type = inForm.elements[i].type;
    if (!(type == "button") && !(type=="submit") && !(type=="hidden")){
	  inForm.elements[i].focus();
	  return;
	  }
     }
}

//Desabilita todos los controles de una pagina
function disableAll (inForm) {
  for (var i=0; (i<inForm.elements.length); i++) {
	type = inForm.elements[i].type;
    if (!(type == "button") && !(type=="submit") && !(type=="hidden")){
	  inForm.elements[i].disabled=true;
	  }
     }
}

/**
 * RoundBox support
 *
 * @copyright   (c) 2005 WebSprockets, LLC
 * @author      Ian Eure
 * @license     LGPL
 */
	
/**
 * Initialize a RoundBox
 *
 * @param   HTMLElement  box RoundBox element to initialize
 * @return  void
 */
function initBox(box)
{
	isOpera = (navigator.userAgent.indexOf("Opera") != -1);
	isIE = (!isOpera) && (navigator.appVersion.indexOf ("MSIE") != -1);

    var ul = document.createElement('div');
    //ul.className = 'corner ul';
    //var ur = document.createElement('div');
    //ur.className = 'corner ur';
    var ll = document.createElement('div');
    //ll.className = 'corner ll';
    //var lr = document.createElement('div');
    //lr.className = 'corner lr';
	if (isIE){
		ul.className = 'corner ulIE';
		ll.className = 'corner llIE';
	}
	else {
		ul.className = 'corner ulMozilla';
		ll.className = 'corner llMozilla';
	}
	
    box.appendChild(ul);
    //box.appendChild(ur);
    box.appendChild(ll);
    //box.appendChild(lr);
}
	
/**
 * Initialize all boxes on a page
 *
 * This initializes any div with a class of 'roundbox'
 *
 * @return  void
 */
function initBoxes()
{
    var boxen = document.getElementsByTagName('div');
    for (var i = 0; i < boxen.length; i++) {
        if (boxen[i].className == 'roundbox') {
            initBox(boxen[i]);
        }
    }
}


function exportToXls()
{

 return false;

}