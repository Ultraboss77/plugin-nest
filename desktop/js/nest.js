/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */
$("#table_cmd").sortable({axis: "y", cursor: "move", items: ".cmd", placeholder: "ui-state-highlight", tolerance: "intersect", forcePlaceholderSize: true});

function printEqLogic(_eqLogic) {
    $('.type_nest').hide();
    if (isset(_eqLogic.configuration) && isset(_eqLogic.configuration.nest_type)) {
        $('.type_' + _eqLogic.configuration.nest_type).show();
    }
}

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {configuration: {}};
    }
    var tr = '<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">';
        tr += '<td class="hidden-xs">'
        tr += '<span class="cmdAttr" data-l1key="id"></span>'
        tr += '</td>'
        tr += '<td>'
        tr += '<div class="input-group">'
        tr += '<input class="cmdAttr form-control input-sm roundedLeft" data-l1key="name" placeholder="{{Nom de la commande}}">'
        tr += '<span class="input-group-btn">'
        tr += '<a class="cmdAction btn btn-sm btn-default" data-l1key="chooseIcon" title="{{Choisir une icône}}"><i class="fas fa-icons"></i></a>'
        tr += '</span>'
        tr += '<span class="cmdAttr input-group-addon roundedRight" data-l1key="display" data-l2key="icon" style="font-size:19px;padding:0 5px 0 0!important;"></span>'
        tr += '</div>'
        tr += '</td>'
        tr += '<td>'
        tr += '<span class="cmdAttr" data-l1key="type"></span>';
        tr += '<br/>';
        tr += '<span class="cmdAttr" data-l1key="subType"></span>';
        tr += '</td>'
        tr += '<td class="hidden-xs">'
        tr += '<span class="cmdAttr" data-l1key="logicalId"></span>'
        tr += '</td>'
        tr += '<td>'
        tr += '<span class="cmdAttr" data-l1key="htmlstate"></span>'
        tr += '</td>'
        tr += '<td>'
        tr += '<label class="checkbox-inline"><input type="checkbox" class="cmdAttr" data-l1key="isHistorized" checked>{{Historiser}}</label> '
        tr += '<div style="margin-top:7px;">'
        tr += '<input class="tooltips cmdAttr form-control input-sm" data-l1key="unite" placeholder="Unité" title="{{Unité}}" style="width:30%;max-width:80px;display:inline-block;margin-right:2px;">'
        tr += '</div>'
        tr += '</td>'
        tr += '<td>'
        if (is_numeric(_cmd.id)) {
                tr += '<a class="btn btn-default btn-xs cmdAction" data-action="configure"><i class="fa fa-cogs"></i></a> ';
                tr += '<a class="btn btn-default btn-xs cmdAction" data-action="test"><i class="fa fa-rss"></i> {{Tester}}</a>';
        }
        tr += '</tr>'
    $('#table_cmd tbody').append(tr);
    $('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');
    jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
}
