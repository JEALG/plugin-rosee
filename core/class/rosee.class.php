<?php

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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
require_once dirname(__FILE__) . '/../../core/php/rosee.inc.php';

class rosee extends eqLogic
{
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */
    public static function deadCmd()
    {
        $return = array();
        foreach (eqLogic::byType('rosee') as $rosee) {
            foreach ($rosee->getCmd() as $cmd) {
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('infoName', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Rosée', __FILE__) . ' ' . $rosee->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Nom Information', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
                preg_match_all("/#([0-9]*)#/", $cmd->getConfiguration('calcul', ''), $matches);
                foreach ($matches[1] as $cmd_id) {
                    if (!cmd::byId(str_replace('#', '', $cmd_id))) {
                        $return[] = array('detail' => __('Rosée', __FILE__) . ' ' . $rosee->getHumanName() . ' ' . __('dans la commande', __FILE__) . ' ' . $cmd->getName(), 'help' => __('Calcul', __FILE__), 'who' => '#' . $cmd_id . '#');
                    }
                }
            }
        }
        return $return;
    }
    //Fonction Widget
    public static $_widgetPossibility = array('custom' => true);

    //Fonction exécutée automatiquement
    public static function cron5()
    {
        foreach (eqLogic::byType('rosee') as $rosee) {
            if ($rosee->getIsEnable()) {
                log::add('rosee', 'debug', '================= CRON 5 ==================');
                $rosee->getInformations();
            }
        }
    }

    public static function cron10()
    {
        foreach (eqLogic::byType('rosee') as $rosee) {
            if ($rosee->getIsEnable()) {
                log::add('rosee', 'debug', '================= CRON 10 =================');
                $rosee->getInformations();
            }
        }
    }

    public static function cron15()
    {
        foreach (eqLogic::byType('rosee') as $rosee) {
            if ($rosee->getIsEnable()) {
                log::add('rosee', 'debug', '================= CRON 15 =================');
                $rosee->getInformations();
            }
        }
    }

    public static function cron30()
    {
        foreach (eqLogic::byType('rosee') as $rosee) {
            if ($rosee->getIsEnable()) {
                log::add('rosee', 'debug', '================= CRON 30 =================');
                $rosee->getInformations();
            }
        }
    }

    public static function cronHourly()
    {
        //no both cron30 and cronHourly enabled:
        if (config::byKey('functionality::cron30::enable', 'rosee', 0) == 1) {
            config::save('functionality::cronHourly::enable', 0, 'rosee');
            return;
        }
        foreach (eqLogic::byType('rosee') as $rosee) {
            if ($rosee->getIsEnable()) {
                log::add('rosee', 'debug', '================= CRON HEURE =================');
                $rosee->getInformations();
            }
        }
    }
    // Template
    public static function templateWidget()
    {
        return rosee_Template::getTemplate();
    }
    public function AddCommand($Name, $_logicalId, $Type = 'info', $SubType = 'binary', $Template = null, $unite = null, $generic_type = null, $IsVisible = 1, $icon = 'default', $forceLineB = 'default', $valuemin = 'default', $valuemax = 'default', $_order = null, $IsHistorized = '0', $repeatevent = false, $_iconname = null, $_calculValueOffset = null, $_historizeRound = null, $_noiconname = null)
    {

        $Command = $this->getCmd(null, $_logicalId);
        if (!is_object($Command)) {
            log::add('rosee', 'debug', '| ───▶︎ CRÉATION COMMANDE : ' . $Name . ' -- Type : ' . $Type . ' -- LogicalID : ' . $_logicalId . ' -- Template Widget / Ligne : ' . $Template . '/' . $forceLineB . '-- Type de générique : ' . $generic_type . ' -- Icône : ' . $icon . ' -- Min/Max : ' . $valuemin . '/' . $valuemax . ' -- Calcul/Arrondi : ' . $_calculValueOffset . '/' . $_historizeRound . ' -- Ordre : ' . $_order);
            $Command = new roseeCmd();
            $Command->setId(null);
            $Command->setLogicalId($_logicalId);
            $Command->setEqLogic_id($this->getId());
            $Command->setName($Name);

            $Command->setType($Type);
            $Command->setSubType($SubType);

            if ($Template != null) {
                $Command->setTemplate('dashboard', $Template);
                $Command->setTemplate('mobile', $Template);
            }

            if ($unite != null && $SubType == 'numeric') {
                $Command->setUnite($unite);
            }

            $Command->setIsVisible($IsVisible);
            $Command->setIsHistorized($IsHistorized);

            if ($icon != 'default') {
                $Command->setdisplay('icon', '<i class="' . $icon . '"></i>');
            }
            if ($forceLineB != 'default') {
                $Command->setdisplay('forceReturnLineBefore', 1);
            }
            if ($_iconname != 'default') {
                $Command->setdisplay('showIconAndNamedashboard', 1);
            }
            if ($_noiconname != null) {
                $Command->setdisplay('showNameOndashboard', 0);
            }

            if ($_calculValueOffset != null) {
                $Command->setConfiguration('calculValueOffset', $_calculValueOffset);
            }

            if ($_historizeRound != null) {
                $Command->setConfiguration('historizeRound', $_historizeRound);
            }
            if ($generic_type != null) {
                $Command->setGeneric_type($generic_type);
            }

            if ($repeatevent == true && $Type == 'info') {
                $Command->setconfiguration('repeatEventManagement', 'never');
                //log::add('rosee', 'debug', '│ No Repeat pour l\'info avec le nom : ' . $Name);
            }
            if ($valuemin != 'default') {
                $Command->setconfiguration('minValue', $valuemin);
            }
            if ($valuemax != 'default') {
                $Command->setconfiguration('maxValue', $valuemax);
            }

            if ($_order != null) {
                $Command->setOrder($_order);
            }
            $Command->save();
        }

        if ($valuemin != 'default') {
            $Command->setconfiguration('minValue', $valuemin);
            $Command->save();
        }
        if ($valuemax != 'default') {
            $Command->setconfiguration('maxValue', $valuemax);
            $Command->save();
        }

        $createRefreshCmd = true;
        $refresh = $this->getCmd(null, 'refresh');
        if (!is_object($refresh)) {
            $refresh = cmd::byEqLogicIdCmdName($this->getId(), __('Rafraichir', __FILE__));
            if (is_object($refresh)) {
                $createRefreshCmd = false;
            }
        }
        if ($createRefreshCmd) {
            if (!is_object($refresh)) {
                $refresh = new roseeCmd();
                $refresh->setLogicalId('refresh');
                $refresh->setIsVisible(1);
                $refresh->setName(__('Rafraichir', __FILE__));
            }
            $refresh->setType('action');
            $refresh->setSubType('other');
            $refresh->setEqLogic_id($this->getId());
            $refresh->save();
        }
        return $Command;
    }

    /*     * *********************Methode d'instance************************* */
    public function refresh()
    {
        foreach ($this->getCmd() as $cmd) {
            $s = print_r($cmd, 1);
            log::add('rosee', 'debug', 'refresh  cmd: ' . $s);
            $cmd->execute();
        }
    }

    public function preInsert() {}

    public function postInsert() {}

    public function preSave() {}

    public function postSave()
    {
        //$_eqName = $this->getName();
        //log::add('rosee', 'debug', 'Sauvegarde de l\'équipement [postSave()] : ' . $_eqName);
        $order = 0;

        /*  ********************** Calcul *************************** */
        $calcul = $this->getConfiguration('type_calcul');
        $templatecore_V4  = 'core::';
        $td_num_min = null;
        if ($calcul == 'tendance') {
            $td_num_max = 5;
            $td_num_visible = 1;
            $td_num = 1;
            $template_td = 'default';
            $template_td_num = 'rosee::tendance';
            $name_td = (__('Tendance', __FILE__));
            $name_td_num = (__('Tendance numérique', __FILE__));
            $_iconname_td = 1;
            $_iconname_td_num = 1;
        } else if ($calcul == 'temperature') {
            /* spécifique à température */
            $td_num_min = -7;
            $td_num_max = 8;
            $td_num_visible = 0;
            $td_num = 1;
            $template_td = 'default';
            $template_td_num = $templatecore_V4 . 'line';
            $name_td = (__('Message', __FILE__));
            $name_td_num = (__('Message numérique', __FILE__));
            $_iconname_td = 1;
            $_iconname_td_num = null;
            $alert1 = (__('Pré Alerte Humidex', __FILE__));
            $alert2 = (__('Alerte Humidex', __FILE__));
        } else {
            $td_num_min = '0';
            $td_num_max = 3;
            $td_num_visible = 0;
            $td_num = 1;
            $template_td = 'default';
            $template_td_num = $templatecore_V4 . 'line';
            $name_td = (__('Message', __FILE__));
            $name_td_num =  (__('Message numérique', __FILE__));
            $_iconname_td = 1;
            $_iconname_td_num = null;
            $alert1 =  (__('Alerte rosée', __FILE__));
            $alert2 = (__('Alerte givre', __FILE__));
        }
        /* Commun */
        $humidityname =  (__('Humidité absolue', __FILE__));
        $humidity_relative_name =  (__('Humidité Relative', __FILE__));
        $pointroseename =  (__('Point de Rosée', __FILE__));
        $pointgivrename =  (__('Point de Givrage', __FILE__));
        $temp_ressentiename =  (__('Température ressentie', __FILE__));
        $temp_name =  (__('Température', __FILE__));
        $indice_chaleur_name =  (__('Indice de Chaleur (Humidex)', __FILE__));
        $pressure_name =  (__('Pression Atmosphérique', __FILE__));
        $dPdT_name =  'dPdT';
        $vent_name =  (__('Vitesse du Vent', __FILE__));

        if ($calcul == 'rosee_givre' || $calcul == 'givre' || $calcul == 'humidityabs') {
            $this->AddCommand($humidityname, 'humidityabs_m3', 'info', 'numeric', $templatecore_V4 . 'line', 'g/m3', 'WEATHER_HUMIDITY', 1, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        }

        if ($calcul == 'rosee_givre' || $calcul == 'rosee' || $calcul == 'temperature') {
            $this->AddCommand($alert1, 'alert_1', 'info', 'binary', $templatecore_V4 . 'line', null, 'SIREN_STATE', 1, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, null, null);
        }

        if ($calcul == 'rosee_givre' || $calcul == 'rosee') {
            $this->AddCommand($pointroseename, 'rosee', 'info', 'numeric', $templatecore_V4 . 'line', '°C', 'GENERIC_INFO', 1, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        }

        if ($calcul == 'rosee_givre' || $calcul == 'givre' || $calcul == 'temperature') {
            $this->AddCommand($alert2, 'alert_2', 'info', 'binary', $templatecore_V4 . 'line', null, 'SIREN_STATE', 1, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, null, null);
            if ($calcul != 'temperature') {
                $this->AddCommand($pointgivrename, 'frost_point', 'info', 'numeric', $templatecore_V4 . 'line', '°C', 'GENERIC_INFO', 1, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
            }
        }
        if ($calcul == 'temperature') {
            $this->AddCommand($temp_ressentiename, 'windchill', 'info', 'numeric', $templatecore_V4 . 'line', '', 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 1, null);
            $this->AddCommand($indice_chaleur_name, 'humidex', 'info', 'numeric', $templatecore_V4 . 'line', null, 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 1, null);
        }
        if ($calcul != 'humidityabs' && $calcul != null && $calcul != 'rosee') {
            $this->AddCommand($name_td, 'td', 'info', 'string', $template_td, null, 'WEATHER_CONDITION', $td_num, 'default', 'default', 'default', 'default', $order++, '0', true, $_iconname_td, null, null, null);
            $this->AddCommand($name_td_num, 'td_num', 'info', 'numeric', $template_td_num, null, 'GENERIC_INFO', $td_num_visible, 'default', 'default', $td_num_min, $td_num_max, $order++, '0', true, $_iconname_td_num, null, null, null);
        }

        if ($calcul != 'tendance' && $calcul != null) {
            $this->AddCommand($temp_name, 'temperature', 'info', 'numeric', $templatecore_V4 . 'line', '°C', 'WEATHER_TEMPERATURE', 0, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        }
        if ($calcul != 'temperature' && $calcul != null) {
            $this->AddCommand($pressure_name, 'pressure', 'info', 'numeric', $templatecore_V4 . 'line', 'hPa', 'WEATHER_PRESSURE', 0, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        }
        if ($calcul == 'tendance') {
            $this->AddCommand($dPdT_name, 'dPdT', 'info', 'numeric', $templatecore_V4 . 'line', 'hPa/h', 'GENERIC_INFO', '0', 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        }
        if ($calcul == 'rosee_givre' || $calcul == 'givre' || $calcul == 'humidityabs' || $calcul == 'temperature') {
            $this->AddCommand($humidity_relative_name, 'humidityrel', 'info', 'numeric', $templatecore_V4 . 'line', '%', 'WEATHER_HUMIDITY', 0, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        }
        if ($calcul == 'temperature') {
            /*  ********************** Vitesse vent *************************** */
            $idvirt = str_replace("#", "", $this->getConfiguration('wind'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {

                $wind_unite = $cmdvirt->getUnite();
            }
            if ($wind_unite == 'm/s') {
                $wind_unite = 'km/h';
            }
            $this->AddCommand($vent_name, 'wind', 'info', 'numeric', $templatecore_V4 . 'line', $wind_unite, 'WEATHER_WIND_SPEED', 0, 'default', 'default', 'default', 'default', $order++, '0', true, 'default', null, 2, null);
        }
        $this->getInformations();
    }

    public function preUpdate()
    {
        if (!$this->getIsEnable()) return;

        if ($this->getConfiguration('type_calcul') == '') {
            log::add('rosee', 'error', '│ Configuration : Méthode de Calcul inexistant pour l\'équipement : ' . $this->getName() . ' ' . $this->getConfiguration('type_calcul'));
            throw new Exception(__((__('Le champ TYPE DE CALCUL ne peut être vide pour l\'équipement : ', __FILE__)) . $this->getName(), __FILE__));
        }
    }

    public function postUpdate() {}

    public function preRemove() {}

    public function postRemove() {}

    public function getImage()
    {
        if ($this->getConfiguration('type_calcul') != '') {
            $filename = 'plugins/rosee/core/config/img/' . $this->getConfiguration('type_calcul') . '.png';
            if (file_exists(__DIR__ . '/../../../../' . $filename)) {
                return $filename;
            }
        }
        return 'plugins/rosee/plugin_info/rosee_icon.png';
    }

    /*  **********************Getteur Setteur*************************** */
    public function getInformations()
    {
        if (!$this->getIsEnable()) return;
        $_eqName = $this->getName();
        log::add('rosee', 'debug', '┌── :fg-success:Configuration de l\'équipement ::/fg: '  . $_eqName . ' ──');

        /*  ********************** Calcul *************************** */
        $calcul = $this->getConfiguration('type_calcul');
        if ($calcul === '') {
            log::add('rosee', 'error', (__('Configuration : Méthode de Calcul inexistant pour l\'équipement :', __FILE__)) . $this->getName() . ' ' . $this->getConfiguration('type_calcul'));
            throw new Exception(__((__('Le champ TYPE DE CALCUL ne peut être vide pour l\'équipement : ', __FILE__)) . $this->getName(), __FILE__));
        } else {
            log::add('rosee', 'debug', '| ───▶︎ Méthode de calcul : ' . $calcul);
        }

        /*  ********************** TEMPERATURE *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $idvirt = str_replace("#", "", $this->getConfiguration('temperature'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $temperature = $cmdvirt->execCmd();
            if ($temperature === '') {
                log::add('rosee', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Température', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Température', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
            } else {
                log::add('rosee', 'debug', '| ───▶︎ Température : ' . $temperature . ' °C');
            }
        } else {
            if ($calcul != 'tendance') {
                log::add('rosee', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ TEMPERATURE', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
                throw new Exception(__((__('Le champ TEMPERATURE', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
            }
        }

        /*  ********************** Offset Température *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        if ($calcul != 'tendance') {
            $OffsetT = $this->getConfiguration('OffsetT');
            if ($OffsetT === '') {
                $OffsetT = 0;
            } else {
                $temperature = $temperature + $OffsetT;
            }
            log::add('rosee', 'debug', '| ───▶︎ Température avec Offset : ' . $temperature . ' °C' . ' - Offset Température : ' . $OffsetT . ' °C');
        }
        /*  ********************** VENT *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        if ($calcul == 'temperature') {
            $idvirt = str_replace("#", "", $this->getConfiguration('wind'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $wind = $cmdvirt->execCmd();
                $wind_unite = $cmdvirt->getUnite();
                if ($wind === '') {
                    log::add('rosee', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Vitesse du Vent', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                    throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Vitesse du Vent', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                } else {
                    log::add('rosee', 'debug', '| ───▶︎ Vent : ' . $wind . ' ' . $wind_unite);
                }
            } else {
                log::add('rosee', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ VITESSE DU VENT', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
                throw new Exception(__((__('Le champ VITESSE DU VENT', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
            }
            if ($wind_unite == 'm/s') {
                log::add('rosee', 'debug', '| ───▶︎ La vitesse du vent sélectionnée est en m/s, le plugin va convertir en km/h');
                $wind = $wind * 3.6;
                $wind_unite = ' km/h';
                log::add('rosee', 'debug', '| ───▶︎ Vent : ' . $wind  . ' ' . $wind_unite);
            }
        }

        /*  ********************** Seuil PRE-Alerte Humidex *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        if ($calcul === 'temperature') {
            $pre_seuil = $this->getConfiguration('PRE_SEUIL');
            if ($pre_seuil === '') {
                $pre_seuil = 30;
                log::add('rosee', 'debug', '| ───▶︎ Aucun Seuil Pré-Alerte Humidex de saisie, valeur par défaut : ' . $pre_seuil . ' °C');
            } else {
                log::add('rosee', 'debug', '| ───▶︎ Seuil Pré-Alerte Humidex : ' . $pre_seuil . ' °C');
            }
        }
        /*  ********************** Seuil Alerte Humidex*************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        if ($calcul === 'temperature') {
            $seuil = $this->getConfiguration('SEUIL');
            if ($seuil === '') {
                $seuil = 40;
                log::add('rosee', 'debug', '| ───▶︎ Aucun Seuil Alerte Humidex de saisie, valeur par défaut : ' . $seuil . ' °C');
            } else {
                log::add('rosee', 'debug', '| ───▶︎ Seuil Alerte Humidex : ' . $seuil . ' °C');
            }
        }

        /*  ********************** PRESSION *************************** => VALABLE AUSSI POUR LE PLUGIN BARO/ROSEE*/
        if ($calcul != 'temperature') {
            $pressure = $this->getConfiguration('pression');
            if ($pressure === '' && $calcul != 'tendance') { //valeur par défaut de la pression atmosphérique : 1013.25 hPa
                $pressure = 1013.25;
                log::add('rosee', 'debug', '| ───▶︎ Pression Atmosphérique aucun équipement sélectionné, valeur par défaut : ' . $pressure . ' hPa');
            } else {
                $pressureID = str_replace("#", "", $this->getConfiguration('pression'));
                $cmdvirt = cmd::byId($pressureID);
                if (is_object($cmdvirt)) {
                    $pressure = $cmdvirt->execCmd();
                    $pressureHISTO = $cmdvirt->getIsHistorized($pressureID);
                    if ($pressure === '') {
                        log::add('rosee', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Pression Atmosphérique', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                        throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Pression Atmosphérique', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                    } else {
                        $log_msg = (__('L\'historique de la commande', __FILE__)) . ' ';
                        $log_msg .= $cmdvirt->getName();
                        $log_msg .= ' ' . (__('doit être activé', __FILE__));
                        if ($pressureHISTO != 1) {
                            log::add('rosee', 'debug', '| ───▶︎ [ALERT] ' . $log_msg . ' : ' . $pressureHISTO);
                            message::add('Plugin Rosée - Givre - Tendance', $_eqName . ' : ' . $log_msg);
                        } else {
                            log::add('rosee', 'debug', '| ───▶︎ :fg-success:' . (__('L\'historique de la commande', __FILE__)) . ':/fg: ' . $cmdvirt->getName() . ':fg-success: est bien activé:/fg:');
                        }
                        log::add('rosee', 'debug', '| ───▶︎ Pression Atmosphérique (' . $cmdvirt->getName() . ') : ' . $pressure . ' hPa');
                    }
                } else {
                    log::add('rosee', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ PRESSION ATMOSPHÉRIQUE', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
                    throw new Exception(__((__('Le champ PRESSION ATMOSPHÉRIQUE', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
                }
            }
        }
        /*  ********************** HUMIDITE *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        $idvirt = str_replace("#", "", $this->getConfiguration('humidite'));
        $cmdvirt = cmd::byId($idvirt);
        if (is_object($cmdvirt)) {
            $humidity = $cmdvirt->execCmd();
            if ($humidity === '') {
                log::add('rosee', 'error', (__('La valeur :', __FILE__)) . ' ' . (__('Humidité Relative', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
                throw new Exception((__('La valeur :', __FILE__)) . ' ' . (__('Humidité Relative', __FILE__)) . ' (' . $cmdvirt->getName() .  ')' . ' ' . (__('pour l\'équipement', __FILE__)) . ' [' . $this->getName() . '] ' . (__('ne peut être vide', __FILE__)));
            } else {
                log::add('rosee', 'debug', '| ───▶︎ Humidité Relative : ' . $humidity . ' %');
            }
        } else {
            if ($calcul != 'tendance') {
                log::add('rosee', 'error', (__('Configuration :', __FILE__)) . ' ' . (__('Le champ HUMIDITÉ RELATIVE', __FILE__))  . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName() . ']');
                throw new Exception(__((__('Le champ HUMIDITÉ RELATIVE', __FILE__)) . ' ' . (__('ne peut être vide', __FILE__)) . ' ['  . $this->getName(), __FILE__) . ']');
            }
        }


        /*  ********************** SEUIL D'ALERTE ROSEE *************************** */
        if ($calcul === 'rosee' || $calcul == 'rosee_givre' || $calcul == 'givre') {
            $dpr = $this->getConfiguration('DPR');
            if ($dpr == '') {
                $dpr = 2.0;
            }
            log::add('rosee', 'debug', '| ───▶︎ Seuil DPR : ' . $dpr . ' °C');
        }

        /*  ********************** SEUIL D'HUMIDITE ABSOLUE ***************************  */
        if ($calcul == 'givre' || $calcul == 'rosee_givre') {
            $SHA = $this->getConfiguration('SHA');
            if ($SHA == '') {
                $SHA = 2.8;
            }
            log::add('rosee', 'debug', '| ───▶︎ Seuil d\'Humidité Absolue : ' . $SHA . '');
        }
        log::add('rosee', 'debug', '└──');

        /*  ********************** Conversion (si Besoin) *************************** */

        /*  ********************** Calcul de l'humidité absolue *************************** */
        if ($calcul == 'rosee_givre' || $calcul == 'givre' || $calcul == 'humidityabs') {
            log::add('rosee', 'debug', '┌── :fg-warning:Calcul de l\'humidité absolue : '  . $_eqName . ':/fg: ──');
            $humidityabs_m3 = rosee::getHumidity($temperature, $humidity, $pressure);
            log::add('rosee', 'debug', '| ───▶︎ Humidité Absolue : ' . $humidityabs_m3 . ' g/m3');
            log::add('rosee', 'debug', '└──');
        }

        /*  ********************** Calcul de la tendance *************************** => VALABLE AUSSI POUR LE PLUGIN BARO/ROSEE*/
        if ($calcul == 'tendance') {
            log::add('rosee', 'debug', '┌── :fg-warning:Calcul de la tendance ::/fg: '  . $_eqName . ' ──');
            $va_result_T = rosee::getTendance($pressureID);
            $td_num = $va_result_T[0];
            $td = $va_result_T[1];
            $dPdT = $va_result_T[2];
            log::add('rosee', 'debug', '└──');
        }

        /*  ********************** Calcul du Point de rosée *************************** */
        $alert_1  = 0;
        if ($calcul == 'rosee_givre' || $calcul == 'rosee' || $calcul == 'givre') {
            log::add('rosee', 'debug', '┌── :fg-warning:Calcul du point de rosée ::/fg: '  . $_eqName . ' ──');
            $va_result_R = rosee::getRosee($temperature, $humidity, $dpr);
            $rosee_point = $va_result_R[0];
            $alert_1 = $va_result_R[1];
            $rosee = $va_result_R[2];
            if ($calcul == 'rosee_givre' || $calcul == 'rosee') {
                log::add('rosee', 'debug', '| ───▶︎ Etat alerte rosée : ' . $alert_1 . ' - Point de Rosée : ' . $rosee_point . ' °C');
            } else {
                log::add('rosee', 'debug', '| ───▶︎ Pas de mise à jour du point de  l\'alerte rosée car le calcul est désactivé');
            }
            log::add('rosee', 'debug', '└──');
        }

        /*  ********************** Calcul du Point de givrage *************************** */
        if ($calcul == 'rosee_givre' || $calcul == 'givre') {
            log::add('rosee', 'debug', '┌── :fg-warning:Calcul du point givrage ::/fg: '  . $_eqName . ' ──');
            $va_result_G = rosee::getGivre($temperature, $SHA, $humidityabs_m3, $rosee);
            $td_num = $va_result_G[0];
            $td = $va_result_G[1];
            $alert_2  = $va_result_G[2];
            $frost_point  = $va_result_G[3];
            $msg_givre2 = $va_result_G[4];
            $msg_givre3 = $va_result_G[5];

            log::add('rosee', 'debug', '| ───▶︎ Cas Actuel N°' . $td_num . ' - Alerte givre : ' . $alert_2 . ' - Message : ' . $td);
            log::add('rosee', 'debug', '| ───▶︎ Point de Givrage : ' . $frost_point . ' °C');
            if ($msg_givre2 != '' && $msg_givre3 != '') {
                log::add('rosee', 'debug', '| ───▶︎ ' . $msg_givre2 . ' - ' . $msg_givre3);
            };
            if ($alert_2 == 1 && $alert_1 == 1) {
                $alert_1 = 0;
                log::add('rosee', 'debug', '| ───▶︎ Annulation alerte rosée : ' . $alert_1);
            };
            log::add('rosee', 'debug', '└──');
        } else {
            $alert_2 = 0;
            $frost_point = 5;
        };
        /*  ********************** Calcul de la température ressentie *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        if ($calcul == 'temperature') {
            log::add('rosee', 'debug', '┌── :fg-warning:Calcul de la température ressentie ::/fg: '  . $_eqName . ' ──');
            $result_T = rosee::getTemperature($wind, $temperature, $humidity, $pre_seuil, $seuil);
            $windchill = $result_T[0];
            $td = $result_T[1];
            $td_num = $result_T[2];
            $humidex = $result_T[3];
            $alert_1 = $result_T[4];
            $alert_2 = $result_T[5];
            log::add('rosee', 'debug', '└──');
        }

        /*  ********************** Mise à Jour des équipements *************************** */
        log::add('rosee', 'debug', '┌── :fg-info:Mise à jour ::/fg: '  . $_eqName . ' ──');

        $EqLogics = eqlogic::byId($this->getId());
        if (is_object($EqLogics) && $EqLogics->getIsEnable()) {
            switch ($calcul) {
                case 'temperature': // Température ressentie
                    $list = 'alert_1,alert_2,humidex,humidityrel,temperature,td,td_num,wind,windchill';
                    $Value_calcul = array('alert_1' => $alert_1, 'alert_2' => $alert_2, 'humidex' => $humidex, 'humidityrel' => $humidity, 'temperature' => $temperature, 'td' => $td, 'td_num' => $td_num, 'wind' => $wind, 'windchill' => $windchill);
                    break;
                case 'humidityab': // Humidité absolue
                    $list = 'humidityabs_m3,humidityrel,pressure,temperature';
                    $Value_calcul = array('humidityabs_m3' => $humidityabs_m3, 'humidityrel' => $humidity, 'pressure' => $pressure, 'temperature' => $temperature);
                    break;
                case 'tendance': // Tendance  => VALABLE AUSSI POUR LE PLUGIN BARO/ROSEE
                    $list = 'dPdT,pressure,td,td_num';
                    $Value_calcul = array('dPdT' => $dPdT, 'pressure' => $pressure, 'td' => $td, 'td_num' => $td_num);
                    break;
                case 'rosee_givre': // Point de rosee et de Givre
                    $list = 'alert_1,alert_2,frost_point,humidityabs_m3,humidityrel,pressure,rosee,temperature,td,td_num';
                    $Value_calcul = array('alert_1' => $alert_1, 'alert_2' => $alert_2, 'frost_point' => $frost_point, 'humidityabs_m3' => $humidityabs_m3, 'humidityrel' => $humidity, 'pressure' => $pressure, 'rosee' => $rosee, 'temperature' => $temperature, 'td' => $td, 'td_num' => $td_num);
                    break;
                case 'rosee': // Point de rosee
                    $list = 'alert_1,pressure,rosee,temperature';
                    $Value_calcul = array('alert_1' => $alert_1, 'pressure' => $pressure, 'rosee' => $rosee, 'temperature' => $temperature);
                    break;
                case 'givre': // Point de Givre
                    $list = 'alert_2,frost_point,humidityabs_m3,humidityrel,pressure,temperature,td,td_num';
                    $Value_calcul = array('alert_2' => $alert_2, 'frost_point' => $frost_point, 'humidityabs_m3' => $humidityabs_m3, 'humidityrel' => $humidity, 'pressure' => $pressure, 'temperature' => $temperature, 'td' => $td, 'td_num' => $td_num);
                    break;
            }
            /*    if ($calcul === 'temperature') { // Température ressentie
                $list = 'alert_1,alert_2,humidex,humidityrel,temperature,td,td_num,wind,windchill';
                $Value_calcul = array('alert_1' => $alert_1, 'alert_2' => $alert_2, 'humidex' => $humidex, 'humidityrel' => $humidity, 'temperature' => $temperature, 'td' => $td, 'td_num' => $td_num, 'wind' => $wind, 'windchill' => $windchill);
            } else if ($calcul === 'humidityabs') { // Humidité absolue
                $list = 'humidityabs_m3,humidityrel,pressure,temperature';
                $Value_calcul = array('humidityabs_m3' => $humidityabs_m3, 'humidityrel' => $humidity, 'pressure' => $pressure, 'temperature' => $temperature);
            } else if ($calcul === 'tendance') { // Tendance
                $list = 'dPdT,pressure,td,td_num';
                $Value_calcul = array('dPdT' => $dPdT, 'pressure' => $pressure, 'td' => $td, 'td_num' => $td_num);
            } else if ($calcul === 'rosee_givre') { // Point de rosee et de Givre
                $list = 'alert_1,alert_2,frost_point,humidityabs_m3,humidityrel,pressure,rosee,temperature,td,td_num';
                $Value_calcul = array('alert_1' => $alert_1, 'alert_2' => $alert_2, 'frost_point' => $frost_point, 'humidityabs_m3' => $humidityabs_m3, 'humidityrel' => $humidity, 'pressure' => $pressure, 'rosee' => $rosee, 'temperature' => $temperature, 'td' => $td, 'td_num' => $td_num);
            } else if ($calcul === 'rosee') { // Point de rosee
                $list = 'alert_1,pressure,rosee,temperature';
                $Value_calcul = array('alert_1' => $alert_1, 'pressure' => $pressure, 'rosee' => $rosee, 'temperature' => $temperature);
            } else if ($calcul === 'givre') { // Point de Givre
                $list = 'alert_2,frost_point,humidityabs_m3,humidityrel,pressure,temperature,td,td_num';
                $Value_calcul = array('alert_2' => $alert_2, 'frost_point' => $frost_point, 'humidityabs_m3' => $humidityabs_m3, 'humidityrel' => $humidity, 'pressure' => $pressure, 'temperature' => $temperature, 'td' => $td, 'td_num' => $td_num);
            }*/
            $fields = explode(',', $list);
            foreach ($this->getCmd() as $cmd) {
                foreach ($fields as $fieldname) {
                    if ($cmd->getLogicalId('data') == $fieldname) {
                        $this->checkAndUpdateCmd($fieldname, $Value_calcul[$fieldname]);
                        log::add('rosee', 'debug', '| :fg-info:───▶︎ ' . $cmd->getName() . ' ::/fg: ' . $Value_calcul[$fieldname]);
                    }
                }
            }
            log::add('rosee', 'debug', '└──');
        }
        log::add('rosee', 'debug', '================ FIN CRON OU SAUVEGARDE =================');
        return;
    }
    /*  ********************** Calcul de l'humidité absolue *************************** */
    public static function getHumidity($temperature, $humidity, $pressure)
    {
        $terme_pvs1 = 2.7877 + (7.625 * $temperature) / (241.6 + $temperature);
        log::add('rosee', 'debug', '| ───▶︎ terme_pvs1 : ' . $terme_pvs1);
        $pvs = pow(10, $terme_pvs1);
        log::add('rosee', 'debug', '| ───▶︎ Pression de saturation de la vapeur d\'eau (pvs) : ' . $pvs);
        $pv = ($humidity * $pvs) / 100.0;
        log::add('rosee', 'debug', '| ───▶︎ Pression partielle de vapeur d\'eau (pv) : ' . $pv);
        $humi_a = 0.622 * ($pv / (($pressure * 100.0) - $pv));
        log::add('rosee', 'debug', '| ───▶︎ Humidité absolue en kg d\'eau par kg d\'air : ' . $humi_a . ' kg');
        $v = (461.24 * (0.622 + $humi_a) * ($temperature + 273.15)) / ($pressure * 100.0);
        log::add('rosee', 'debug', '| ───▶︎ Volume specifique (v) : ' . $v . ' m3/kg');
        $p = 1.0 / $v;
        log::add('rosee', 'debug', '| ───▶︎ Poids spécifique (p) : ' . $p . ' m3/kg');
        $humidityabs_m3 = 1000.0 * $humi_a * $p;
        return $humidityabs_m3;
    }

    /*  ********************** Calcul du Point de rosée *************************** */
    public static function getRosee($temperature, $humidity, $dpr)
    {
        /* Paramètres de MAGNUS pour l'air saturé (entre -45°C et +60°C) : */
        $alpha = 6.112;
        $beta = 17.62;
        $lambda = 243.12;
        log::add('rosee', 'debug', '| ───▶︎ Paramètres de MAGNUS pour l\'air saturé (entre -45°C et +60°C) : Lambda = ' . $lambda . ' °C // alpha = ' . $alpha . ' hPa // beta = ' . $beta);

        $Terme1 = log($humidity / 100);
        $Terme2 = ($beta * $temperature) / ($lambda + $temperature);
        log::add('rosee', 'debug', '| ───▶︎ Terme1 = ' . $Terme1 . ' // Terme2 = ' . $Terme2);
        $rosee = $lambda * ($Terme1 + $Terme2) / ($beta - $Terme1 - $Terme2);
        $rosee_point = $rosee;
        $alert_1 = 0;

        /*  ********************** Calcul de l'alerte rosée en fonction du seuil d'alerte *************************** */
        $frost_alert_rosee = $temperature - $rosee_point;
        log::add('rosee', 'debug', '| ───▶︎ Calcul point de rosée : (Température - point de Rosée) : (' . $temperature . ' - ' . $rosee_point . ' )= ' . $frost_alert_rosee . ' °C');
        if ($frost_alert_rosee <= $dpr) {
            $alert_1 = 1;
            log::add('rosee', 'debug', '| ───▶︎ Résultat : Calcul Alerte point de rosée = (' . $frost_alert_rosee . ' <= ' . $dpr . ') = Alerte active');
        } else {
            log::add('rosee', 'debug', '| ───▶︎ Résultat : Calcul Alerte point de rosée = (' . $frost_alert_rosee . ' > ' . $dpr . ') = Alerte désactivée');
        }

        return array($rosee_point, $alert_1, $rosee);
    }
    /*  ********************** Calcul du Point de givrage *************************** */
    public static function getGivre($temperature, $SHA, $humidityabs_m3, $rosee)
    {
        $td = (__('Aucun risque de Givre', __FILE__));
        $td_num = number_format(0);
        $alert_2  = 0;
        if ($temperature <= 5) {
            $msg_givre2 = '';
            $msg_givre3 = '';
            $frost_K = 2954.61 / ($temperature + 273.15);
            $frost_K = $frost_K + 2.193665 * log(($temperature + 273.15));
            $frost_K = $frost_K - 13.3448;
            $frost_K = 2671.02 / $frost_K;
            $frost_K = $frost_K + ($rosee + 273.15) - ($temperature + 273.15);
            log::add('rosee', 'debug', '| ───▶︎ Point de givrage : ' . $frost_K . ' K');
            $frost = $frost_K - 273.15;
            $frost_point = $frost;

            if ($temperature <= 1 && $frost_point <= 0) {
                $alert_2  = 1;
                if ($humidityabs_m3 > $SHA) { // Cas N°3
                    $td = (__('Givre, Présence de givre', __FILE__));
                    $td_num = number_format(3);
                };
                if ($humidityabs_m3 < $SHA) { // Cas N°1
                    $td = (__('Givre peu probable malgré la température', __FILE__));
                    $td_num = number_format(1);
                };
            } elseif ($temperature <= 4 && $frost_point <= 0.5) { // Cas N°2
                $td = (__('Risque de givre', __FILE__));
                $td_num = number_format(2);
                $alert_2  = 1;
                //} else {// Cas N°0
            };
        } else {
            $frost_point = 5;
            $msg_givre2 = (__('Info supplémentaire : Il fait trop chaud pas de calcul de l\'alerte givre (', __FILE__)) . $temperature . ' °C > 5 °C)';
            $msg_givre3 = (__('Info supplémentaire : Point de givre fixé est : ', __FILE__)) . $frost_point . ' °C';
        };
        return array($td_num, $td, $alert_2, $frost_point, $msg_givre2, $msg_givre3);
    }
    /*  ********************** Calcul de la tendance *************************** => VALABLE AUSSI POUR LE PLUGIN BARO/ROSEE*/
    public static function getTendance($pressureID)
    {
        $histo = new scenarioExpression();
        $endDate = $histo->collectDate($pressureID);

        // calcul du timestamp actuel
        $_date1 = new DateTime("$endDate");
        $_date2 = new DateTime("$endDate");
        $startDate = $_date1->modify('-15 minute');
        $startDate = $_date1->format('Y-m-d H:i:s');
        // Valeur nulle 
        $td_moy = 100;
        $dPdT = number_format($td_moy, 3, '.', '');
        $td_num = number_format(5);
        $td = 'Pression atmosphérique nulle (historique)';

        // dernière mesure barométrique
        $h1 = $histo->lastBetween($pressureID, $startDate, $endDate);
        if ($h1 != '') {
            log::add('rosee', 'debug', '| ───▶︎ Timestamp -15min : Start/End Date : ' . $startDate . '/' . $endDate . ' - Pression Atmosphérique : ' . $h1 . ' hPa');

            // calcul du timestamp - 2h
            $endDate = $_date2->modify('-2 hour');
            $endDate = $_date2->format('Y-m-d H:i:s');
            $startDate = $_date1->modify('-2 hour');
            $startDate = $_date1->format('Y-m-d H:i:s');

            // mesure barométrique -2h
            $h2 = $histo->lastBetween($pressureID, $startDate, $endDate);

            // calculs de tendance 15min/2h
            if ($h2 != null) {
                $td2h = ($h1 - $h2) / 2;
                $log_msg = 'Tendance -2h : ' . $td2h . ' hPa/h';
                log::add('rosee', 'debug', '| ───▶︎ Timestamp -2h    : Start/End Date : ' . $startDate . '/' . $endDate . ' - Pression Atmosphérique : ' . $h2 . ' hPa - ' . $log_msg);
                // calcul du timestamp - 4h
                $endDate = $_date2->modify('-2 hour');
                $endDate = $_date2->format('Y-m-d H:i:s');
                $startDate = $_date1->modify('-2 hour');
                $startDate = $_date1->format('Y-m-d H:i:s');

                // mesure barométrique -4h
                $h4 = $histo->lastBetween($pressureID, $startDate, $endDate);

                // calculs de tendance 2h/4h
                if ($h4 != null) {
                    $td4h = (($h1 - $h4) / 4);
                    $log_msg = 'Tendance -4h : ' . $td4h . ' hPa/h';
                    log::add('rosee', 'debug', '| ───▶︎ Timestamp -4h    : Start/End Date : ' . $startDate . '/' . $endDate . ' - Pression Atmosphérique : ' . $h4 . ' hPa - ' . $log_msg);

                    // Calcul de la tendance
                    //log::add('rosee', 'debug', '│ ┌───────── Calcul Tendance Moyenne');
                    // sources : http://www.freescale.com/files/sensors/doc/app_note/AN3914.pdf
                    // et : https://www.parallax.com/sites/default/files/downloads/29124-Altimeter-Application-Note-501.pdf
                    $td_moy = (0.5 * $td2h + 0.5 * $td4h);
                    $dPdT = number_format($td_moy, 3, '.', '');

                    log::add('rosee', 'debug', '| ───▶︎ Tendance Moyenne (dPdT): ' . $dPdT . ' hPa/h');
                    if ($td_moy > 2.5) { // Quickly rising High Pressure System, not stable
                        $td = (__('Forte embellie, instable', __FILE__));
                        $td_num = number_format(5);
                    } elseif ($td_moy > 0.5 && $td_moy <= 2.5) { // Slowly rising High Pressure System, stable good weather
                        $td = (__('Amélioration, beau temps durable', __FILE__));
                        $td_num = number_format(4);
                    } elseif ($td_moy > 0.0 && $td_moy <= 0.5) { // Stable weather condition
                        $td = (__('Lente amélioration, temps stable', __FILE__));
                        $td_num = number_format(3);
                    } elseif ($td_moy > -0.5 && $td_moy <= 0) { // Stable weather condition
                        $td = (__('Lente dégradation, temps stable', __FILE__));
                        $td_num = number_format(2);
                    } elseif ($td_moy > -2.5 && $td_moy <= -0.5) { // Slowly falling Low Pressure System, stable rainy weather
                        $td = (__('Dégradation, mauvais temps durable', __FILE__));
                        $td_num = number_format(1);
                    } else { // Quickly falling Low Pressure, Thunderstorm, not stable
                        $td = (__('Forte dégradation, instable', __FILE__));
                        $td_num = 0;
                    };
                } else {
                    $td4h = 0;
                    log::add('rosee', 'debug', '| ───▶︎ [ALERT] Pression Atmosphérique -4h nulle (historique) : ' . $h4 . ' hPa');
                }
            } else {
                $td2h = 0;
                log::add('rosee', 'debug', '| ───▶︎ [ALERT] Pression Atmosphérique -2h nulle (historique) : ' . $h2 . ' hPa');
            }
        } else {
            log::add('rosee', 'debug', '| ───▶︎ [ALERT] Pression Atmosphérique -15min nulle (historique) : ' . $h1 . ' hPa');
        }
        return array($td_num, $td, $dPdT);
    }
    /*  ********************** Calcul de la Température ressentie *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
    public static function getTemperature($wind, $temperature, $humidity, $pre_seuil, $seuil)
    {
        /*  ********************** Calcul du Windchill *************************** */
        //log::add('rosee', 'debug', '│ | ───▶︎ CALCUL DE LA TEMPERATURE RESSENTIE (WINDCHILL)');
        // sources : https://fr.m.wikipedia.org/wiki/Refroidissement_éolien#Calcul
        if ($temperature > 10.0) {
            $windchill = $temperature;
        } else {
            if ($wind >= 4.8) {
                $Rc1 = 13.12 + 0.6215 * $temperature;
                $Rc2 = 0.3965 * $temperature - 11.37;
                $Rc3 = pow($wind, 0.16);
                $windchill = $Rc1 + ($Rc2 * $Rc3);
            } else {
                $Rc2 = 0.1345 * $temperature - 1.59;
                $Rc3 = 0.2 * $Rc2;
                $windchill = $temperature + $Rc3 * $wind;
            }
        }
        log::add('rosee', 'debug', '| ───▶︎ Température ressentie (Windchill) : ' . $windchill . '°C');
        //log::add('rosee', 'debug', '│ └───────');

        /*  ********************** Calcul de l'indice de chaleur *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        //log::add('rosee', 'debug', '│ | ───▶︎ CALCUL DU FACTEUR HUMIDEX');
        // sources : http://www.meteo-mussidan.fr/hum.php
        $var1 = null;
        // Calcul pression vapeur eau
        $temperature_k = $temperature + 273.15;
        log::add('rosee', 'debug', '| ───▶︎ Temperature Kelvin : ' . $temperature_k . ' K');
        // Partage calcul
        $var1 = (-2937.4 / $temperature_k);
        $eTs = pow(10, ($var1 - 4.9283 * log($temperature_k) / 2.302585092994046 + 23.5471));
        $eTd = $eTs * $humidity / 100;
        //Calcul de l'humidex
        $humidex = round($temperature + (($eTd - 10) * 5 / 9));
        if ($humidex  < $temperature) {
            log::add('rosee', 'debug', '| ───▶︎ Indice de Chaleur (Humidex) < Température : ' . $humidex);
            $humidex  = $temperature;
        } else {
            log::add('rosee', 'debug', '| ───▶︎ Indice de Chaleur (Humidex) : ' . $humidex);
        }

        if ($temperature < 10) {
            if (0 < $windchill) {
                $td = (__('Pas de risque de gelures ni d’hypothermie (pour une exposition normale', __FILE__));
                $td_num = -1;
            } else if (-10 < $windchill && 0 <= $windchill) {
                $td = (__('Faible risque de gelures', __FILE__));
                $td_num = -2;
            } else if (-28 < $windchill && -10 <= $windchill) {
                $td = (__('Faible risque de gelures et d’hypothermie', __FILE__));
                $td_num = -3;
            } else if (-40 < $windchill && -28 <= $windchill) {
                $td = (__('Risque modéré de gelures en 10 à 30 minutes de la peau exposée et d’hypothermie', __FILE__));
                $td_num = -4;
            } else if (-48 < $windchill && -40 <= $windchill) {
                $td = (__('Risque élevé de gelures en 5 à 10 minutes (voir note) de la peau exposée et d’hypothermie', __FILE__));
                $td_num = -5;
            } else if (-55 < $windchill && -48 <= $windchill) {
                $td = (__('Risque très élevé de gelures en 2 à 5 minutes (voir note) sans protection intégrale ni activité', __FILE__));
                $td_num = -6;
            } else if ($windchill <= -55) {
                $td = (__('Danger ! Risque extrêmement élevé de gelures en moins de 2 minutes (voir note) et d\'hypothermie. Rester à l\'abri', __FILE__));
                $td_num = -7;
            }
        } else {
            if ($humidex < 15.0) {
                $td = (__('Sensation de frais ou de froid', __FILE__));
                $td_num = 1;
            } elseif ($humidex >= 15.0 && $humidex <= 19.0) {
                $td = (__('Aucun inconfort', __FILE__));
                $td_num = 2;
            } elseif ($humidex > 19.0 && $humidex <= 29.0) {
                $td = (__('Sensation de bien être', __FILE__));
                $td_num = 3;
            } elseif ($humidex > 29.0 && $humidex <= 34.0) {
                $td = (__('Sensation d\'inconfort plus ou moins grande', __FILE__));
                $td_num = 4;
            } elseif ($humidex > 34.0 && $humidex <= 39.0) {
                $td = (__('Sensation d\'inconfort assez grande. Prudence. Ralentir certaines activités en plein air.', __FILE__));
                $td_num = 5;
            } elseif ($humidex > 39.0 && $humidex <= 45.0) {
                $td = (__('Sensation d\'inconfort généralisée. Danger. Éviter les efforts.', __FILE__));
                $td_num = 6;
            } elseif ($humidex > 45.0 && $humidex <= 53.0) {
                $td = (__('Danger extrême. Arrêt de travail dans de nombreux domaines.', __FILE__));
                $td_num = 7;
            } else {
                $td = (__('Coup de chaleur imminent (danger de mort).', __FILE__));
                $td_num = 8;
            }
        }
        // log::add('rosee', 'debug', '│ └──');

        /*  ********************** Calcul de l'alerte inconfort indice de chaleur en fonction du seuil d'alerte *************************** => VALABLE AUSSI POUR LE PLUGIN TEMPERATURE/ROSEE*/
        // log::add('rosee', 'debug', '│ | ───▶︎ ALERTE HUMIDEX');
        if (($humidex) >= $pre_seuil) {
            $alert_1 = 1;
        } else {
            $alert_1 = 0;
        }
        log::add('rosee', 'debug', '| ───▶︎ Seuil Pré-alerte Humidex : ' . $alert_1);

        if (($humidex) >= $seuil) {
            $alert_2 = 1;
        } else {
            $alert_2 = 0;
        }
        log::add('rosee', 'debug', '| ───▶︎ Seuil Alerte Haute Humidex : ' . $alert_2);
        //  log::add('rosee', 'debug', '└──');


        return array($windchill, $td, $td_num, $humidex, $alert_1, $alert_2);
    }
}

class roseeCmd extends cmd
{
    /*     * *************************Attributs****************************** */

    /*     * ***********************Methode static*************************** */

    /*     * *********************Methode d'instance************************* */
    public function dontRemoveCmd()
    {
        if ($this->getLogicalId() == 'refresh') {
            return true;
        }
        return false;
    }

    public function execute($_options = null)
    {
        if ($this->getLogicalId() == 'refresh') {
            log::add('rosee', 'debug', ' ─────────> ACTUALISATION MANUELLE');
            $this->getEqLogic()->getInformations();
            log::add('rosee', 'debug', ' ─────────> FIN ACTUALISATION MANUELLE');
            return;
        }
    }
}
