<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('nest');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
   <div class="col-lg-12 eqLogicThumbnailDisplay">
    <legend>{{Mes équipements Nest}}</legend>
    <?php
if (count($eqLogics) == 0) {
	echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez encore Nest de configuré, allez sur la page Générale -> Plugins, configurez votre compte Nest et sauvegardez pour voir apparaitre vos Nest}}</span></center>";
} else {
	?>
       <input class="form-control" placeholder="{{Rechercher}}" style="margin-bottom:4px;" id="in_searchEqlogic" />
       <div class="eqLogicThumbnailContainer">
        <?php
	foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				if ($eqLogic->getConfiguration('nest_type') != '') {
					echo '<img src="plugins/nest/core/img/' . $eqLogic->getConfiguration('nest_type', '') . '.jpg" height="105" width="95" />';
				} else {
					echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
				}
				echo '<br/>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
	?>
 </div>
 <?php }
?>
</div>

<div class="col-lg-12 eqLogic" style="display: none;">
   		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
    <ul class="nav nav-tabs" role="tablist">
       <li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
       <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
       <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
   </ul>

   <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
      <div role="tabpanel" class="tab-pane active" id="eqlogictab">
          <br/>
          <form class="form-horizontal">
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-4 control-label">{{Nom de l'équipement Nest}}</label>
                    <div class="col-sm-6">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement mail}}"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label" >{{Objet parent}}</label>
                    <div class="col-sm-6">
                        <select class="eqLogicAttr form-control" data-l1key="object_id">
                            <option value="">{{Aucun}}</option>
                            <?php
foreach (jeeObject::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                       </select>
                   </div>
               </div>
               <div class="form-group">
                <label class="col-sm-4 control-label">{{Activer}}</label>
                <div class="col-sm-8">
                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">{{Catégorie}}</label>
                <div class="col-sm-8">
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
   <form class="form-horizontal">
    <fieldset>
        <legend>{{Informations}}</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label">{{Type}}</label>
            <div class="col-sm-2">
                <span class="eqLogicAttr tooltips label label-default" data-l1key="configuration" data-l2key="nest_type" style="font-size : 1em" ></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">{{ID}}</label>
            <div class="col-sm-2">
                <span class="eqLogicAttr tooltips label label-default" data-l1key="logicalId" style="font-size : 1em"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">{{IP}}</label>
            <div class="col-sm-2">
                <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="local_ip" style="font-size : 1em"></span>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">{{MAC}}</label>
            <div class="col-sm-2">
                <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="local_mac" style="font-size : 1em"></span>
            </div>
        </div>
        <div class="type_nest type_protect">
            <div class="form-group">
                <label class="col-sm-2 control-label">{{Batterie}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="battery_level" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Santé}}</label>
                <div class="col-sm-1">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="battery_health_state" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Remplacer le}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="replace_by_date" style="font-size : 1em"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">{{Dernière mise à jour}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="last_update" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Dernier test}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="last_manual_test" style="font-size : 1em"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">{{Model}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="model" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Logiciel}}</label>
                <div class="col-sm-1">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="software_version" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Branché (0) ou batterie (1)}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="wired_or_battery" style="font-size : 1em"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">{{Fabriqué le}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="born_on_date" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Nom}}</label>
                <div class="col-sm-1">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="name" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Emplacement}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="where" style="font-size : 1em"></span>
                </div>
            </div>
        </div>
        <div class="type_nest type_thermostat">
            <div class="form-group">
                <label class="col-sm-2 control-label">{{IP externe}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="wan_ip" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Dernier connexion}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="last_connection" style="font-size : 1em"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">{{Alimenté}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="ac" style="font-size : 1em"></span>
                </div>
                <label class="col-sm-2 control-label">{{Batterie}}</label>
                <div class="col-sm-2">
                    <span class="eqLogicAttr tooltips label label-default" data-l1key="status" data-l2key="battery_level" style="font-size : 1em"></span>
                </div>
            </div>
        </div>
    </fieldset>
</form>
</div>
<div role="tabpanel" class="tab-pane" id="commandtab">
  <br/>
  <table id="table_cmd" class="table table-bordered table-condensed">
    <thead>
        <tr>
            <th>{{Nom}}</th><th>{{Options}}</th><th></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

</div>
</div>
</div>
</div>

<?php
include_file('desktop', 'nest', 'js', 'nest');
include_file('core', 'plugin.template', 'js');
?>
