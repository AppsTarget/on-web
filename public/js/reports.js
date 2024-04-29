var csvTitulos, csvDados;
function csv() {
    csvTitulos = new Array();
    csvDados = new Array();
    $($("table")[0]).find("th").each(function() {
        csvTitulos.push($(this).html());
    });
    $("tbody").each(function() {
        $(this).find("tr").each(function() {
            csvDados.push("");
            $(this).find("td").each(function() {
                var conteudo = $(this).html();
                if (conteudo.indexOf("<div>") > -1) conteudo = $(this).children(":first").data().conteudo;
                else if ($(this).data().comp !== undefined) conteudo = $(this).data().comp;
                csvDados[csvDados.length - 1] += conteudo.trim() + ";";
            });
        });
    });
    setTimeout(function() {
        var dados = new Array();
        dados.push(csvTitulos.join(";"));
        for (var i = 0; i < csvDados.length - 1; i++) dados.push(csvDados[i].substring(0, csvDados[i].length - 1));
        csvMain(dados);
    }, 1000);
}

function removerCharEspecial(texto) {
    const pares = [
        ["Ã", "A"],
        ["Á", "A"],
        ["É", "E"],
        ["Ç", "C"]
    ];
    for (var i = 0; i < pares.length; i++) {
        while (texto.indexOf(pares[i][0]) > -1) texto = texto.replace(pares[i][0], pares[i][1]);
    }
    return texto;
}

function csvMain(dados) {
    var _titulo = location.href.split("/");
    _titulo = _titulo[_titulo.indexOf("imprimir") - 1];
    $.post("/saude-beta/csv", {
        _token: $("meta[name=csrf-token]").attr("content"),
        conteudo : removerCharEspecial(dados.join("\n")).toUpperCase(),
        titulo : _titulo
    }, function(data) {
        if (data != "false") window.open("/saude-beta/arqcsv/" + data, "_blank");
        else alert("Ocorreu um erro ao gerar o arquivo");
    })
}