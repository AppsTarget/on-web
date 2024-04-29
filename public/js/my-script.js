$(".rg").mask("AA.AAA.AAA-A", { reverse: true });
$(".cpf").mask("000.000.000-00", { reverse: true });
$(".cnpj").mask("00.000.000/0000-00", { reverse: true });
$(".cep").mask("00000-000");
$(".celular").mask("(00) 00000-0000");
$(".telefone").mask("(00) 0000-0000");
$(".timing").mask("00:00");
$(".date-mask").mask("00/00/0000");
$(".date").mask("00/00/0000");
$(".money").mask("############,00", {reverse: true});
$(".money-brl").mask("A ############,00", {reverse: true});
$(".money-brl").blur(function() {
    var value = $(this).val();
    if (value.length == 2) {
        $(this).val(value + ',00');
    }
})
$(".money-brl2").each(function() {
  $($(this)[0]).focus(function() {
    if ($(this).val() == "") $(this).val("R$ 0,00");
  })
  $($(this)[0]).keyup(function() {
    var texto_final = $(this).val();
    if (texto_final == "") $(this).val("R$ 0,00");
    $(this).val(moneyAux(texto_final));
  });
});
$(".mask-money-brl").each(function() {
  var texto_final = (parseFloat($(this).html()) * 100).toString();
  if (texto_final.indexOf(".") > -1) texto_final = texto_final.substring(0, texto_final.indexOf("."));
  if (texto_final == "") $(this).html("R$ 0,00");
  $(this).html(moneyAux(texto_final));
  $(this).addClass("text-right");
});

function moneyAux(texto_final) {
  texto_final = phoneInt(texto_final);
  if (texto_final.length > 2) {
    var valor_inteiro = parseInt(texto_final.substring(0, texto_final.length - 2)).toString();
    var resultado_pontuado = "";
    var cont = 0;
    for (var i = valor_inteiro.length - 1; i >= 0; i--) {
      if (cont % 3 == 0 && cont > 0) resultado_pontuado = "." + resultado_pontuado;
      resultado_pontuado = valor_inteiro[i] + resultado_pontuado;
      cont++;
    }
    texto_final = resultado_pontuado + "," + texto_final.substring(texto_final.length - 2);
  } else texto_final = "0," + texto_final;
  texto_final = "R$ " + texto_final;
  if (texto_final == "R$ 0,0") texto_final += "0";
  if (texto_final == "R$ 0,") texto_final += "00";
  return texto_final;
}

$(".date").each(function() {
  var _this = $(this);
  _this.datepicker({
    format: "dd/mm/yyyy",
    locale: "pt-br",
    uiLibrary: 'bootstrap4',
    iconsLibrary: 'fontawesome'
  });
});

$(".colorpalette").each(function() {
  var _this = $(this);
  _this.colorPalettePicker({
    lines: 1,
    onSelected: function(color) {
      console.log(_this.data().input_id);
      $(_this.data().input_id).val(color);
    }
  });
});

const capitalize = (s) => {
    if (typeof s !== 'string') return ''
    return s.charAt(0).toUpperCase() + s.slice(1)
}

$.expr[":"].contains = function(a, i, m) {
  return (
      jQuery(a)
          .text()
          .toUpperCase()
          .normalize('NFD').replace(/[\u0300-\u036f]/g, "")
          .indexOf(m[3].toUpperCase()) >= 0
  );
};

// Variáveis para o time de atendimento
var sec = 0, min = 0, hour = 0, timer;
// ------------------------------------

