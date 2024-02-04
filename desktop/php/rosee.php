<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
// Déclaration des variables obligatoires
$plugin = plugin::byId('rosee');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>
<div class="row row-overflow">
    <!-- Page d'accueil du plugin -->
    <div class="col-xs-12 eqLogicThumbnailDisplay">
        <legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
        <!-- Boutons de gestion du plugin -->
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction logoPrimary" data-action="add">
                <i class="fas fa-plus-circle"></i>
                <br />
                <span>{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
                <i class="fas fa-wrench"></i><br>
                <span>{{Configuration}}</span>
            </div>
        </div>
        <!-- Champ de recherche -->
        <div class="input-group" style="margin-bottom:5px;">
            <input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
            <div class="input-group-btn">
                <a id="bt_resetObjectSearch" class="btn" style="width:30px"><i class="fas fa-times"></i>
                </a><a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>
            </div>
        </div>
        <!-- Liste des équipements du plugin "Mes équipements : Mes Points de Rosée, de Givre" -->
        <legend><i class="fas fa-umbrella"></i> <i class="icon jeedomapp-weather"></i> {{Mes Points de Rosée, de Givre}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            $status_r = 0;
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                if ($eqLogic->getConfiguration('type_calcul') != 'tendance' && $eqLogic->getConfiguration('type_calcul') != 'temperature') {
                    $status_r = 1;
                    echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '" >';
                    if ($eqLogic->getConfiguration('type_calcul') == 'tendance' or $eqLogic->getConfiguration('type_calcul') == 'temperature') {
                        echo '<img src="' . $eqLogic->getImage() . '"/>';
                    } else {
                        echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                    }
                    echo '<br>';
                    echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                    echo '<span class="hiddenAsCard displayTableRight hidden">';
                    echo '<span class="label" title="{{Type de Calcul}}">' . $eqLogic->getConfiguration('type_calcul')  .  '</span>';
                    echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Équipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Équipement non visible}}"></i>';
                    echo '</span>';
                    echo '</div>';
                }
            }
            if ($status_r == 1) {
                //echo '</div>';
            } else {
                echo "<br/><br/><br/><center><span style='color:#767676;font-size:1em;font-weight: bold;margin-left: 10px'>{{Aucun équipement de type Points de Rosée ou de Givre n'a été créé.}}</span></center>";
            }
            ?>
        </div>
        <!-- Liste des équipements du plugin "Mes équipements : Mes Tendances -->
        <legend><i class="fas fa-chart-bar"></i> {{Mes Tendances}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            $status = 0;
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                if ($eqLogic->getConfiguration('type_calcul') == 'tendance') {
                    $status = 1;
                    echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '" >';
                    if ($eqLogic->getConfiguration('type_calcul') == 'tendance' or $eqLogic->getConfiguration('type_calcul') == 'temperature') {
                        echo '<img src="' . $eqLogic->getImage() . '"/>';
                    } else {
                        echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                    }
                    echo '<br>';
                    echo '<br>';
                    echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                    echo '<span class="hiddenAsCard displayTableRight hidden">';
                    echo '<span class="label" title="{{Type de Calcul}}">' . $eqLogic->getConfiguration('type_calcul')  .  '</span>';
                    echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Équipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Équipement non visible}}"></i>';
                    echo '</span>';
                    echo '</div>';
                }
            }
            if ($status == 1) {
                //echo '</div>';
            } else {
                echo "<br/><br/><br/><center><span style='color:#767676;font-size:1em;font-weight: bold;margin-left: 10px'>{{Aucun équipement de type Tendance n'a été créé.}}</span></center>";
            }
            ?>
        </div>
        <!-- Liste des équipements du plugin "Mes équipements : Mes Températures ressenties -->
        <legend><i class="jeedom-thermo-moyen"></i> {{Mes Températures ressenties}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            $status = 0;
            foreach ($eqLogics as $eqLogic) {
                $opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
                if ($eqLogic->getConfiguration('type_calcul') == 'temperature') {
                    $status = 1;
                    echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '" >';
                    if ($eqLogic->getConfiguration('type_calcul') == 'tendance' or $eqLogic->getConfiguration('type_calcul') == 'temperature') {
                        echo '<img src="' . $eqLogic->getImage() . '"/>';
                    } else {
                        echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
                    }
                    echo '<br>';
                    echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
                    echo '<span class="hiddenAsCard displayTableRight hidden">';
                    echo '<span class="label" title="{{Type de Calcul}}">' . $eqLogic->getConfiguration('type_calcul')  .  '</span>';
                    echo ($eqLogic->getIsVisible() == 1) ? '<i class="fas fa-eye" title="{{Équipement visible}}"></i>' : '<i class="fas fa-eye-slash" title="{{Équipement non visible}}"></i>';
                    echo '</span>';
                    echo '</div>';
                }
            }
            if ($status == 1) {
                // echo '</div>';
            } else {
                echo "<br/><br/><br/><center><span style='color:#767676;font-size:1em;font-weight: bold;margin-left: 10px'>{{Aucun équipement de type Température n'a été créé.}}</span></center>";
            }
            ?>
        </div>
    </div> <!-- /.eqLogicThumbnailDisplay -->
    <!-- Page de présentation de l'équipement -->
    <div class="col-xs-12 eqLogic" style="display: none;">
        <!-- barre de gestion de l'équipement -->
        <div class="input-group pull-right" style="display:inline-flex">
            <span class="input-group-btn">
                <!-- Les balises <a></a> sont volontairement fermées à la ligne suivante pour éviter les espaces entre les boutons. Ne pas modifier -->
                <a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
                </a><a class="btn btn-warning btn-sm" id="bt_autoDEL_eq" title="{{Recréer les commandes}}"><i class="fas fa-search"></i><span class="hidden-xs"> {{Recréer les commandes}}</span>
                </a><a class="btn btn-sm btn-default eqLogicAction" data-action="copy"><i class="fas fa-copy"></i><span class="hidden-xs"> {{Dupliquer}}</span>
                </a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
                </a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
                </a>
            </span>
        </div>
        <!-- Onglets -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
            <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Équipement}}</a></li>
            <li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-list"></i> {{Commandes}}</a></li>
        </ul>
        <div class="tab-content">
            <!-- Onglet de configuration de l'équipement -->
            <div role="tabpanel" class="tab-pane active" id="eqlogictab">
                <!-- Partie gauche de l'onglet "Equipements" -->
                <!-- Paramètres généraux de l'équipement -->
                <form class="form-horizontal">
                    <fieldset>
                        <div class="col-lg-6">
                            <legend><i class="fas fa-wrench"></i> {{Paramètres généraux}}</legend>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Nom de l'équipement}}</label>
                                <div class="col-sm-6">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display:none;">
                                    <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement}}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Objet parent}}</label>
                                <div class="col-sm-6">
                                    <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                        <option value="">{{Aucun}}</option>
                                        <?php
                                        $options = '';
                                        foreach ((jeeObject::buildTree(null, false)) as $object) {
                                            $options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
                                        }
                                        echo $options;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Catégorie}}</label>
                                <div class="col-sm-6">
                                    <?php
                                    foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                        echo '<label class="checkbox-inline">';
                                        echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" >' . $value['name'];
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Options}}</label>
                                <div class="col-sm-6">
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked>{{Activer}}</label>
                                    <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked>{{Visible}}</label>
                                </div>
                            </div>

                            <!-- Paramètres spéficique de l'équipement -->
                            <legend><i class="fas fa-cogs"></i> {{Paramètres spécifiques}}</legend>
                            <!-- Champ de saisie du cron d'auto-actualisation + assistant cron -->
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{Type de Calcul}}
                                    <sup><i class="fas fa-question-circle" title="{{Il est possible de se limiter à un seul calcul, Point de Rosée et Point de Givre fait tous les calculs}}"></i></sup>
                                </label>
                                <div class="col-sm-6">
                                    <select id="type_calcul" class="form-control eqLogicAttr" data-l1key="configuration" data-l2key="type_calcul">
                                        <option value=''>{{Aucun}}</option>
                                        <option value='humidityabs'>{{Humidité absolue}}</option>
                                        <option value='givre'>{{Point de Givre}}</option>
                                        <option value='rosee'>{{Point de Rosée}}</option>
                                        <option value='rosee_givre'>{{Point de Rosée et Point de Givre}}</option>
                                        <option value='temperature'>{{Température ressentie}}</option>
                                        <option value='tendance'>{{Tendance Météo}}</option>
                                    </select>
                                </div>
                            </div>
                            <div id="temperature" class="form-group" style="display:none">
                                <label class="col-md-4 control-label">{{Température}}
                                    <sup><i class="fas fa-question-circle" title="{{(°C) Température}}"></i></sup>
                                </label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="temperature" placeholder="{{(°C) Température}}">
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listCmdActionOther roundedRight" title="{{Rechercher une commande}}"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div id="temperature_offset" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Offset Température}}
                                    <sup><i class="fas fa-question-circle" title="{{À ajuster en fonction des observations locales et de la position de la sonde, 0 par défaut.}}"></i></sup>
                                </label>
                                <div class="col-md-2">
                                    <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="OffsetT" placeholder="0">
                                </div>
                            </div>
                            <div id="humidite" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Humidité Relative}}
                                    <sup><i class="fas fa-question-circle" title="F{{(%) Humidité Relative}}"></i></sup>
                                </label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="humidite" placeholder="{{(%) Humidité Relative}}">
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listCmdActionOther roundedRight" title="{{Rechercher une commande}}"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div id="pressure" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Pression Atmosphérique}}
                                    <sup><i class="fas fa-question-circle" title="{{(hPa) Pression atmosphérique réelle sur le site. 1013,25 hPa par défaut si non renseignée. Ce champ est obligatoire pour le calcul Tendance Météo}}"></i></sup>
                                </label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="pression" placeholder="1013.25 hPa">
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listCmdActionOther roundedRight" title="{{Rechercher une commande}}"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div id="wind" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Vitesse du Vent}}
                                    <sup><i class="fas fa-question-circle" title="{{(km/h) Vitesse du vent}}"></i></sup>
                                </label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="eqLogicAttr form-control roundedLeft" data-l1key="configuration" data-l2key="wind" placeholder="{{Vitesse du Vent}}">
                                        <span class="input-group-btn">
                                            <a class="btn btn-default listCmdActionOther roundedRight" title="{{Rechercher une commande}}"><i class="fas fa-list-alt"></i></a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div id="DPR" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Seuil de l'Alerte Rosée}}
                                    <sup><i class="fas fa-question-circle" title="{{(°C) Seuil de déclenchement de l'alerte rosée, 2°C par défaut (dépression du point de rosée T°-Tr°) A ajuster en fonction des observations locales.}}"></i></sup>
                                </label>
                                <div class="col-md-2">
                                    <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="DPR" placeholder="2">
                                </div>
                            </div>
                            <div id="SHA" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Seuil d'Humidité Absolue}}
                                    <sup><i class="fas fa-question-circle" title="{{Seuil d'humidité absolue en dessous duquel il est peu probable qu'il givre, 2.8 par défaut.}}"></i></sup>
                                </label>
                                <div class="col-md-2">
                                    <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="SHA" placeholder="2.8">
                                </div>
                            </div>
                            <div id="step1" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Seuil Pré-alerte Humidex}}
                                    <sup><i class="fas fa-question-circle" title="{{(°C) Seuil de déclenchement de la pré-alerte inconfort de l'indice de température, 30°C par défaut}}"></i></sup>
                                </label>
                                <div class="col-md-2">
                                    <input type="number" step="0.1"" class=" eqLogicAttr form-control" data-l1key="configuration" data-l2key="PRE_SEUIL" value="30" placeholder="{{30}}">
                                </div>
                            </div>
                            <div id="step2" class="form-group" style="display:none">
                                <label class="col-sm-4 control-label">{{Seuil Alerte Haute Humidex}}
                                    <sup><i class="fas fa-question-circle" title="{{(°C) Seuil de déclenchement de l'alerte inconfort de l'indice de température, 40°C par défaut (seuil de danger)}}"></i></sup>
                                </label>
                                <div class="col-md-2">
                                    <input type="number" step="0.1" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="SEUIL" value="40" placeholder="{{40}}">
                                </div>
                            </div>
                        </div>


                        <!-- Partie droite de l'onglet "Equipement" -->
                        <!-- Affiche l'icône du plugin par défaut mais vous pouvez y afficher les informations de votre choix -->
                        <div class="col-lg-6">
                            <legend><i class="fas fa-info"></i> {{Informations}}</legend>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-7 text-center">
                                    <img src="core/img/no_image.gif" data-original=".png" id="img_device" style="width:120px;" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>

            </div> <!-- /.tabpanel #eqlogictab-->
            <!-- Onglet des commandes de l'équipement -->
            <div role="tabpanel" class="tab-pane" id="commandtab">
                <!--  <a class="btn btn-default btn-sm pull-right cmdAction" data-action="add" style="margin-top:5px;"><i class="fas fa-plus-circle"></i> {{Ajouter une commande}}</a> -->
                <br><br>
                <div class="table-responsive">
                    <table id="table_cmd" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th class="hidden-xs" style="min-width:50px;width:70px;">ID</th>
                                <th style="min-width:200px;width:350px;">{{Nom}}</th>
                                <th>{{Type}}</th>
                                <th style="min-width:260px;">{{Options}}</th>
                                <th>{{Valeur}}</th>
                                <th style="min-width:80px;width:200px;">{{Actions}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div><!-- /.tabpanel #commandtab-->

        </div><!-- /.tab-content -->
    </div><!-- /.eqLogic -->
</div><!-- /.row row-overflow -->
<?php
include_file('desktop', 'rosee', 'js', 'rosee');
include_file('core', 'plugin.template', 'js');
?>