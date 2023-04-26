// JavaScript Document
Number.prototype.formatMoney = function (c, d, t) {
  var n = this,
    c = isNaN((c = Math.abs(c))) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = parseInt((n = Math.abs(+n || 0).toFixed(c))) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
  return (
    s +
    (j ? i.substr(0, j) + t : "") +
    i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
    (c
      ? d +
        Math.abs(n - i)
          .toFixed(c)
          .slice(2)
      : "")
  );
};

var colum = 0; // columna por la que se filtrar�
var valor; // value del bot�n que se ha pulsado
var cuitDuplicado = false;

function EliminarImg(imagen, id) {
  $.ajax({
    data: { img: imagen },
    url: "abm_noticia_images.php",
    type: "post",
    dataType: "json",
    beforeSend: function () {
      $("#spinnerImg").css("display", "");
    },
    success: function (error) {
      if (!error) {
        $("#divImg_" + id).remove();
      } else {
        alert(
          "Ocurrio un error al eliminar la imagen!\nComuniquese con el administrador\n\n" +
            error.message
        );
      }
      //$("#spinnerImg").css("display","");
    },
    error: function (response) {
      alert("Error AJAX!\n\nComuniquese con el administrador");
    },
  });
}

function EliminarImgAlbum(IDAlbum, IDImag) {
  if (confirm("Esta seguro?")) {
    $.get(
      "deleteImageFromAlbum.php?IDAlbum=" + IDAlbum + "&IDImg=" + IDImag,
      function (data, status) {
        //alert("Data: " + data + "\nStatus: " + status);
        if (data == "OK") {
          location.reload(true);
        } else {
          alert("ERROR AL ELIMINAR IMAGEN!");
        }
      }
    );
  }
  return false;
}

function setPortada(IDAlbum, IDImag) {
  $.get(
    "setPortadaImage.php?IDAlbum=" + IDAlbum + "&IDImg=" + IDImag,
    function (data, status) {
      //alert("Data: " + data + "\nStatus: " + status);
      if (data == "OK") {
        location.reload(true);
      } else {
        alert("ERROR AL ELIMINAR IMAGEN!");
      }
    }
  );
  return false;
}

function Administrar(id) {
  window.open(
    "admin_noticia_images.php?id=" + id,
    "_blank",
    "toolbar=no, scrollbars=yes, resizable=no, top=200, left=500, width=800, height=600"
  );
}

function selecciona(obj, num) {
  t = document.getElementById("tab");
  filas = t.getElementsByTagName("tr");
  // Deseleccionar columna anterior
  for (i = 1; (ele = filas[i]); i++)
    ele.getElementsByTagName("td")[colum].className = "";
  // Seleccionar columna actual
  colum = num;
  for (i = 1; (ele = filas[i]); i++)
    ele.getElementsByTagName("td")[colum].className = "celdasel";
  // Cambiar bot�n por cuadro de texto
  valor = obj.value;
  celda = obj.parentNode;
  celda.removeChild(obj);
  txt = document.createElement("input");
  celda.appendChild(txt);
  txt.focus();
  txt.onblur = function () {
    ponerBoton(this, num);
  };
  txt.onkeyup = function () {
    filtra(this.value, num);
  };
  // Desactivar los dem�s botones
  for (i = 0; (ele = t.getElementsByTagName("input")[i]); i++)
    if (ele.type == "button") ele.disabled = true;
}

function ponerBoton(obj, num) {
  celda = obj.parentNode;
  celda.removeChild(obj);
  boton = document.createElement("input");
  boton.type = "button";
  boton.value = valor;
  boton.onclick = function () {
    selecciona(this, num);
  };
  boton.onkeypress = function () {
    selecciona(this, num);
  };
  celda.appendChild(boton);
  // Activar botones
  for (i = 0; (ele = t.getElementsByTagName("input")[i]); i++)
    ele.disabled = false;
}

function filtra(txt, col) {
  t = document.getElementById("tab");
  filas = t.getElementsByTagName("tr");

  vTexto = txt.split("-");
  if (
    col == 0 &&
    vTexto.length == 1 &&
    vTexto[0].length > 2 &&
    vTexto[0].length < 11
  ) {
    txt = vTexto[0].substr(0, 2) + "-" + vTexto[0].substr(2, vTexto[0].length);
    //alert(txt);
  } else {
    if (
      col == 0 &&
      vTexto.length == 1 &&
      vTexto[0].length > 2 &&
      vTexto[0].length == 11
    ) {
      txt =
        vTexto[0].substr(0, 2) +
        "-" +
        vTexto[0].substr(2, 8) +
        "-" +
        vTexto[0].substr(10, 1);
      //alert(txt);
    }
  }

  for (i = 1; (ele = filas[i]); i++) {
    texto = ele.getElementsByTagName("td")[colum].innerHTML.toUpperCase();
    /*for (j=0; ra=document.forms[0].rad[j]; j++) // Comprobar radio seleccionado
		  if (ra.checked) num = j;*/
    num = 1;
    if (num == 0) posi = texto.indexOf(txt.toUpperCase()) == 0;
    //else if (num==1) posi = (texto.lastIndexOf(txt.toUpperCase()) == texto.length-txt.length);
    else posi = texto.indexOf(txt.toUpperCase()) != -1;
    ele.style.display = posi ? "" : "none";
  }
}
function QuitarGuiones(cuit) {
  var guionado = cuit.split("-");

  if (guionado[1]) {
    cuit = "";
    i = 0;
    while (guionado[i]) {
      cuit += guionado[i];
      i++;
    }
  }

  return cuit;
}
function PonerGuiones(obj) {
  //document.form3.cuit.value
  var original = QuitarGuiones(obj.value);
  var condicion_iva = parseInt(document.form3.condicion_iva.value);
  var final = "";
  var validado = false;

  //console.log(condicion_iva);
  if (condicion_iva == 4 || condicion_iva == 5) {
    validado = true;
  } else {
    validado = validaCuit(original);
  }

  if (validado) {
    // PONGO LOS 2 GUIONES
    if (
      original.indexOf("-") != 2 &&
      original.lastIndexOf("-") != original.length - 2 &&
      original != "" &&
      parseInt(original) != 0
    ) {
      var tmp1 = original.substring(0, 2);
      var tmp2 = original.substring(2, original.length - 1);
      var tmp3 = original.substring(original.length - 1);
      final = tmp1 + "-" + tmp2 + "-" + tmp3;
    }

    // LOS 2 OK
    if (
      original.indexOf("-") == 2 &&
      original.lastIndexOf("-") == original.length - 2 &&
      original != "" &&
      parseInt(original) != 0
    ) {
      return true;
    }
    // EL ULTIMO MAL
    if (
      original.indexOf("-") == 2 &&
      original.lastIndexOf("-") != original.length - 2 &&
      original != "" &&
      parseInt(original) != 0
    ) {
      var tmp1 = original.substring(0, 2);
      var tmp2 = original.substring(2, original.length - 1);
      var tmp3 = original.substring(original.length - 1);
      final = tmp1 + tmp2 + "-" + tmp3;
    }
    // EL PRIMERO MAL
    if (
      original.indexOf("-") != 2 &&
      original.lastIndexOf("-") == original.length - 2 &&
      original != "" &&
      parseInt(original) != 0
    ) {
      var tmp1 = original.substring(0, 2);
      var tmp2 = original.substring(2, original.length - 1);
      var tmp3 = original.substring(original.length - 1);
      final = tmp1 + "-" + tmp2 + tmp3;
    }
    //console.log(buscarCUITDuplicado(final, obj));
    obj.value = final;
    document.getElementById("nroiibb").value = final;
    document.getElementById("cuit_cuenta1").value = final;
    obj.style = "";

    //Vani: Es denso en el onBlur, repite mucho el mensaje. Lo chequeo al final.
    //Ademas esta funcion tambien se utiliza en Unidades ejecutoras y mezcla con proveedores si lo dejo aca
    //buscarCUITDuplicado(final, obj);
    //return true;
  } else {
    //obj.focus();
    obj.style = "border-color:red;";
    return false;
  }
}
function RellenarRazonSocial(obj) {
  document.getElementById("titular_cuenta1").value = obj.value;
}

function newItem(iID) {
  $("#ac-new-form").lightbox_me({
    centered: true,
    onLoad: function () {
      //alert($("#ac_p3").attr("disabled"));
      if (!$("#ac_p3").attr("disabled")) {
        $("#ac_new_item_p3_tr").css("display", "");
      } else {
        $("#ac_new_item_p3_tr").css("display", "none");
      }
      $("#ac-new-form").find("input:first").focus();
    },
  });
  e.preventDefault();
}
function calcSubT(obj) {
  var valor = parseFloat(obj.value) * parseFloat($("#ac-newitem-cant").val());
  if (isNaN(valor)) {
    valor = 0;
  }
  if (obj.id == "ac-newitem-p1-pu") {
    $("#ac-p1-subt").html(valor.toFixed(2));
  }
  if (obj.id == "ac-newitem-p2-pu") {
    $("#ac-p2-subt").html(valor.toFixed(2));
  }
  if (obj.id == "ac-newitem-p3-pu") {
    $("#ac-p3-subt").html(valor.toFixed(2));
  }
}

function calcAllSubT() {
  var valor1 =
    parseFloat($("#ac-newitem-p1-pu").val()) *
    parseFloat($("#ac-newitem-cant").val());
  var valor2 =
    parseFloat($("#ac-newitem-p2-pu").val()) *
    parseFloat($("#ac-newitem-cant").val());
  var valor3 =
    parseFloat($("#ac-newitem-p3-pu").val()) *
    parseFloat($("#ac-newitem-cant").val());

  if (isNaN(valor1)) {
    valor1 = 0;
  }
  if (isNaN(valor2)) {
    valor2 = 0;
  }
  if (isNaN(valor3)) {
    valor3 = 0;
  }

  $("#ac-p1-subt").html(valor1.toFixed(2));
  $("#ac-p2-subt").html(valor2.toFixed(2));
  $("#ac-p3-subt").html(valor3.toFixed(2));
}

function newAct() {
  $("#lista_actas").css("display", "none");
  $("#nueva_acta").css("display", "");
  $("#view1").css("display", "block");
  $("#v1").attr("class", "selected");
  $("#view2").css("display", "none");
  $("#v2").removeAttr("class");
  $("#view3").css("display", "none");
  $("#v3").removeAttr("class");
}

function cancelAct() {
  $("#lista_actas").css("display", "");
  //clearActData();
  $("#nueva_acta").css("display", "none");
}

function delAct(iOP, iID) {
  if (confirm("Seguro de eliminar el Acta de Compra?")) {
    window.open("abm_actas_compras.php?op=2&id=" + iID, "_self");
  } else {
    alert("cancelar");
  }
}

function clearActData() {
  alert("Limpiar datos antiguos");
}

//Se llama en el alta y modificacion del acta compra, al agregar un item (momento de guardado)
function agregarItem() {
  var tds;
  var orden = parseInt($("#orden").val()) + 1;

  var desc = $("#ac-newitem-descripcion").val();
  var unid = $("#ac-newitem-unidad").val();
  var cant = $("#ac-newitem-cant").val();
  //si esta visible el proveedor 3
  var new_item_tr_p3 = $("#ac_new_item_p3_td1").css("display");
  //precio unitario del item a agregar
  var p1pu = parseFloat($("#ac-newitem-p1-pu").val());
  var p2pu = parseFloat($("#ac-newitem-p2-pu").val());
  var p3pu = parseFloat($("#ac-newitem-p3-pu").val());
  //precio por cantidad del item para cada proveedor
  var p1st = $("#ac-p1-subt").html();
  var p2st = $("#ac-p2-subt").html();
  var p3st = $("#ac-p3-subt").html();
  var p3stcss = "";
  var p2stcss = "";
  var p1stcss = "";

  //si no eligio proveedor 3
  if ($("#ac_p3").val() == "-1") {
    $("#ac_p3").attr("disabled", "disabled");
    $("#ac_p3_tr").css("display", "none");
  } else {
    $("#ac_p3_tr").css("display", "");
  }
  if (
    desc &&
    unid &&
    cant &&
    p1pu >= 0 &&
    p2pu >= 0 &&
    (p3pu >= 0 || new_item_tr_p3 == "none")
  ) {
    tds =
      "<tr id='data-row_" +
      orden +
      "' class='modo1' style='font-size:10px'>" +
      "	<td>" +
      orden +
      "</td>" +
      "	<td id='ac-item-desc-" +
      orden +
      "'>" +
      desc +
      "</td>" +
      "	<td id='ac-item-unid-" +
      orden +
      "'>" +
      unid +
      "</td>" +
      "	<td id='ac-item-cant-" +
      orden +
      "'>" +
      cant +
      "</td>" +
      "	<td  id='ac-item-p1pu-" +
      orden +
      "' " +
      p1stcss +
      ">" +
      p1pu.formatMoney(2, ",", ".") +
      "</td>" +
      "	<td id='ac_p1_st" +
      orden +
      "' " +
      p1stcss +
      ">" +
      parseFloat(p1st).formatMoney(2, ",", ".") +
      "<input type='radio' value='1' name='sel_op_" +
      orden +
      "' id='sel_op1_" +
      orden +
      "' onclick='actualizarTotales(\"" +
      orden +
      '", 1,"' +
      p1st +
      "\",false)'></td>" +
      "	<td id='ac-item-p2pu-" +
      orden +
      "' " +
      p2stcss +
      ">" +
      p2pu.formatMoney(2, ",", ".") +
      "</td>" +
      "	<td id='ac_p2_st" +
      orden +
      "' " +
      p2stcss +
      ">" +
      parseFloat(p2st).formatMoney(2, ",", ".") +
      "<input type='radio' value='2' name='sel_op_" +
      orden +
      "' id='sel_op2_" +
      orden +
      "' onclick='actualizarTotales(\"" +
      orden +
      '", 2,"' +
      p2st +
      "\",false)'></td>";

    if (new_item_tr_p3 != "none") {
      tds +=
        "	<td id='ac-item-p3pu-" +
        orden +
        "'" +
        p3stcss +
        ">" +
        p3pu.formatMoney(2, ",", ".") +
        "</td>" +
        "	<td id='ac_p3_st" +
        orden +
        "' " +
        p3stcss +
        ">" +
        parseFloat(p3st).formatMoney(2, ",", ".") +
        "<input type='radio' value='3' name='sel_op_" +
        orden +
        "' id='sel_op3_" +
        orden +
        "' onclick='actualizarTotales(\"" +
        orden +
        '", 3,"' +
        p3st +
        "\",false)'></td>";
    }

    tds +=
      "	<td id='imgDelete-" +
      orden +
      "'><img src='eliminar.png' style='width:16px;cursor: pointer;' onclick='delItem(\"" +
      orden +
      '","' +
      p1st +
      '","' +
      p2st +
      '","' +
      p3st +
      "\");'/></td>" +
      "	<td id='imgEdit-" +
      orden +
      "'><img src='actualizar_datos.png' style='width:16px;cursor: pointer;' onclick='editItem(\"" +
      orden +
      "\");'/></td>" +
      "	<td style='display:none'>" +
      "		<input type='hidden' id='ac-newitem-orden-" +
      orden +
      "' name='ac-newitem-orden-" +
      orden +
      "' value='" +
      orden +
      "'/>" +
      "		<input type='hidden' id='ac-newitem-desc-" +
      orden +
      "' name='ac-newitem-desc-" +
      orden +
      "' value='" +
      desc +
      "'/>" +
      "		<input type='hidden' id='ac-newitem-unidad-" +
      orden +
      "' name='ac-newitem-unidad-" +
      orden +
      "' value='" +
      unid +
      "'/>" +
      "		<input type='hidden' id='ac-newitem-cant-" +
      orden +
      "' name='ac-newitem-cant-" +
      orden +
      "' value='" +
      cant +
      "'/>" +
      "		<input type='hidden' id='ac-newitem-p1pu-" +
      orden +
      "' name='ac-newitem-p1pu-" +
      orden +
      "' value='" +
      p1pu.toFixed(2) +
      "'/>" +
      "		<input type='hidden' id='ac-newitem-p1st-" +
      orden +
      "' name='ac-newitem-p1st-" +
      orden +
      "' value='" +
      parseFloat(p1st).toFixed(2) +
      "'/>" +
      "		<input type='hidden' id='ac-newitem-p2pu-" +
      orden +
      "' name='ac-newitem-p2pu-" +
      orden +
      "' value='" +
      p2pu.toFixed(2) +
      "'/>" +
      "		<input type='hidden' id='ac-newitem-p2st-" +
      orden +
      "' name='ac-newitem-p2st-" +
      orden +
      "' value='" +
      parseFloat(p2st).toFixed(2) +
      "'/>";

    if (new_item_tr_p3 != "none") {
      tds +=
        "		<input type='hidden' id='ac-newitem-p3pu-" +
        orden +
        "' name='ac-newitem-p3pu-" +
        orden +
        "' value='" +
        p3pu.toFixed(2) +
        "'/>" +
        "		<input type='hidden' id='ac-newitem-p3st-" +
        orden +
        "' name='ac-newitem-p3st-" +
        orden +
        "' value='" +
        parseFloat(p3st).toFixed(2) +
        "'/>";
    }

    tds +=
      "		<input type='hidden' id='ac-newitem-lastProv-" +
      orden +
      "' value='0'/>" +
      "		<input type='hidden' id='ac-newitem-lastValue-" +
      orden +
      "' value='0'/>" +
      "	</td>";
    ("</tr>");
    //alert(tds);
    $("#ac_items").append(tds);
    $("#orden").val(parseInt($("#orden").val()) + 1);
    //esto del 9999 debe ser para que en la comparacion para los colores no se tenga en cuenta
    if (new_item_tr_p3 == "none") {
      p3st = 9999999;
    }
    setItemBackgroud(orden, p1st, p2st, p3st, true);
  } else {
    alert("Debe completar todos los campos");
  }
}

//Se llama en la modificacion del acta compra, al agregar un item al listado cuando carga la pagina
function agregarItem1(
  desc,
  unid,
  cant,
  p1pu,
  p2pu,
  p3pu,
  p1st,
  p2st,
  p3st,
  st_sel
) {
  var tds;
  var orden = parseInt($("#orden").val()) + 1;

  //precio unitario de cada proveedor
  p1pu = parseFloat(p1pu);
  p2pu = parseFloat(p2pu);
  p3pu = parseFloat(p3pu);
  //precio por cantidad de cada proveedor
  p1st = parseFloat(p1st);
  p2st = parseFloat(p2st);
  p3st = parseFloat(p3st);

  var new_item_tr_p3 = $("#ac_p3_pu_th").css("display");
  //alert(new_item_tr_p3);
  var p1tot = parseFloat($("#ac_p1_tot").val()) + parseFloat(p1st);
  var p2tot = parseFloat($("#ac_p2_tot").val()) + parseFloat(p2st);
  var p3tot = parseFloat($("#ac_p3_tot").val()) + parseFloat(p3st);
  var p3stcss = "";
  var p2stcss = "";
  var p1stcss = "";

  if (
    desc &&
    unid &&
    cant &&
    p1pu >= 0 &&
    p2pu >= 0 &&
    (p3pu >= 0 || new_item_tr_p3 == "none")
  ) {
    //if(parseInt($("#orden").val()) > 0){
    //	$("#imgDelete-"+$("#orden").val()).css("display","none");
    //}
    //$('#ac-new-form').lightbox_me().closeLightbox();
    tds =
      "<tr id='data-row_" +
      orden +
      "' class='modo1' style='font-size:10px'>" +
      "	<td>" +
      orden +
      "</td>" +
      "	<td id='ac-item-desc-" +
      orden +
      "'>" +
      desc +
      "</td>" +
      "	<td id='ac-item-unid-" +
      orden +
      "'>" +
      unid +
      "</td>" +
      "	<td id='ac-item-cant-" +
      orden +
      "'>" +
      cant +
      "</td>" +
      "	<td id='ac-item-p1pu-" +
      orden +
      "' " +
      p1stcss +
      ">" +
      p1pu.formatMoney(2, ",", ".") +
      "</td>" +
      "	<td id='ac_p1_st" +
      orden +
      "' " +
      p1stcss +
      ">" +
      p1st.formatMoney(2, ",", ".") +
      "<input type='radio' value='1' name='sel_op_" +
      orden +
      "' id='sel_op1_" +
      orden +
      "' onclick='actualizarTotales(\"" +
      orden +
      '",1,"' +
      p1st +
      "\",false)'></td>" +
      "	<td id='ac-item-p2pu-" +
      orden +
      "' " +
      p2stcss +
      ">" +
      p2pu.formatMoney(2, ",", ".") +
      "</td>" +
      "	<td id='ac_p2_st" +
      orden +
      "' " +
      p2stcss +
      ">" +
      p2st.formatMoney(2, ",", ".") +
      "<input type='radio' value='2' name='sel_op_" +
      orden +
      "' id='sel_op2_" +
      orden +
      "' onclick='actualizarTotales(\"" +
      orden +
      '",2,"' +
      p2st +
      "\",false)'></td>";
    if (new_item_tr_p3 != "none") {
      tds +=
        "	<td id='ac-item-p3pu-" +
        orden +
        "' " +
        p3stcss +
        ">" +
        p3pu.formatMoney(2, ",", ".") +
        "</td>" +
        "	<td id='ac_p3_st" +
        orden +
        "' " +
        p3stcss +
        ">" +
        p3st.formatMoney(2, ",", ".") +
        "<input type='radio' value='3' name='sel_op_" +
        orden +
        "' id='sel_op3_" +
        orden +
        "' onclick='actualizarTotales(\"" +
        orden +
        '",3,"' +
        p3st +
        "\",false)'></td>";
    }
    tds +=
      "	<td id='imgDelete-" +
      orden +
      "'><img src='eliminar.png' style='width:16px;cursor: pointer;' onclick='delItem(\"" +
      orden +
      '","' +
      p1st +
      '","' +
      p2st +
      '","' +
      p3st +
      "\");'/></td>" +
      "	<td id='imgEdit-" +
      orden +
      "'><img src='actualizar_datos.png' style='width:16px;cursor: pointer;' onclick='editItem(\"" +
      orden +
      "\");'/></td>" +
      "	<td style='display:none'>" +
      "		<input type='hidden' name='ac-newitem-orden-" +
      orden +
      "' id='ac-newitem-orden-" +
      orden +
      "' value='" +
      orden +
      "'/>" +
      "		<input type='hidden' name='ac-newitem-desc-" +
      orden +
      "' id='ac-newitem-desc-" +
      orden +
      "' value='" +
      desc +
      "'/>" +
      "		<input type='hidden' name='ac-newitem-unidad-" +
      orden +
      "' id='ac-newitem-unidad-" +
      orden +
      "' value='" +
      unid +
      "'/>" +
      "		<input type='hidden' name='ac-newitem-cant-" +
      orden +
      "' id='ac-newitem-cant-" +
      orden +
      "' value='" +
      cant +
      "'/>" +
      "		<input type='hidden' name='ac-newitem-p1pu-" +
      orden +
      "' id='ac-newitem-p1pu-" +
      orden +
      "' value='" +
      p1pu.toFixed(2) +
      "'/>" +
      "		<input type='hidden' name='ac-newitem-p1st-" +
      orden +
      "' id='ac-newitem-p1st-" +
      orden +
      "' value='" +
      p1st.toFixed(2) +
      "'/>" +
      "		<input type='hidden' name='ac-newitem-p2pu-" +
      orden +
      "' id='ac-newitem-p2pu-" +
      orden +
      "' value='" +
      p2pu.toFixed(2) +
      "'/>" +
      "		<input type='hidden' name='ac-newitem-p2st-" +
      orden +
      "' id='ac-newitem-p2st-" +
      orden +
      "' value='" +
      p2st.toFixed(2) +
      "'/>";
    if (new_item_tr_p3 != "none") {
      tds +=
        "		<input type='hidden' name='ac-newitem-p3pu-" +
        orden +
        "' id='ac-newitem-p3pu-" +
        orden +
        "' value='" +
        p3pu.toFixed(2) +
        "'/>" +
        "		<input type='hidden' name='ac-newitem-p3st-" +
        orden +
        "' id='ac-newitem-p3st-" +
        orden +
        "' value='" +
        p3st.toFixed(2) +
        "'/>";
    }
    tds +=
      "		<input type='hidden' id='ac-newitem-lastProv-" +
      orden +
      "' value='0'/>" +
      "		<input type='hidden' id='ac-newitem-lastValue-" +
      orden +
      "' value='0'/>" +
      "	</td>";
    ("</tr>");
    //alert(tds);
    $("#ac_items").append(tds);
    $("#orden").val(parseInt($("#orden").val()) + 1);
    $("#ac-newitem-descripcion").val("");
    $("#ac-newitem-unidad").val("");
    $("#ac-newitem-cant").val("");
    $("#ac-newitem-p1-pu").val("");
    $("#ac-newitem-p2-pu").val("");
    $("#ac-newitem-p3-pu").val("");
    $("#ac-p1-subt").html("0.00");
    $("#ac-p2-subt").html("0.00");
    $("#ac-p3-subt").html("0.00");
    //Se marca al de menor costo en verde de los subtotales y totales
    if (new_item_tr_p3 == "none") {
      p3st = 9999999;
    }
    setItemBackgroud(orden, p1st, p2st, p3st, true, st_sel);
  } else {
    alert(
      "Debe completar todos los campos \n\n desc: " +
        desc +
        "\nUnidad: " +
        unid +
        "\nCant: " +
        cant +
        "\nP1pu: " +
        p1pu +
        "\nP2pu" +
        p2pu +
        "\nP3pu: " +
        p3pu
    );
  }
}

function delItem(iID, p1st, p2st, p3st) {
  //console.log(iID.concat(" ",p1st," ",p2st," ",p3st));
  //Verifico el proveedor elegido
  var bP1 = $("#sel_op1_" + iID).attr("checked");
  var bP2 = $("#sel_op2_" + iID).attr("checked");
  var bP3 = $("#sel_op3_" + iID).attr("checked");
  //console.log(bP1,bP2,bP3);
  //Resto valor a borrar a los totales por proveedor de la pesta�a 3
  var p1tot = parseFloat($("#ac_p1_tot").val()) - parseFloat(p1st);
  var p2tot = parseFloat($("#ac_p2_tot").val()) - parseFloat(p2st);
  var p3tot = parseFloat($("#ac_p3_tot").val()) - parseFloat(p3st);
  //console.log(p1tot,p2tot,p3tot);
  //Se actualizan los totales por proveedor
  if (bP1) {
    //total por proveedor de la pesta�a3
    $("#ac_p1_total").html(p1tot.formatMoney(2, ",", "."));
    //campo hidden
    $("#ac_p1_tot").val(p1tot.toFixed(2));
  }
  if (bP2) {
    $("#ac_p2_total").html(p2tot.formatMoney(2, ",", "."));
    $("#ac_p2_tot").val(p2tot.toFixed(2));
  }
  if (bP3) {
    $("#ac_p3_total").html(p3tot.formatMoney(2, ",", "."));
    $("#ac_p3_tot").val(p3tot.toFixed(2));
  }

  $("#data-row_" + iID).remove();

  if ($("#orden").val() == 0) {
    $("#ac_p3").removeAttr("disabled");
  }
}

//Esta funcion se utiliza para cargar los datos en el formulario que se muestra al presionar el icono naranja de edicion
//Orden de compra, items
//El id de parametro es el nro de orden
function editItem(iID) {
  var p1pu = $("#ac-item-p1pu-" + iID).html();
  var p2pu = $("#ac-item-p2pu-" + iID).html();
  var p1st = $("#ac_p1_st" + iID).html();
  var p2st = $("#ac_p2_st" + iID).html();
  //p1pu = p1pu.replace(".","");
  //p1pu = p1pu.replace(",",".");
  //p2pu = p2pu.replace(".","");
  //p2pu = p2pu.replace(",",".");
  //p1st = p1st.replace(".","");
  //p1st = p1st.replace(",",".");
  //p2st = p2st.replace(".","");
  //p2st = p2st.replace(",",".");
  $("#ac-newitem-descripcion").val($("#ac-item-desc-" + iID).html());
  $("#ac-newitem-unidad").val($("#ac-item-unid-" + iID).html());
  $("#ac-newitem-cant").val($("#ac-item-cant-" + iID).html());
  $("#ac-newitem-p1-pu").val(p1pu);
  $("#ac-newitem-p2-pu").val(p2pu);
  //$("#ac-p1-subt").html(p1st);
  //$("#ac-p2-subt").html(p2st);
  $("#ac-edititem-id").val(iID); //nro de orden del item editado
  $("#ac-newitem-cant").focus(); //Esto debe ser para que actualice los subtotales
  $("#ac-newitem-descripcion").focus();
  if ($("#ac_p3").val() > 0) {
    var p3pu = $("#ac-item-p3pu-" + iID).html();
    var p3st = $("#ac_p3_st" + iID).html();
    //p3pu = p3pu.replace(".","");
    //p3pu = p3pu.replace(",",".");
    //p3st = p3st.replace(".","");
    //p3st = p3st.replace(",",".");
    $("#ac-newitem-p3-pu").val(p3pu);
    //$("#ac-p3-subt").html(p3st);
  }
  calcAllSubT();
}

//Esta funcion se utiliza para editar propiamente los datos al dar al boton guardar cambios
//Debe no solo actualizar los datos de la tabla sino tambien los totales restando los valores viejos y sumando los nuevos
function editarItem() {
  var i = $("#ac-edititem-id").val(); //nro de orden del item editado
  var desc = $("#ac-newitem-descripcion").val();
  var unid = $("#ac-newitem-unidad").val();
  var cant = $("#ac-newitem-cant").val();
  //precio de cada proveedor
  var p1pu = parseFloat($("#ac-newitem-p1-pu").val()).toFixed(2);
  var p2pu = parseFloat($("#ac-newitem-p2-pu").val()).toFixed(2);
  var p3pu = parseFloat($("#ac-newitem-p3-pu").val()).toFixed(2);
  //precio por cantidad del item para cada proveedor
  var p1st = parseFloat($("#ac-p1-subt").html()).toFixed(2);
  var p2st = parseFloat($("#ac-p2-subt").html()).toFixed(2);
  var p3st = parseFloat($("#ac-p3-subt").html()).toFixed(2);
  //totales por proveedor de la pesta�a 3
  var p1tot = parseFloat($("#ac_p1_tot").val()).toFixed(2);
  var p2tot = parseFloat($("#ac_p2_tot").val()).toFixed(2);
  var p3tot = parseFloat($("#ac_p3_tot").val()).toFixed(2);

  //actualiza los valores de la grilla de items
  $("#ac-item-desc-" + i).html(desc);
  $("#ac-item-unid-" + i).html(unid);
  $("#ac-item-cant-" + i).html(cant);
  $("#ac-item-p1pu-" + i).html(p1pu);
  $("#ac-item-p2pu-" + i).html(p2pu);
  $("#ac-item-p3pu-" + i).html(p3pu);
  //subtotal por proveedor por item de la grilla
  $("#ac_p1_st" + i).html(
    p1st +
      "<input type='radio' value='1' name='sel_op_" +
      i +
      "' id='sel_op1_" +
      i +
      "' onclick='actualizarTotales(\"" +
      i +
      '",1,"' +
      p1st +
      "\",false)'>"
  );
  $("#ac_p2_st" + i).html(
    p2st +
      "<input type='radio' value='2' name='sel_op_" +
      i +
      "' id='sel_op2_" +
      i +
      "' onclick='actualizarTotales(\"" +
      i +
      '",2,"' +
      p2st +
      "\",false)'>"
  );
  $("#ac_p3_st" + i).html(
    p3st +
      "<input type='radio' value='3' name='sel_op_" +
      i +
      "' id='sel_op3_" +
      i +
      "' onclick='actualizarTotales(\"" +
      i +
      '",3,"' +
      p3st +
      "\",false)'>"
  );

  //estos son input hidden que todavia no entiendo para que sirven
  $("#ac-newitem-orden-" + i).val(i);
  $("#ac-newitem-desc-" + i).val(desc);
  $("#ac-newitem-unidad-" + i).val(unid);
  $("#ac-newitem-cant-" + i).val(cant);
  $("#ac-newitem-p1pu-" + i).val(p1pu);
  $("#ac-newitem-p2pu-" + i).val(p2pu);
  $("#ac-newitem-p3pu-" + i).val(p3pu);
  $("#ac-newitem-p1st-" + i).val(p1st);
  $("#ac-newitem-p2st-" + i).val(p2st);
  $("#ac-newitem-p3st-" + i).val(p3st);

  //si el proveedor 3 esta disabled su total es 9999? no entiendo
  if ($("#ac_p3").attr("disabled")) {
    p3st = 9999999;
  }
  //Dentro de setItemBackgroud(sic) se llama a la funcion que calcula los totales
  setItemBackgroud(i, p1st, p2st, p3st, false);
  //reseteo
  $("#ac-edititem-id").val("-1");
}

function agregarOeditarItem() {
  if ($("#ac-edititem-id").val() == "-1") {
    agregarItem();
  } else {
    editarItem();
  }
  //Limpio los datos
  $("#ac-newitem-descripcion").val("");
  $("#ac-newitem-unidad").val("");
  $("#ac-newitem-cant").val("");
  $("#ac-newitem-p1-pu").val("");
  $("#ac-newitem-p2-pu").val("");
  $("#ac-newitem-p3-pu").val("");
  $("#ac-p1-subt").html("0.00");
  $("#ac-p2-subt").html("0.00");
  $("#ac-p3-subt").html("0.00");
}

function menorDistCero(lista) {
  var menor = lista[0];
  var posMenor = 0;
  for (i = 1; i < lista.length; i++) {
    //alert(lista[i]);
    if (lista[i] != 0 && (menor == 0 || lista[i] < menor)) {
      menor = lista[i];
      posMenor = i;
    }
  }
  return posMenor;
}

function setItemBackgroud(i, p1st, p2st, p3st, bPlusOnly, st_sel) {
  //console.log(i,p1st,p2st,p3st,bPlusOnly,st_sel);
  //Se marca al de menor costo en verde de los subtotales y totales
  $("#ac-item-p1pu-" + i).removeAttr("style");
  $("#ac-item-p2pu-" + i).removeAttr("style");
  $("#ac-item-p3pu-" + i).removeAttr("style");

  $("#ac_p1_st" + i).removeAttr("style");
  $("#ac_p2_st" + i).removeAttr("style");
  $("#ac_p3_st" + i).removeAttr("style");

  $("#ac_p1_tr").removeAttr("style");
  $("#ac_p2_tr").removeAttr("style");
  if ($("#ac_p3").val() != "-1") {
    $("#ac_p3_tr").removeAttr("style");
  }
  if (st_sel) {
    $("#sel_op" + st_sel + "_" + i).attr("checked", "checked");
    if (st_sel == "1") {
      pst = p1st;
    }
    if (st_sel == "2") {
      pst = p2st;
    }
    if (st_sel == "3") {
      pst = p3st;
    }
    actualizarTotales(i, st_sel, pst, bPlusOnly);
  }
  var miLista = [parseFloat(p1st), parseFloat(p2st), parseFloat(p3st)];
  var posMenor = 0;
  posMenor = menorDistCero(miLista);
  if (posMenor == 2) {
    //if((parseFloat(p3st) < parseFloat(p2st)) && (parseFloat(p3st) < parseFloat(p1st))){
    //p3stcss = "style='background-color:#AFDEA6;'";
    $("#ac-item-p3pu-" + i).attr(
      "style",
      "background-color:#AFDEA6;text-align:right"
    );
    $("#ac_p3_st" + i).attr(
      "style",
      "background-color:#AFDEA6;padding-bottom: 5px;text-align:right"
    );
    if (!st_sel || st_sel == 0) {
      $("#sel_op3_" + i).attr("checked", "checked");
      actualizarTotales(i, "3", p3st, bPlusOnly);
    }
  } else {
    $("#ac-item-p3pu-" + i).attr("style", "text-align:right");
    $("#ac_p3_st" + i).attr("style", "padding-bottom: 5px;text-align:right");
  }
  if (posMenor == 1) {
    //if((parseFloat(p2st) < parseFloat(p1st)) && (parseFloat(p2st) < parseFloat(p3st))){
    //p2stcss = "style='background-color:#AFDEA6;'";
    $("#ac-item-p2pu-" + i).attr(
      "style",
      "background-color:#AFDEA6;text-align:right"
    );
    $("#ac_p2_st" + i).attr(
      "style",
      "background-color:#AFDEA6;padding-bottom: 5px;text-align:right"
    );
    if (!st_sel || st_sel == 0) {
      $("#sel_op2_" + i).attr("checked", "checked");
      actualizarTotales(i, "2", p2st, bPlusOnly);
    }
  } else {
    $("#ac-item-p2pu-" + i).attr("style", "text-align:right");
    $("#ac_p2_st" + i).attr("style", "padding-bottom: 5px;text-align:right");
  }
  if (posMenor == 0) {
    //if((parseFloat(p1st) < parseFloat(p2st)) && (parseFloat(p1st) < parseFloat(p3st))){
    //p1stcss = "style='background-color:#AFDEA6;'";
    $("#ac-item-p1pu-" + i).attr(
      "style",
      "background-color:#AFDEA6;text-align:right"
    );
    $("#ac_p1_st" + i).attr(
      "style",
      "background-color:#AFDEA6;padding-bottom: 5px;text-align:right"
    );
    if (!st_sel || st_sel == 0) {
      $("#sel_op1_" + i).attr("checked", "checked");
      actualizarTotales(i, "1", p1st, bPlusOnly);
    }
  } else {
    $("#ac-item-p1pu-" + i).attr("style", "text-align:right");
    $("#ac_p1_st" + i).attr("style", "padding-bottom: 5px;text-align:right");
  }

  //Si son iguales los pone en color rosa
  if (parseFloat(p1st) == parseFloat(p2st)) {
    $("#ac-item-p1pu-" + i).attr(
      "style",
      "background-color:#F7D0D2;text-align:right"
    );
    $("#ac_p1_st" + i).attr(
      "style",
      "background-color:#F7D0D2;padding-bottom: 5px;text-align:right"
    );

    $("#ac-item-p2pu-" + i).attr(
      "style",
      "background-color:#F7D0D2;text-align:right"
    );
    $("#ac_p2_st" + i).attr(
      "style",
      "background-color:#F7D0D2;padding-bottom: 5px;text-align:right"
    );
  }
  if (parseFloat(p1st) == parseFloat(p3st)) {
    $("#ac-item-p1pu-" + i).attr(
      "style",
      "background-color:#F7D0D2;text-align:right"
    );
    $("#ac_p1_st" + i).attr(
      "style",
      "background-color:#F7D0D2;padding-bottom: 5px;text-align:right"
    );

    $("#ac-item-p3pu-" + i).attr(
      "style",
      "background-color:#F7D0D2;text-align:right"
    );
    $("#ac_p3_st" + i).attr(
      "style",
      "background-color:#F7D0D2;padding-bottom: 5px;text-align:right"
    );
  }
  if (parseFloat(p2st) == parseFloat(p3st)) {
    $("#ac-item-p2pu-" + i).attr(
      "style",
      "background-color:#F7D0D2;text-align:right"
    );
    $("#ac_p2_st" + i).attr(
      "style",
      "background-color:#F7D0D2;padding-bottom: 5px;text-align:right"
    );

    $("#ac-item-p3pu-" + i).attr(
      "style",
      "background-color:#F7D0D2;text-align:right"
    );
    $("#ac_p3_st" + i).attr(
      "style",
      "background-color:#F7D0D2;padding-bottom: 5px;text-align:right"
    );
  }
}

function actualizarTotales(orden, iProv, fSubT, bPlusOnly) {
  var prevProv = $("#ac-newitem-lastProv-" + orden).val();

  //alert("orden: "+orden+"\nProv: "+iProv+"\nSub Tot: "+fSubT+"\nSolo sumar: "+bPlusOnly+"\nValor anterior: "+$("#ac-newitem-lastValue-"+orden).val()+"\nProv ant:"+prevProv);
  if (bPlusOnly) {
    var ptot =
      parseFloat($("#ac_p" + iProv + "_tot").val()) + parseFloat(fSubT);
  } else {
    var ptotOld =
      parseFloat($("#ac_p" + prevProv + "_tot").val()) -
      parseFloat($("#ac-newitem-lastValue-" + orden).val());
    $("#ac_p" + prevProv + "_total").html(ptotOld.formatMoney(2, ",", "."));
    $("#ac_p" + prevProv + "_tot").val(ptotOld.toFixed(2));
    var ptot =
      parseFloat($("#ac_p" + iProv + "_tot").val()) + parseFloat(fSubT);
  }
  $("#ac-newitem-lastValue-" + orden).val(fSubT);
  $("#ac-newitem-lastProv-" + orden).val(iProv);
  $("#ac_p" + iProv + "_total").html(ptot.formatMoney(2, ",", "."));
  $("#ac_p" + iProv + "_tot").val(ptot.toFixed(2));
}

function copyProv(obj) {
  //alert(obj.options[obj.selectedIndex].text);
  var sdata = obj.options[obj.selectedIndex].text;
  if (sdata.length > 25) {
    sdata = sdata.substr(0, 25) + "...";
  }
  if (obj.id == "ac_p1") {
    $("#ac_p1_t").html(sdata);
    $("#ac_p1_3").html(sdata);
    $("#ac_new_item_p1_td1").html("P.U. " + sdata);
    $("#ac_p1_check").val(obj.options[obj.selectedIndex].value);
  }
  if (obj.id == "ac_p2") {
    $("#ac_p2_t").html(sdata);
    $("#ac_p2_3").html(sdata);
    $("#ac_new_item_p2_td1").html("P.U. " + sdata);
    $("#ac_p2_check").val(obj.options[obj.selectedIndex].value);
  }
  //Muestro precio unitario proveedor 3 (o no) al cambiar a esta pesta�a
  if (obj.id == "ac_p3") {
    if (sdata.length > 1) {
      $("#ac_p3_t").css("display", "");
      $("#ac_p3_pu_th").css("display", "");
      $("#ac_p3_st_th").css("display", "");
      $("#ac_new_item_p3_td1").css("display", "");
      $("#ac_new_item_p3_td2").css("display", "");
      $("#ac_p3_tr").css("display", "");
      $("#ac_p3_t").html(sdata);
      $("#ac_p3_3").html(sdata);
      $("#ac_new_item_p3_td1").html("P.U. " + sdata);
      $("#ac_p3_check").val(obj.options[obj.selectedIndex].value);
    } else {
      $("#ac_p3_t").css("display", "none");
      $("#ac_p3_pu_th").css("display", "none");
      $("#ac_p3_st_th").css("display", "none");
      $("#ac_new_item_p3_td1").css("display", "none");
      $("#ac_new_item_p3_td2").css("display", "none");
      $("#ac_p3_tr").css("display", "none");
    }
  }
}

/*Chequea datos de form acta compras para el alta y modificacion cuando presiona siguiente
Oculta y muestra la pesta�a siguiente*/
function checkData(formulario, op) {
  if (
    $("#ac_procedimiento").val() &&
    $("#ac_firmante").val() &&
    $("#ac_moneda").val() &&
    $("#ac_objeto").val() &&
    $("#ac_p1").val() &&
    $("#ac_p2").val()
  ) {
    // && $("#ac_p3").val()
    if (op == "1") {
      //Pesta�a 1
      $("#view1").css("display", "none");
      $("#v1").removeAttr("class");
      $("#view2").css("display", "block");
      $("#v2").attr("class", "selected");
      $("#view3").css("display", "none");
      $("#v3").removeAttr("class");
    }
    if (op == "2") {
      //Pesta�a 2
      if ($("#orden").val() == "0") {
        alert("No hay items.");
      } else {
        //alert("todo completo");
        //formulario.submit();
        $("#view1").css("display", "none");
        $("#v1").removeAttr("class");
        $("#view2").css("display", "none");
        $("#v2").removeAttr("class");
        $("#view3").css("display", "block");
        $("#v3").attr("class", "selected");
      }
    }
    if (op == "3") {
      //Pesta�a 3
      if ($("#orden").val() == "0") {
        alert("No hay items.");
      } else {
        //alert("todo completo");
        formulario.submit();
      }
    }
    return true;
  } else {
    alert("Falta completar 'Datos Generales'");
    return false;
  }
}

function validaCuit(sCUIT) {
  sCUIT = sCUIT.replace(/-/gi, "");
  var sMsj = "";
  var aMult = "5432765432";
  var aMult = aMult.split("");

  if (sCUIT && sCUIT.length == 11) {
    aCUIT = sCUIT.split("");
    var iResult = 0;
    for (i = 0; i <= 9; i++) {
      iResult += aCUIT[i] * aMult[i];
    }
    iResult = iResult % 11;
    iResult = 11 - iResult;

    if (iResult == 11) iResult = 0;
    if (iResult == 10) iResult = 9;

    if (iResult == aCUIT[10]) {
      return true;
    } else {
      sMsj = "CUIT/CUIL invalido.";
      //alert(sMsj);
      return false;
    }
  } else {
    sMsj = "CUIT demasiado corto.";
    //alert(sMsj);
    return false;
  }
}

function buscarCUITDuplicado(cuit, idProveedor) {
  //alert(objCUIT.value + objIdProveedor.value);
  var result = false;
  $.ajax({
    data: { CUIT: cuit, idProveedor: idProveedor },
    url: "function.IsCuitInBd.php",
    type: "post",
    dataType: "json",
    async: false, //async false y devolviendo el valor fuera de la llamada ajax, puedo retornar resultados
    success: function (data) {
      //console.log($("#opcion").val());
      //Si no esta modificando... no entiendo este if (Vani)
      //if($("#opcion").val() != '3'){
      //alert('enajax'+data);
      result = data;
      //if(!data){
      //alert("El CUIT existe en la Base De Datos");
      //objCUIT.focus();
      //objCUIT.style="border-color:red;";
      //No me gusta que el alert este dentro de la funcion, lo llamo fuera
      //result = data;
      //}else{
      //objCUIT.style="border-color:inherit;";
      //alert("El CUIT NO existe en la Base De Datos");
      //result = data;
      //}
      //}else{
      //obj.style="border-color:inherit;";
      //alert("asdfsadEl CUIT existe en la Base De Datos");
      //result = data;
      //}
    },
    error: function (response) {
      //Si el request falla
      alert("Error AJAX!\n\nComuniquese con el administrador");
      result = false;
    },
  });
  return result;
}

function ret(valor) {
  return valor;
}

function showIIBB(obj) {
  var valor = parseInt(obj.value);
  var nombre = obj.name;
  var IIBB = document.getElementById("IIBB");
  var nroIIBB = document.getElementById("nroiibb");
  var cmPercent = document.getElementById("cmpercent");
  var sDisplay = "";
  var CUIT = QuitarGuiones(document.getElementById("cuit").value);

  //console.clear();
  //console.log("VALOR: " + valor + " NOMBRE: " + nombre);
  if (nombre == "condicion_iva") {
    if (valor == 5 || valor == 4) {
      sDisplay = "none";
      nroIIBB.value = "";
      document.getElementById("trCMPercent").style.display = sDisplay;
      //Si es extranjero no debo mostrar la provincia
      if (valor == 4) {
        document.getElementById("trProvincia").style.display = "none";
      } else {
        document.getElementById("trProvincia").style.display = "";
      }
    } else {
      if (!validaCuit(CUIT)) {
        document.getElementById("cuit").focus();
        document.getElementById("cuit").style = "border-color:red;";
      } else {
        document.getElementById("cuit").style = "border-color:inherit;";
      }
      nroIIBB.value = document.getElementById("cuit").value;
      if (parseInt(IIBB.value) != 2) {
        nroIIBB.disabled = "disabled";
      } else {
        nroIIBB.disabled = "";
      }
      document.getElementById("trProvincia").style.display = "";
    }
    document.getElementById("trIIBB").style.display = sDisplay;
    document.getElementById("trNroIIBB").style.display = sDisplay;
  }

  if (nombre == "IIBB") {
    cmPercent.value = 100;
    if (valor != 2) {
      nroIIBB.disabled = "disabled";
      nroIIBB.value = document.getElementById("cuit").value;
      sDisplay = "none";
    } else {
      nroIIBB.disabled = "";
      nroIIBB.value = "";
      nroIIBB.maxlength = "10";
      sDisplay = "";
    }
    document.getElementById("trCMPercent").style.display = sDisplay;
  }
}

function closeOP(opID, iYear, showDate, obj) {
  var lightbox = document.getElementById("lightbox");
  var opIDtxt = document.getElementById("opID");
  var divFechaAvisoPago = document.getElementById("div_fecha_aviso_pago");

  if (obj.checked) {
    opIDtxt.innerHTML = opID + "/" + iYear;
    if (showDate == "true") divFechaAvisoPago.style.display = "block";
    else divFechaAvisoPago.style.display = "none";

    fadeIn(lightbox);
  } else {
    fadeOut(lightbox);
  }
}

function Cancelar(divId) {
  var obj = document.getElementById(divId);
  var opID = document.getElementById("opID").innerHTML.split("/");
  var opCheck = document.getElementById("closeOP_" + opID[0] + opID[1]);

  opCheck.checked = false;
  fadeOut(obj);
}

function Aceptar(divId) {
  var obj = document.getElementById(divId);
  var opID = document.getElementById("opID").innerHTML.split("/");
  var opCheck = document.getElementById("closeOP_" + opID[0] + opID[1]);
  var divFechaAvisoPago = document.getElementById("div_fecha_aviso_pago");

  var fecha_aviso_pago_txt = document.getElementById("fecha_aviso_pago");
  fecha_aviso_pago_string = fecha_aviso_pago_txt.value;
  var fecha_aviso_pago_array = fecha_aviso_pago_string.split("-");
  var fecha_aviso_pago_string =
    fecha_aviso_pago_array[2] +
    "/" +
    fecha_aviso_pago_array[1] +
    "/" +
    fecha_aviso_pago_array[0];
  var fecha_aviso_pago = new Date(fecha_aviso_pago_string);
  ayer = new Date();
  ayer.setDate(ayer.getDate() - 1);

  //Compruebo que la fecha de aviso de pago elegida sea correcta
  if (divFechaAvisoPago.style.display == "block") {
    if (fecha_aviso_pago_txt.value.trim() == "") {
      alert("Debe indicar una fecha de aviso de pago.");
      fecha_aviso_pago_txt.focus();
      return false;
    } else if (!isDataFormatValid(fecha_aviso_pago_txt.value, "dateOnly")) {
      alert(
        "El formato de la fecha de aviso de pago es incorrecto. Por favor complete la fecha con el formato: " +
          dateFormatPattern +
          "."
      );
      fecha_aviso_pago_txt.focus();
      return false;
    } else if (fecha_aviso_pago < ayer) {
      alert(
        "La fecha de aviso de pago no puede ser anterior a hoy. Por favor, corrijala."
      );
      fecha_aviso_pago_txt.focus();
      return false;
    }
  }

  $.post(
    "cerrarOP.php?opid=" +
      opID[0] +
      "&anio=" +
      opID[1] +
      "&fechaavisopago=" +
      fecha_aviso_pago_txt.value,
    function (data) {
      //eval('var obj='+data);
      //console.log(data);
      location.reload(true);
    }
  );
}

function CancelaGen(divId) {
  var obj = document.getElementById(divId);
  fadeOut(obj);
}

function ConfirmaGen(divId) {
  var obj = document.getElementById(divId);
  document.form3.submit();
}
// fade out

function fadeOut(el) {
  el.style.opacity = 1;

  (function fade() {
    if ((el.style.opacity -= 0.1) < 0) {
      el.style.display = "none";
    } else {
      requestAnimationFrame(fade);
    }
  })();
}

// fade in

function fadeIn(el, display) {
  el.style.opacity = 0;
  el.style.display = display || "block";

  (function fade() {
    var val = parseFloat(el.style.opacity);
    if (!((val += 0.1) > 1)) {
      el.style.opacity = val;
      requestAnimationFrame(fade);
    }
  })();
}

function checkCBU(obj) {
  var CBU = obj.value;

  if (CBU.length != 22) {
    obj.style = "border-color:red;";
    obj.focus();
  } else {
    obj.style = "border-color:inherit;";
  }
}

function validaEmail(email) {
  expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if (!expr.test(email) || !email) {
    sMsj = "E-mail inválido";
    return true;
  } else {
    return false;
  }
}

function validaPassword(pwd) {
  expr = /(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/;
  if (!expr.test(pwd) || !pwd) {
    sMsj = "Contraseña inválida";
    return true;
  } else {
    return false;
  }
}
