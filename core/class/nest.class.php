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

	private $_collectDate = '';
	public static $_widgetPossibility = array('custom' => true);

	/*     * ***********************Methode static*************************** */

	public static function cron5() {
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
		$devices = $nest_api->getDevices();
		log::add('nest', 'debug', 'NEST themrostat : ' . print_r($devices, true));
           foreach ($devices as $thermostat){
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
				$manual_away->setName(__('Fonction Chez moi/ Absent', __FILE__));
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
			$thermostat->setConfiguration('minValue', 09);
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
				$auto_away_off->setName(__('Températures éco désactivées', __FILE__));
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
				$auto_away_on->setName(__('Températures éco activées', __FILE__));
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
				$eco_mode->setName(__('Mode eco', __FILE__));
			}
			$eco_mode->setType('info');
			$eco_mode->setSubType('other');
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
			$leaf->setSubType('numeric');
			$leaf->setEqLogic_id($eqLogic->getId());
			$leaf->save();
			
			$mode = $eqLogic->getCmd(null, 'mode');
			if (!is_object($mode)) {
				$mode = new nestCmd();
				$mode->setLogicalId('mode');
				$mode->setIsVisible(1);
				$mode->setName(__('mode', __FILE__));
			}
			$mode->setType('info');
			$mode->setSubType('other');
			$mode->setEqLogic_id($eqLogic->getId());
			$mode->save();
			
			$away_mode_on = $eqLogic->getCmd(null, 'away_mode_on');
			if (!is_object($away_mode_on)) {
				$away_mode_on = new nestCmd();
				$away_mode_on->setLogicalId('away_mode_on');
				$away_mode_on->setIsVisible(1);
				$away_mode_on->setName(__('Mode éco activées', __FILE__));
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
				$away_mode_off->setName(__('Mode éco désactivées', __FILE__));
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
			$online->setSubType('other');
			$online->setEqLogic_id($eqLogic->getId());
			$online->save();
			
			$scale = $eqLogic->getCmd(null, 'scale');
			if (!is_object($scale)) {
				$scale = new nestCmd();
				$scale->setLogicalId('scale');
				$scale->setIsVisible(1);
				$scale->setName(__('Scale', __FILE__));
			}
			$scale->setType('info');
			$scale->setSubType('other');
			$scale->setEqLogic_id($eqLogic->getId());
			$scale->save();
			
			$eqLogic->updateFromNest();
			$eqLogic->save();
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
			$eqLogic->updateFromNest();
			$eqLogic->save();
		}
    }    
	/*    * *********************Methode d'instance************************* */

	public function updateFromNest() {
		try {
			$nest_api = nest::getNestApi();
			$device_info = $nest_api->getDeviceInfo($this->getLogicalId());
			if ($this->getConfiguration('nestNumberFailed', 0) > 0) {
				$this->setConfiguration('nestNumberFailed', 0);
				$this->save();
			}
		} catch (Exception $e) {
			if ($this->getConfiguration('nestNumberFailed', 0) > 3) {
				log::add('nest', 'error', __('Erreur sur ', __FILE__) . $this->getHumanName() . ' (' . $this->getConfiguration('nestNumberFailed', 0) . ') : ' . $e->getMessage());
			} else {
				$this->setConfiguration('nestNumberFailed', $this->getConfiguration('nestNumberFailed', 0) + 1);
				$this->save();
			}
			return;
		}
		log::add('nest', 'debug', print_r($device_info, true));
		
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
			$battery_max = 5500;
			$battery_min = 3600;
			$battery = round(($device_info->battery_level - $battery_min) / ($battery_max - $battery_min) * 100, 0);
			if ($battery < 0) {
				$battery = 0;
			}
			$this->batteryStatus($battery);
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
				if (isset($device_info->network->online)) {
					$this->setConfiguration('online', $device_info->network->online);
				}
				$last_connection =  $device_info->network->last_connection;
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
					if ($key == 'auto_away') {
						if ($value == -1) {
							$value = 0;
						} else {
							$value = 1;
						}
					}
					if ($key == 'manual_away') {
						if ($value == 1) {
							$value = 1;
						} else {
							$value = 0;
						}
					}
					if ($key == 'auto_away_enable') {
						if ($value == 1) {
							$value = 1;
						} else {
							$value = 0;
						}
					}
					if ($key == 'eco_mode') {
						if ($value == "manual-eco" ) {
							$value = 1;
						} else {
							$value = 0;
						}
					}

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
		$this->refreshWidget();
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
			sleep(5);
		}
		if ($this->getLogicalId() == 'fan_mode_on') {
			$nest_api->setFanMode(FAN_MODE_ON, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'fan_mode_off') {
			$nest_api->setFanMode(FAN_MODE_OFF, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'off') {
			$nest_api->turnOff($eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'away_on') {
			$nest_api->setAway(TRUE, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'away_off') {
			$nest_api->setAway(FALSE, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'auto_away_on') {
			$nest_api->setAutoAwayEnabled(TRUE, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'auto_away_off') {
			$nest_api->setAutoAwayEnabled(FALSE, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'away_mode_on') {
			$nest_api->setAway(TRUE, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'away_mode_off') {
			$nest_api->setAway(FALSE, $eqLogic->getLogicalId());
			sleep(5);
		}
		if ($this->getLogicalId() == 'cool') {
			$nest_api->setTargetTemperatureMode(TARGET_TEMP_MODE_HEAT, NULL , $eqLogic->getLogicalId());
			sleep(5);
		}
		$eqLogic->updateFromNest();
		$eqLogic->save();
		return '';
	}

	/*     * **********************Getteur Setteur*************************** */
}
