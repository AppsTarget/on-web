(function ($) {
    "use strict";

    var paletteObj = {
        laranja_escuro : '#FF7043',
        vermelho       : '#ef5350',
        rosa           : '#EC407A',
        violeta        : '#AB47BC',
        roxo           : '#7E57C2',
        azul           : '#5C6BC0',
        azul_claro     : '#42A5F5',
        azul_bebe      : '#29B6F6',
        ciano          : '#26C6DA',
        verde_azulado  : '#26C6DA',
        verde          : '#66BB6A',
        verde_claro    : '#9CCC65',
        limao          : '#D4E157',
        amarelo        : '#FFCA28',
        laranja        : '#FFA726',
        marrom         : '#8D6E63',
        cinza          : '#78909C',
        preto          : '#000000',
        branco         : '#FFFFFF'
    }

    var methods = {
        init: function (params) {
            const defaults = $.fn.colorPalettePicker.defaults;
            if (params.bootstrap == 3) {
                $(this).addClass('dropdown');
                defaults.buttonClass = 'btn btn-default dropdown-toggle';
                defaults.button = '<button id="colorpaletterbuttonid" name="colorpalettebutton" class="{buttonClass}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><span id="{buttonPreviewName}" name="{buttonPreviewName}" style="display:none">■ </span>{buttonText} <span class="caret"></span></button>';
                defaults.dropdown = '<ul class="dropdown-menu" aria-labelledby="colorpaletterbuttonid"><h5 class="dropdown-header text-center">{dropdownTitle}</h5>';
                defaults.menu = '<ul class="list-inline" style="padding-left:10px;padding-right:10px">';
                defaults.item = '<li><div name="picker_{name}" style="background-color:{color}; width:25px; height:25px; border-radius:25px; border: 1px solid {color}; margin: 0px;cursor:pointer" data-toggle="tooltip" title="{name}" data-color="{color}"></div></li>';
            }
            const options = $.extend({}, defaults, params);

            // button configuration
            const btn = $(options.button
                .replace('{buttonText}', options.buttonText)
                .replace('{buttonPreviewName}', options.buttonPreviewName)
                .replace('{buttonPreviewName}', options.buttonPreviewName)
                .replace('{buttonClass}', options.buttonClass));
            $(this).html(btn);
            // dropdown configuration
            const dropdown = $(options.dropdown.replace('{dropdownTitle}', options.dropdownTitle));
            // check if colors passed throught data-colors
            const dataColors = $(this).attr('data-colors');
            if (dataColors != undefined) {
                options.palette = dataColors.split(',');
            }
            // check if lines passed throught data-lines
            const dataLines = $(this).attr('data-lines');
            if (dataLines != undefined)
                options.lines = dataLines;
            // calculating items per line
            const paletteLength = options.palette.length;
            const itemsPerLine = Math.round(paletteLength / options.lines);
            let paletteIndex = 0;
            for (let i = 0; i < options.lines; i++) {
                const menu = $(options.menu);

                for (let j = 0; j < itemsPerLine; j++) {
                    const paletteObjItem = paletteObj[options.palette[paletteIndex]];
                    if (paletteObjItem != undefined) {
                        menu.append(options.item.replace(/{name}/gi, options.palette[paletteIndex]).replace(/{color}/gi, paletteObjItem));
                    }
                    paletteIndex++;
                }
                dropdown.append(menu);
            }
            $(this).append(dropdown);
            // item click bindings
            $(this).find('div[name^=picker_]').on('click',
                function () {
                    const selectedColor = $(this).attr('data-color');
                    const colorSquare = $(this).parent().parent().parent().parent().find('span[name=' + options.buttonPreviewName + ']');
                    colorSquare.css('color', selectedColor);
                    if (!colorSquare.is(':visible'))
                        colorSquare.show();
                    if (typeof options.onSelected === 'function') {
                        options.onSelected(selectedColor);
                    }
                });
        }
    }

    $.fn.colorPalettePicker = function (options) {
        if (methods[options]) {
            return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof options === 'object' || !options) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Opção ' + options + ' não encontrada no colorPalettePicker.');
        }
    };

    $.fn.colorPalettePicker.defaults = {
        button: '<button name="colorpalettebutton" class="{buttonClass}" data-toggle="dropdown"><span id="{buttonPreviewName}" name="{buttonPreviewName}" style="color:rgb(120, 144, 156)">■ </span>{buttonText}</button>',
        buttonClass: 'btn btn-pickcolor dropdown-toggle',
        buttonPreviewName: 'colorpaletteselected',
        buttonText: ' ',
        dropdown: '<div class="dropdown-menu"><h5 class="dropdown-header text-center">{dropdownTitle}</h5>',
        dropdownTitle: 'Cores Disponíveis',
        menu: '<ul class="list-inline" style="padding-left:10px; padding-right:10px; text-align: center;">',
        item: '<li class="list-inline-item"><div name="picker_{name}" class="list-item-color" style="background-color:{color}; border: 1px solid {color};" data-toggle="tooltip" title="{name}" data-color="{color}"></div></li>',
        palette: ['laranja_escuro', 'vermelho', 'rosa', 'violeta', 'roxo', 'azul', 'azul_claro', 'azul_bebe', 'ciano', 'verde_azulado', 'verde', 'verde_claro', 'limao', 'amarelo', 'laranja', 'marrom', 'cinza', 'preto', 'branco'],
        lines: 1,
        bootstrap: 4,
        onSelected: null
    };    
})(jQuery);