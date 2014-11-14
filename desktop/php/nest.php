<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'nest');
?>

<div class="row row-overflow">
    <div class="col-lg-2">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('nest') as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName() . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="col-lg-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <div class='row'>
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Général}}</legend>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">{{Nom de l'équipement nest}}</label>
                            <div class="col-lg-6">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement mail}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label" >{{Objet parent}}</label>
                            <div class="col-lg-6">
                                <select class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
                                    foreach (object::all() as $object) {
                                        echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">{{Activer}}</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
                            </div>
                            <label class="col-lg-4 control-label">{{Visible}}</label>
                            <div class="col-lg-1">
                                <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-4 control-label">{{Catégorie}}</label>
                            <div class="col-lg-8">
                                <?php
                                foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                    echo '<label class="checkbox-inline">';
                                    echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                    echo '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </fieldset> 
                </form>
            </div>
            <div class="col-lg-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Informations}}</legend>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{Type}}</label>
                            <div class="col-lg-2">
                                <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="nest_type" ></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{ID}}</label>
                            <div class="col-lg-2">
                                <span class="eqLogicAttr tooltips label label-default" data-l1key="logicalId" ></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{IP}}</label>
                            <div class="col-lg-2">
                                <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="local_ip" ></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{MAC}}</label>
                            <div class="col-lg-2">
                                <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="local_mac" ></span>
                            </div>
                        </div>
                        <div class="type_nest type_protect">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{Batterie}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="battery_level" ></span>
                                </div>
                                <label class="col-lg-2 control-label">{{Santé}}</label>
                                <div class="col-lg-1">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="battery_health_state" ></span>
                                </div>
                                <label class="col-lg-2 control-label">{{Remplacer le}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="replace_by_date" ></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{Dernière mise à jour}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="last_update" ></span>
                                </div>
                                <label class="col-lg-2 control-label">{{Dernier test}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="last_manual_test" ></span>
                                </div>
                            </div>
                        </div>
                        <div class="type_nest type_thermostat">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{IP externe}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="wan_ip" ></span>
                                </div>
                                <label class="col-lg-2 control-label">{{Dernier connexion}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="last_connection" ></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">{{Alimenté}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="ac" ></span>
                                </div>
                                <label class="col-lg-2 control-label">{{Batterie}}</label>
                                <div class="col-lg-2">
                                    <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="battery_level" ></span>
                                </div>
                            </div>
                        </div>
                    </fieldset> 
                </form>
            </div>
        </div>

        <legend>{{Commande}}</legend>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th>{{Nom}}</th><th>{{Options}}</th><th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<?php
include_file('desktop', 'nest', 'js', 'nest');
include_file('core', 'plugin.template', 'js');
?>