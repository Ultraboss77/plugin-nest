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
require_once dirname(__FILE__) . '/../../core/php/nest.inc.php';

class nest extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function pull() {
        foreach (nest::byType('nest') as $eqLogic) {
            if ($eqLogic->getIsEnable() == 1) {
                $eqLogic->updateFromNest();
                $eqLogic->save();
            }
        }
    }

    public static function getNestApi() {
        if (config::byKey('username', 'nest') == '' || config::byKey('password', 'nest') == '') {
            throw new Exception(__('Aucun nom d\'utilisateur ou mot de passe défini', __FILE__));
        }
        return new nest_api(config::byKey('username', 'nest'), config::byKey('password', 'nest'));
    }

    public static function syncWithNest() {
        $nest_api = self::getNestApi();
        foreach ($nest_api->getDevices() as $thermostat) {
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

            $temperature = $eqLogic->getCmd(null, 'temperature');
            if (!is_object($temperature)) {
                $temperature = new nestCmd();
                $temperature->setLogicalId('temperature');
                $temperature->setIsVisible(1);
                $temperature->setName(__('Température', __FILE__));
                $temperature->setOrder(6);
            }
            $temperature->setUnite('°C');
            $temperature->setType('info');
            $temperature->setSubType('numeric');
            $temperature->setEventOnly(1);
            $temperature->setEqLogic_id($eqLogic->getId());
            $temperature->save();

            $humidity = $eqLogic->getCmd(null, 'humidity');
            if (!is_object($humidity)) {
                $humidity = new nestCmd();
                $humidity->setLogicalId('humidity');
                $humidity->setIsVisible(1);
                $humidity->setName(__('Humidité', __FILE__));
                $humidity->setOrder(7);
            }
            $humidity->setType('info');
            $humidity->setSubType('numeric');
            $humidity->setUnite('%');
            $humidity->setEventOnly(1);
            $humidity->setEqLogic_id($eqLogic->getId());
            $humidity->save();

            $heat = $eqLogic->getCmd(null, 'heat');
            if (!is_object($heat)) {
                $heat = new nestCmd();
                $heat->setLogicalId('heat');
                $heat->setIsVisible(0);
                $heat->setOrder(2);
                $heat->setName(__('Chauffage', __FILE__));
            }
            $heat->setType('info');
            $heat->setSubType('binary');
            $heat->setEventOnly(1);
            $heat->setEqLogic_id($eqLogic->getId());
            $heat->save();

            $auto_away = $eqLogic->getCmd(null, 'auto_away');
            if (!is_object($auto_away)) {
                $auto_away = new nestCmd();
                $auto_away->setLogicalId('auto_away');
                $auto_away->setIsVisible(1);
                $auto_away->setName(__('Absence automatique', __FILE__));
                $auto_away->setOrder(5);
            }
            $auto_away->setType('info');
            $auto_away->setSubType('binary');
            $auto_away->setEventOnly(1);
            $auto_away->setEqLogic_id($eqLogic->getId());
            $auto_away->save();

            $manual_away = $eqLogic->getCmd(null, 'manual_away');
            if (!is_object($manual_away)) {
                $manual_away = new nestCmd();
                $manual_away->setLogicalId('manual_away');
                $manual_away->setIsVisible(0);
                $manual_away->setName(__('Absence', __FILE__));
                $manual_away->setOrder(4);
            }
            $manual_away->setType('info');
            $manual_away->setSubType('binary');
            $manual_away->setEventOnly(1);
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
            $order->setEventOnly(1);
            $order->setUnite('°C');
            $order->setEqLogic_id($eqLogic->getId());
            $order->save();

            $thermostat = $eqLogic->getCmd(null, 'thermostat');
            if (!is_object($thermostat)) {
                $thermostat = new nestCmd();
                $thermostat->setLogicalId('thermostat');
                $thermostat->setIsVisible(1);
                $thermostat->setName(__('Thermostat', __FILE__));
                $thermostat->setTemplate('dashboard', 'thermostat');
                $thermostat->setTemplate('mobile', 'thermostat');
                $thermostat->setOrder(8);
            }
            $thermostat->setType('action');
            $thermostat->setSubType('slider');
            $thermostat->setConfiguration('minValue', 16);
            $thermostat->setConfiguration('maxValue', 28);
            $thermostat->setEqLogic_id($eqLogic->getId());
            $thermostat->setValue($order->getId());
            $thermostat->setUnite('°C');
            $thermostat->save();

            $away_on = $eqLogic->getCmd(null, 'away_on');
            if (!is_object($away_on)) {
                $away_on = new nestCmd();
                $away_on->setLogicalId('away_on');
                $away_on->setIsVisible(1);
                $away_on->setName(__('Absent', __FILE__));
                $away_on->setTemplate('dashboard', 'nest_away');
                $away_on->setTemplate('mobile', 'nest_away');
                $away_on->setOrder(11);
            }
            $away_on->setType('action');
            $away_on->setSubType('other');
            $away_on->setValue($manual_away->getId());
            $away_on->setEqLogic_id($eqLogic->getId());
            $away_on->save();

            $away_off = $eqLogic->getCmd(null, 'away_off');
            if (!is_object($away_off)) {
                $away_off = new nestCmd();
                $away_off->setLogicalId('away_off');
                $away_off->setIsVisible(1);
                $away_off->setName(__('Présent', __FILE__));
                $away_off->setTemplate('dashboard', 'nest_away');
                $away_off->setTemplate('mobile', 'nest_away');
                $away_off->setOrder(12);
            }
            $away_off->setType('action');
            $away_off->setSubType('other');
            $away_off->setValue($manual_away->getId());
            $away_off->setEqLogic_id($eqLogic->getId());
            $away_off->save();

            $eqLogic->updateFromNest();
            $eqLogic->save();
        }
        foreach ($nest_api->getDevices(DEVICE_TYPE_PROTECT) as $protects) {
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
            $cmd->setEventOnly(1);
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
            $cmd->setEventOnly(1);
            $cmd->setDisplay('invertBinary', 1);
            $cmd->setEqLogic_id($eqLogic->getId());
            $cmd->save();
            $eqLogic->updateFromNest();
            $eqLogic->save();
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function updateFromNest() {
        try {
            $nest_api = nest::getNestApi();
            $device_info = $nest_api->getDeviceInfo($this->getLogicalId());
        } catch (Exception $e) {
            log::add('nest', 'error', __('Erreur sur ', __FILE__) . $this->getName() . ' : ' . $e->getMessage());
            return;
        }
        if (isset($device_info->network)) {
            if (isset($device_info->network->local_ip)) {
                $this->setConfiguration('local_ip', $device_info->network->local_ip);
            }
            if (isset($device_info->network->mac_address)) {
                $this->setConfiguration('local_mac', $device_info->network->mac_address);
            }
        }

        /*         * ********************PROTECT NEST********************** */
        if ($this->getConfiguration('nest_type') == 'protect') {
            $cmd = $this->getCmd(null, 'co_status');
            if (is_object($cmd)) {
                if ($cmd->execCmd() === '' || $cmd->execCmd() != $cmd->formatValue($device_info->co_status)) {
                    $cmd->setCollectDate('');
                    $cmd->event($device_info->co_status);
                }
            }
            $cmd = $this->getCmd(null, 'smoke_status');
            if (is_object($cmd)) {
                if ($cmd->execCmd() === '' || $cmd->execCmd() != $cmd->formatValue($device_info->smoke_status)) {
                    $cmd->setCollectDate('');
                    $cmd->event($device_info->smoke_status);
                }
            }
            $this->setConfiguration('battery_level', $device_info->battery_level);
            $this->setConfiguration('battery_health_state', $device_info->battery_health_state);
            $this->setConfiguration('replace_by_date', $device_info->replace_by_date);
            $this->setConfiguration('last_update', $device_info->last_update);
            $this->setConfiguration('last_manual_test', $device_info->last_manual_test);
            $testOk = true;
            foreach ($device_info->tests_passed as $key => $value) {
                $this->setConfiguration('test_' . $key, $value);
                if ($value != 1) {
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
            if (isset($device_info->network)) {
                if (isset($device_info->network->wan_ip)) {
                    $this->setConfiguration('wan_ip', $device_info->network->wan_ip);
                }
                if (isset($device_info->network->last_connection)) {
                    $this->setConfiguration('last_connection', $device_info->network->last_connection);
                }
            }
            if (isset($device_info->network)) {
                if (isset($device_info->current_state->ac)) {
                    $this->setConfiguration('ac', $device_info->current_state->ac);
                }
                if (isset($device_info->current_state->battery_level)) {
                    $this->setConfiguration('battery_level', $device_info->current_state->battery_level);
                }
                foreach ($device_info->current_state as $key => $value) {
                    $cmd = $this->getCmd(null, $key);
                    if (is_object($cmd) && ($cmd->execCmd() === '' || $cmd->execCmd() != $cmd->formatValue($value))) {
                        $cmd->setCollectDate('');
                        $cmd->event($value);
                    }
                }
            }
            if (isset($device_info->target) && isset($device_info->target->temperature)) {
                $temperatures = $device_info->target->temperature;
                $order = $this->getCmd(null, 'order');
                if (is_object($order)) {
                    if (is_array($temperatures)) {
                        $temperature = array_sum($temperatures) / count($temperatures);
                    } else {
                        $temperature = $temperatures;
                    }
                    if ($order->execCmd() === '' || $order->execCmd() != $order->formatValue($temperature)) {
                        $order->setCollectDate('');
                        $order->event(round($temperature, 1));
                    }
                }
            }
        }
    }

    /*     * **********************Getteur Setteur*************************** */
}

class nestCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function execute($_options = null) {
        if ($this->getType() == 'info') {
            return '';
        }
        $eqLogic = $this->getEqLogic();
        $nest_api = nest::getNestApi();
        if ($this->getLogicalId() == 'thermostat') {
            $nest_api->setTargetTemperature($_options['slider'], $eqLogic->getLogicalId());
        }
        if ($this->getLogicalId() == 'fan_mode_on') {
            $nest_api->setFanMode(FAN_MODE_ON, $eqLogic->getLogicalId());
        }
        if ($this->getLogicalId() == 'fan_mode_off') {
            $nest_api->setFanMode(FAN_MODE_OFF, $eqLogic->getLogicalId());
        }
        if ($this->getLogicalId() == 'off') {
            $nest_api->turnOff($eqLogic->getLogicalId());
        }
        if ($this->getLogicalId() == 'away_on') {
            $nest_api->setAway(AWAY_MODE_ON, $eqLogic->getLogicalId());
        }
        if ($this->getLogicalId() == 'away_off') {
            $nest_api->setAway(AWAY_MODE_OFF, $eqLogic->getLogicalId());
        }
        if ($this->getLogicalId() == 'auto_away_on') {
            $nest_api->setAutoAwayEnabled(true, $eqLogic->getLogicalId());
        }
        if ($this->getLogicalId() == 'auto_away_off') {
            $nest_api->setAutoAwayEnabled(false, $eqLogic->getLogicalId());
        }
        $eqLogic->updateFromNest();
        $eqLogic->save();
        return '';
    }

    /*     * **********************Getteur Setteur*************************** */
}
