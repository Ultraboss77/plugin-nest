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
include_file('3rdparty', 'nest_api/nest_api', 'php', 'nest');

class nest extends eqLogic {
        /*     * *************************Attributs****************************** */

        private $_collectDate = '';
        public static $_widgetPossibility = array('custom' => true, 'custom::layout' => false);

        /*     * ***********************Methode static*************************** */

        public static function cron5() {
                foreach (nest::byType('nest') as $eqLogic) {
                        if ($eqLogic->getIsEnable() == 1) {
                                $eqLogic->updateFromNest();
                        }
                }
        }

        public static function getNestApi() {
                if (config::byKey('username', 'nest') == '' || config::byKey('password', 'nest') == '') {
                        throw new Exception(__('Aucun issueToken ou Cookies défini', __FILE__));
                }
                return new nest_api(NULL, NULL, config::byKey('username', 'nest'), config::byKey('password', 'nest'));
        }

        public static function syncWithNest() {
                $nest_api = self::getNestApi();
                $devices = $nest_api->getDevices();
                log::add('nest', 'debug', 'NEST themrostat : ' . print_r($devices, true));
                foreach ($devices as $thermostat) {
                        $eqLogic = nest::byLogicalId($thermostat, 'nest');
                        if (!is_object($eqLogic)) {
                                $eqLogic = new nest();
                                $eqLogic->setName($thermostat);
                                $eqLogic->setEqType_name('nest');
                                $eqLogic->setIsVisible(1);
                                $eqLogic->setIsEnable(1);
                                $eqLogic->setCategory('heating', 1);
                                $eqLogic->setLogicalId($thermostat);
                                $eqLogic->setConfiguration('nest_type', 'thermostat');
                                $eqLogic->save();
                        }

                        $refresh = $eqLogic->getCmd(null, 'refresh');
                        if (!is_object($refresh)) {
                                $refresh = new nestCmd();
                                $refresh->setLogicalId('refresh');
                                $refresh->setIsVisible(1);
                                $refresh->setName(__('Rafraîhir', __FILE__));
                        }
                        $refresh->setType('action');
                        $refresh->setSubType('other');
                        $refresh->setEqLogic_id($eqLogic->getId());
                        $refresh->save();

                        $temperature = $eqLogic->getCmd(null, 'temperature');
                        if (!is_object($temperature)) {
                                $temperature = new nestCmd();
                                $temperature->setLogicalId('temperature');
                                $temperature->setIsVisible(1);
                                $temperature->setName(__('Température', __FILE__));
                        }
                        $temperature->setUnite('°C');
                        $temperature->setType('info');
                        $temperature->setSubType('numeric');
                        $temperature->setEqLogic_id($eqLogic->getId());
                        $temperature->save();

                        $humidity = $eqLogic->getCmd(null, 'humidity');
                        if (!is_object($humidity)) {
                                $humidity = new nestCmd();
                                $humidity->setLogicalId('humidity');
                                $humidity->setIsVisible(1);
                                $humidity->setName(__('Humidité', __FILE__));
                        }
                        $humidity->setType('info');
                        $humidity->setSubType('numeric');
                        $humidity->setUnite('%');
                        $humidity->setEqLogic_id($eqLogic->getId());
                        $humidity->save();

                        $heat = $eqLogic->getCmd(null, 'heat');
                        if (!is_object($heat)) {
                                $heat = new nestCmd();
                                $heat->setLogicalId('heat');
                                $heat->setIsVisible(0);
                                $heat->setName(__('Chauffage', __FILE__));
                        }
                        $heat->setType('info');
                        $heat->setSubType('binary');
                        $heat->setEqLogic_id($eqLogic->getId());
                        $heat->save();

                        $auto_away = $eqLogic->getCmd(null, 'auto_away');
                        if (!is_object($auto_away)) {
                                $auto_away = new nestCmd();
                                $auto_away->setLogicalId('auto_away');
                                $auto_away->setIsVisible(1);
                                $auto_away->setName(__('Etat de la détection de mouvement du Nest', __FILE__));
                        }
                        $auto_away->setType('info');
                        $auto_away->setSubType('binary');
                        $auto_away->setEqLogic_id($eqLogic->getId());
                        $auto_away->save();

                        $manual_away = $eqLogic->getCmd(null, 'manual_away');
                        if (!is_object($manual_away)) {
                                $manual_away = new nestCmd();
                                $manual_away->setLogicalId('manual_away');
                                $manual_away->setIsVisible(0);
                                $manual_away->setName(__('Absence', __FILE__));
                        }
                        $manual_away->setType('info');
                        $manual_away->setSubType('binary');
                        $manual_away->setEqLogic_id($eqLogic->getId());
                        $manual_away->save();

                        $order = $eqLogic->getCmd(null, 'order');
                        if (!is_object($order)) {
                                $order = new nestCmd();
                                $order->setLogicalId('order');
                                $order->setIsVisible(0);
                                $order->setName(__('Consigne', __FILE__));
                        }
                        $order->setType('info');
                        $order->setSubType('numeric');
                        $order->setUnite('°C');
                        $order->setEqLogic_id($eqLogic->getId());
                        $order->save();

                        $thermostat = $eqLogic->getCmd(null, 'thermostat');
                        if (!is_object($thermostat)) {
                                $thermostat = new nestCmd();
                                $thermostat->setLogicalId('thermostat');
                                $thermostat->setIsVisible(1);
                                $thermostat->setName(__('Thermostat', __FILE__));
                        }
                        $thermostat->setType('action');
                        $thermostat->setSubType('slider');
                        $thermostat->setConfiguration('minValue', 9);
                        $thermostat->setConfiguration('maxValue', 90);
                        $thermostat->setEqLogic_id($eqLogic->getId());
                        $thermostat->setUnite('°C');
                        $thermostat->save();

                        $away_on = $eqLogic->getCmd(null, 'away_on');
                        if (!is_object($away_on)) {
                                $away_on = new nestCmd();
                                $away_on->setLogicalId('away_on');
                                $away_on->setIsVisible(1);
                                $away_on->setName(__('Fonction Absent', __FILE__));
                        }
                        $away_on->setType('action');
                        $away_on->setSubType('other');
                        $away_on->setEqLogic_id($eqLogic->getId());
                        $away_on->save();

                        $away_off = $eqLogic->getCmd(null, 'away_off');
                        if (!is_object($away_off)) {
                                $away_off = new nestCmd();
                                $away_off->setLogicalId('away_off');
                                $away_off->setIsVisible(1);
                                $away_off->setName(__('Fonction Chez moi', __FILE__));
                        }
                        $away_off->setType('action');
                        $away_off->setSubType('other');
                        $away_off->setEqLogic_id($eqLogic->getId());
                        $away_off->save();

                        $auto_away_off = $eqLogic->getCmd(null, 'auto_away_off');
                        if (!is_object($auto_away_off)) {
                                $auto_away_off = new nestCmd();
                                $auto_away_off->setLogicalId('auto_away_off');
                                $auto_away_off->setIsVisible(1);
                                $auto_away_off->setName(__('Températures éco auto désactivées', __FILE__));
                        }
                        $auto_away_off->setType('action');
                        $auto_away_off->setSubType('other');
                        $auto_away_off->setEqLogic_id($eqLogic->getId());
                        $auto_away_off->save();

                        $auto_away_on = $eqLogic->getCmd(null, 'auto_away_on');
                        if (!is_object($auto_away_on)) {
                                $auto_away_on = new nestCmd();
                                $auto_away_on->setLogicalId('auto_away_on');
                                $auto_away_on->setIsVisible(1);
                                $auto_away_on->setName(__('Températures éco auto activées', __FILE__));
                        }
                        $auto_away_on->setType('action');
                        $auto_away_on->setSubType('other');
                        $auto_away_on->setEqLogic_id($eqLogic->getId());
                        $auto_away_on->save();

                        $eco_mode = $eqLogic->getCmd(null, 'eco_mode');
                        if (!is_object($eco_mode)) {
                                $eco_mode = new nestCmd();
                                $eco_mode->setLogicalId('eco_mode');
                                $eco_mode->setIsVisible(1);
                                $eco_mode->setName(__('Mode éco', __FILE__));
                        }
                        $eco_mode->setType('info');
                        $eco_mode->setSubType('string');
                        $eco_mode->setEqLogic_id($eqLogic->getId());
                        $eco_mode->save();

                        $leaf = $eqLogic->getCmd(null, 'leaf');
                        if (!is_object($leaf)) {
                                $leaf = new nestCmd();
                                $leaf->setLogicalId('leaf');
                                $leaf->setIsVisible(1);
                                $leaf->setName(__('Températures éco', __FILE__));
                        }
                        $leaf->setType('info');
                        $leaf->setSubType('binary');
                        $leaf->setEqLogic_id($eqLogic->getId());
                        $leaf->save();

                        $mode = $eqLogic->getCmd(null, 'mode');
                        if (!is_object($mode)) {
                                $mode = new nestCmd();
                                $mode->setLogicalId('mode');
                                $mode->setIsVisible(1);
                                $mode->setName(__('Mode', __FILE__));
                        }
                        $mode->setType('info');
                        $mode->setSubType('string');
                        $mode->setEqLogic_id($eqLogic->getId());
                        $mode->save();

                        $away_mode_on = $eqLogic->getCmd(null, 'away_mode_on');
                        if (!is_object($away_mode_on)) {
                                $away_mode_on = new nestCmd();
                                $away_mode_on->setLogicalId('away_mode_on');
                                $away_mode_on->setIsVisible(1);
                                $away_mode_on->setName(__('Mode éco activé', __FILE__));
                        }
                        $away_mode_on->setType('action');
                        $away_mode_on->setSubType('other');
                        $away_mode_on->setEqLogic_id($eqLogic->getId());
                        $away_mode_on->save();

                        $away_mode_off = $eqLogic->getCmd(null, 'away_mode_off');
                        if (!is_object($away_mode_off)) {
                                $away_mode_off = new nestCmd();
                                $away_mode_off->setLogicalId('away_mode_off');
                                $away_mode_off->setIsVisible(1);
                                $away_mode_off->setName(__('Mode éco désactivé', __FILE__));
                        }
                        $away_mode_off->setType('action');
                        $away_mode_off->setSubType('other');
                        $away_mode_off->setEqLogic_id($eqLogic->getId());
                        $away_mode_off->save();

                        $off = $eqLogic->getCmd(null, 'off');
                        if (!is_object($off)) {
                                $off = new nestCmd();
                                $off->setLogicalId('off');
                                $off->setIsVisible(1);
                                $off->setName(__('Eteint', __FILE__));
                        }
                        $off->setType('action');
                        $off->setSubType('other');
                        $off->setEqLogic_id($eqLogic->getId());
                        $off->save();

                        $cool = $eqLogic->getCmd(null, 'cool');
                        if (!is_object($cool)) {
                                $cool = new nestCmd();
                                $cool->setLogicalId('cool');
                                $cool->setIsVisible(1);
                                $cool->setName(__('Allumer', __FILE__));
                        }
                        $cool->setType('action');
                        $cool->setSubType('other');
                        $cool->setEqLogic_id($eqLogic->getId());
                        $cool->save();

                        $online = $eqLogic->getCmd(null, 'online');
                        if (!is_object($online)) {
                                $online = new nestCmd();
                                $online->setLogicalId('online');
                                $online->setIsVisible(1);
                                $online->setName(__('En ligne', __FILE__));
                        }
                        $online->setType('info');
                        $online->setSubType('binary');
                        $online->setEqLogic_id($eqLogic->getId());
                        $online->save();

                        $scale = $eqLogic->getCmd(null, 'scale');
                        if (!is_object($scale)) {
                                $scale = new nestCmd();
                                $scale->setLogicalId('scale');
                                $scale->setIsVisible(1);
                                $scale->setName(__('Unité', __FILE__));
                        }
                        $scale->setType('info');
                        $scale->setSubType('string');
                        $scale->setEqLogic_id($eqLogic->getId());
                        $scale->save();
                        $eqLogic->save();

                        $hot_water_on = $eqLogic->getCmd(null, 'hot_water_on');
                        if (!is_object($hot_water_on)) {
                                $hot_water_on = new nestCmd();
                                $hot_water_on->setLogicalId('hot_water_on');
                                $hot_water_on->setIsVisible(1);
                                $hot_water_on->setName(__('Allumer eau chaude', __FILE__));
                        }
                        $hot_water_on->setType('action');
                        $hot_water_on->setSubType('other');
                        $hot_water_on->setEqLogic_id($eqLogic->getId());
                        $hot_water_on->save();

                        $hot_water_off = $eqLogic->getCmd(null, 'hot_water_off');
                        if (!is_object($hot_water_off)) {
                                $hot_water_off = new nestCmd();
                                $hot_water_off->setLogicalId('hot_water_off');
                                $hot_water_off->setIsVisible(1);
                                $hot_water_off->setName(__('Éteindre eau chaude', __FILE__));
                        }
                        $hot_water_off->setType('action');
                        $hot_water_off->setSubType('other');
                        $hot_water_off->setEqLogic_id($eqLogic->getId());
                        $hot_water_off->save();

                        $hot_water = $eqLogic->getCmd(null, 'hot_water');
                        if (!is_object($hot_water)) {
                                $hot_water = new nestCmd();
                                $hot_water->setLogicalId('hot_water');
                                $hot_water->setIsVisible(1);
                                $hot_water->setName(__('Eau chaude', __FILE__));
                        }
                        $hot_water->setType('info');
                        $hot_water->setSubType('binary');
                        $hot_water->setEqLogic_id($eqLogic->getId());
                        $hot_water->save();
                }

                $devices = $nest_api->getDevices(DEVICE_TYPE_PROTECT);
                log::add('nest', 'debug', 'NEST protect device : ' . print_r($devices, true));
                foreach ($devices as $protects) {
                        $eqLogic = nest::byLogicalId($protects, 'nest');
                        if (!is_object($eqLogic)) {
                                $eqLogic = new nest();
                                $eqLogic->setName($protects);
                                $eqLogic->setEqType_name('nest');
                                $eqLogic->setIsVisible(1);
                                $eqLogic->setIsEnable(1);
                                $eqLogic->setCategory('security', 1);
                                $eqLogic->setLogicalId($protects);
                                $eqLogic->setConfiguration('nest_type', 'protect');
                        }
                        $eqLogic->setConfiguration('battery_type', '6xAA Ultimate Lithium (L91)');
                        $eqLogic->save();
                        $cmd = $eqLogic->getCmd(null, 'co_status');
                        if (!is_object($cmd)) {
                                $cmd = new nestCmd();
                                $cmd->setLogicalId('co_status');
                                $cmd->setIsVisible(1);
                                $cmd->setName(__('CO', __FILE__));
                        }
                        $cmd->setType('info');
                        $cmd->setSubType('binary');
                        $cmd->setDisplay('invertBinary', 1);
                        $cmd->setEqLogic_id($eqLogic->getId());
                        $cmd->save();

                        $cmd = $eqLogic->getCmd(null, 'smoke_status');
                        if (!is_object($cmd)) {
                                $cmd = new nestCmd();
                                $cmd->setLogicalId('smoke_status');
                                $cmd->setIsVisible(1);
                                $cmd->setName(__('Fumée', __FILE__));
                        }
                        $cmd->setType('info');
                        $cmd->setSubType('binary');
                        $cmd->setDisplay('invertBinary', 1);
                        $cmd->setEqLogic_id($eqLogic->getId());
                        $cmd->save();
                        $eqLogic->save();
                }
        }
        /*    * *********************Methode d'instance************************* */

        public function updateFromNest() {
                try {
                        $nest_api = nest::getNestApi();
                        $device_info = $nest_api->getDeviceInfo($this->getLogicalId());
                        if ($this->getStatus('nestNumberFailed', 0) > 0) {
                                $this->setStatus('nestNumberFailed', 0);
                        }
                } catch (Exception $e) {
                        if ($this->getStatus('nestNumberFailed', 0) > 3) {
                                log::add('nest', 'error', __('Erreur sur ', __FILE__) . $this->getHumanName() . ' (' . $this->getStatus('nestNumberFailed', 0) . ') : ' . $e->getMessage());
                        } else {
                                $this->setStatus('nestNumberFailed', $this->getStatus('nestNumberFailed', 0) + 1);
                        }
                        return;
                }
                log::add('nest', 'debug', print_r($device_info, true));

                $status = array();

                if (isset($device_info->network)) {
                        if (isset($device_info->network->local_ip)) {
                                $status['local_ip'] = $device_info->network->local_ip;
                        }
                        if (isset($device_info->network->mac_address)) {
                                $status['local_mac'] = $device_info->network->mac_address;
                        }
                        if (isset($device_info->network->wan_ip)) {
                                $status['wan_ip'] = $device_info->network->wan_ip;
                        }
                        if (isset($device_info->network->online)) {
                                $status['online'] = $device_info->network->online;
                        }
                        if (isset($device_info->network->last_connection)) {
                                $status['last_connection'] = $device_info->network->last_connection;
                        }
                }

                /*         * ********************PROTECT NEST********************** */
                if ($this->getConfiguration('nest_type') == 'protect') {
                        $this->checkAndUpdateCmd('co_status', $device_info->co_status);
                        $this->checkAndUpdateCmd('smoke_status', $device_info->smoke_status);
                        $this->setStatus('battery_level', $device_info->battery_level);
                        $battery_max = 5500;
                        $battery_min = 3600;
                        $battery = round(($device_info->battery_level - $battery_min) / ($battery_max - $battery_min) * 100, 0);
                        if ($battery < 0) {
                                $battery = 0;
                        }
                        $this->batteryStatus($battery);

                        $status['battery_health_state'] = $device_info->battery_health_state;
                        $status['replace_by_date'] = $device_info->replace_by_date;
                        $status['last_update'] = $device_info->last_update;
                        $status['last_manual_test'] = $device_info->last_manual_test;
                        $status['model'] = $device_info->model;
                        $status['software_version'] = $device_info->software_version;
                        $status['wired_or_battery'] = $device_info->wired_or_battery;
                        $status['born_on_date'] = $device_info->born_on_date;
                        $status['name'] = $device_info->name;
                        $status['where'] = $device_info->where;
                        $status['color'] = $device_info->color;
                        if (isset($device_info->nest_features) && is_object($device_info->nest_features)) {
                                $status['night_time_promise'] = $device_info->nest_features->night_time_promise;
                                $status['night_light'] = $device_info->nest_features->night_light;
                                $status['auto_away'] = $device_info->nest_features->auto_away;
                                $status['heads_up'] = $device_info->nest_features->heads_up;
                                $status['steam_detection'] = $device_info->nest_features->steam_detection;
                                $status['home_alarm_link'] = $device_info->nest_features->home_alarm_link;
                        }
                        $this->setStatus($status);

                        $testOk = true;
                        foreach ($device_info->tests_passed as $key => $value) {
                                $this->setCache('test_' . $key, $value);
                                if ($value != 1 && !in_array($key,array('heat','us'))) {
                                        $testOk = false;
                                        log::add('nest', 'error', __('Echec du test : ', __FILE__) . $key . __(' sur ', __FILE__) . $this->getHumanName(), 'nestTest' . $key);
                                }
                        }
                        if ($testOk) {
                                message::removeAll('nest', 'nestTest', true);
                        }
                }

                /*         * ********************THERMOSTAT NEST********************** */
                if ($this->getConfiguration('nest_type') == 'thermostat') {
                        $changed = false;
                        foreach ($device_info->current_state as $key => $value) {
                                $cmd = $this->getCmd(null, $key);
                                if ($key == 'auto_away') {
                                        $value = ($value == -1) ? 0 : 1;
                                }
                                if ($key == 'manual_away') {
                                        $value = ($value == 1) ? 1 : 0;
                                }
                                if ($key == 'auto_away_enable') {
                                        $value = ($value == 1) ? 1 : 0;
                                }
                                if ($key == 'eco_mode') {
                                        $value = (($value == "manual-eco") || ($value == "auto-eco")) ? 1 : 0;
                                }
                                $changed = $this->checkAndUpdateCmd($key, $value) || $changed;
                        }
                        if (isset($device_info->target) && isset($device_info->target->temperature)) {
                                $temperatures = $device_info->target->temperature;
                                $temperature = (is_array($temperatures)) ? array_sum($temperatures) / count($temperatures) : $temperatures;
                                $changed = $this->checkAndUpdateCmd('order', $temperature) || $changed;
                        }
                        if (isset($device_info->scale)) {
                                $status['scale'] = $device_info->scale;
                        }
                        if (isset($device_info->current_state->ac)) {
                                $status['ac'] = $device_info->current_state->ac;
                        }
                        if (isset($device_info->current_state->battery_level)) {
                                $status['battery_level'] = $device_info->current_state->battery_level;
                        }
                        $this->setStatus($status);
                        if ($changed) {
                                $this->refreshWidget();
                        }
                }

        }

        public function toHtml($_version = 'dashboard') {
                if ($this->getConfiguration('nest_type') == 'thermostat') {
                        $replace = $this->preToHtml($_version);
                        if (!is_array($replace)) {
                                return $replace;
                        }
                        $version = jeedom::versionAlias($_version);
                        foreach ($this->getCmd() as $cmd) {
                                if ($cmd->getType() == 'info') {
                                        $replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
                                }
                        }
                        $thermostat = $this->getCmd(null, 'thermostat');
                        $replace['#thermostat_cmd_id#'] = $thermostat->getId();
                        $replace['#thermostat_maxValue#'] = $thermostat->getConfiguration('maxValue');
                        $replace['#thermostat_minValue#'] = $thermostat->getConfiguration('minValue');

                        $auto_away_on = $this->getCmd(null, 'auto_away_on');
                        $replace['#auto_away_on_id#'] = $auto_away_on->getId();

                        $auto_away_off = $this->getCmd(null, 'auto_away_off');
                        $replace['#auto_away_off_id#'] = $auto_away_off->getId();

                        $away_on = $this->getCmd(null, 'away_on');
                        $replace['#away_on_id#'] = $away_on->getId();

                        $away_off = $this->getCmd(null, 'away_off');
                        $replace['#away_off_id#'] = $away_off->getId();

                        $cool = $this->getCmd(null, 'cool');
                        $replace['#cool_id#'] = $cool->getId();

                        $off = $this->getCmd(null, 'off');
                        $replace['#off_id#'] = $off->getId();

                        $away_mode_on = $this->getCmd(null, 'away_mode_on');
                        $replace['#away_mode_on_id#'] = $away_mode_on->getId();

                        $away_mode_off = $this->getCmd(null, 'away_mode_off');
                        $replace['#away_mode_off_id#'] = $away_mode_off->getId();

                        $hot_water_on = $this->getCmd(null, 'hot_water_on');
                        $replace['#hot_water_on_id#'] = $hot_water_on->getId();

                        $hot_water_off = $this->getCmd(null, 'hot_water_off');
                        $replace['#hot_water_off_id#'] = $hot_water_off->getId();

                        $refresh = $this->getCmd(null, 'refresh');
                        if (is_object($refresh)) {
                                $replace['#refresh_id#'] = $refresh->getId();
                        }
                        $html = template_replace($replace, getTemplate('core', $version, 'nest', 'nest'));
                        cache::set('widgetHtml' . $_version . $this->getId(), $html, 0);
                        return $html;
                } else {
                        return parent::toHtml($_version);
                }
        }

        /*     * **********************Getteur Setteur*************************** */

        public function getCollectDate() {
                return $this->_collectDate;
        }

        public function setCollectDate($_collectDate) {
                $this->_collectDate = $_collectDate;
        }

}

class nestCmd extends cmd {
        /*     * *************************Attributs****************************** */

        public static $_widgetPossibility = array('custom' => false);

        /*     * ***********************Methode static*************************** */

        /*     * *********************Methode d'instance************************* */

        public function imperihomeGenerate($ISSStructure) {
                $eqLogic = $this->getEqLogic();
                $object = $eqLogic->getObject();
                if ($eqLogic->getConfiguration('nest_type') == 'protect') {
                        $info_device = array(
                                "id" => $this->getId(),
                                "name" => ($this->getName() == __('Etat', __FILE__)) ? $eqLogic->getName() : $this->getName(),
                                "room" => (is_object($object)) ? $object->getId() : 99999,
                                "type" => imperihome::convertType($this, $ISSStructure, true),
                                'params' => array(),
                        );
                        $cmd_params = imperihome::generateParam($this, $info_device['type'], $ISSStructure);
                        $info_device['params'] = $cmd_params['params'];
                        return $info_device;
                }

                $type = 'DevThermostat';
                $info_device = array(
                        'id' => $this->getId(),
                        'name' => $eqLogic->getName(),
                        'room' => (is_object($object)) ? $object->getId() : 99999,
                        'type' => $type,
                        'params' => array(),
                );
                $info_device['params'] = $ISSStructure[$info_device['type']]['params'];
                $info_device['params'][1]['value'] = '#' . $eqLogic->getCmd('info', 'temperature')->getId() . '#';
                $info_device['params'][2]['value'] = '#' . $eqLogic->getCmd('info', 'order')->getId() . '#';
                $info_device['params'][3]['value'] = 1;
                return $info_device;
        }

        public function imperihomeAction($_action, $_value) {
                $eqLogic = $this->getEqLogic();
                if ($_action == 'setSetPoint') {
                        $cmd = $eqLogic->getCmd('action', 'thermostat');
                        if (is_object($cmd)) {
                                $cmd->execCmd(array('slider' => $_value));
                        }
                }
        }

        public function imperihomeCmd() {
                $eqLogic = $this->getEqLogic();
                if ($eqLogic->getConfiguration('nest_type') == 'protect') {
                        return true;
                }
                if ($this->getLogicalId() == 'thermostat') {
                        return true;
                }
                return false;
        }

        public function execute($_options = null) {
                if ($this->getType() == 'info') {
                        return '';
                }
                $eqLogic = $this->getEqLogic();
                $nest_api = nest::getNestApi();
                if ($this->getLogicalId() == 'thermostat') {
                        $nest_api->setTargetTemperature(round($_options['slider'] * 2) / 2);
                        sleep(1);
                }
                if ($this->getLogicalId() == 'fan_mode_on') {
                        $nest_api->setFanMode(FAN_MODE_ON, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'fan_mode_off') {
                        $nest_api->setFanMode(FAN_MODE_OFF, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'off') {
                        $nest_api->turnOff($eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'away_on') {
                        $nest_api->setAway(TRUE, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'away_off') {
                        $nest_api->setAway(FALSE, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'auto_away_on') {
                        $nest_api->setAutoAwayEnabled(TRUE, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'auto_away_off') {
                        $nest_api->setAutoAwayEnabled(FALSE, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'away_mode_on') {
                        $nest_api->setEcoMode(ECO_MODE_MANUAL, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'away_mode_off') {
                        $nest_api->setEcoMode(ECO_MODE_SCHEDULE, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'cool') {
                        $nest_api->setTargetTemperatureMode(TARGET_TEMP_MODE_HEAT, NULL, $eqLogic->getLogicalId());
                        sleep(1);
                }
                if ($this->getLogicalId() == 'hot_water_on') {
                        $nest_api->setHotWaterBoost(3600, $eqLogic->getLogicalId());
                        sleep(3);
                }
                if ($this->getLogicalId() == 'hot_water_off') {
                        $nest_api->cancelHotWaterBoost($eqLogic->getLogicalId());
                        sleep(3);
                }
                $eqLogic->updateFromNest();
                $eqLogic->save();
                return '';
        }

        /*     * **********************Getteur Setteur*************************** */
}
