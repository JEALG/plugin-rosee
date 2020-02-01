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

class rosee extends eqLogic {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */
	public static function cron5() {
		foreach (eqLogic::byType('rosee') as $rosee) {
			if ($rosee->getIsEnable()) {
				log::add('rosee', 'debug', '================= CRON 5 ==================');
				$rosee->getInformations();
			}
		}
	}

	public static function cron30($_eqlogic_id = null) {
		//no both cron5 and cron30 enabled:
		if (config::byKey('functionality::cron5::enable', 'rosee', 0) == 1) {
			config::save('functionality::cron30::enable', 0, 'rosee');
			return;
		}
		foreach (eqLogic::byType('rosee') as $rosee) {
			if ($rosee->getIsEnable()) {
				log::add('rosee', 'debug', '================= CRON 30 =================');
				$rosee->getInformations();
			}
		}
	}

	/*     * *********************Methode d'instance************************* */
	public function refresh() {
        foreach ($this->getCmd() as $cmd)
        {
            $s = print_r($cmd, 1);
            log::add('rosee', 'debug', 'refresh  cmd: '.$s);
            $cmd->execute();
        }
    }

	public function preUpdate() {
		if ($this->getConfiguration('temperature') == '') {
			throw new Exception(__('Le champ temperature ne peut etre vide',__FILE__));
		}

		if ($this->getConfiguration('humidite') == '') {
			throw new Exception(__('Le champ humidite ne peut etre vide',__FILE__));
		}
	}

	public function postInsert() {
		// Ajout d'une commande dans le tableau pour le point de rosée
            $roseeCmd = new roseeCmd();
            $roseeCmd->setName(__('Point de rosée', __FILE__));
            $roseeCmd->setEqLogic_id($this->id);
            $roseeCmd->setLogicalId('rosee');
            $roseeCmd->setConfiguration('data', 'rosee_point');
            $roseeCmd->setType('info');
            $roseeCmd->setSubType('numeric');
            $roseeCmd->setUnite('°C');
            $roseeCmd->setIsHistorized(0);
            $roseeCmd->setIsVisible(1);
            $roseeCmd->setDisplay('generic_type','');
            $roseeCmd->save();

		// Ajout d'une commande dans le tableau pour le point de givrage
            $frostCmd = new roseeCmd();
            $frostCmd->setName(__('Point de givrage', __FILE__));
            $frostCmd->setEqLogic_id($this->id);
            $frostCmd->setLogicalId('givrage');
            $frostCmd->setConfiguration('data', 'frost_point');
            $frostCmd->setType('info');
            $frostCmd->setSubType('numeric');
            $frostCmd->setUnite('°C');
            $frostCmd->setIsHistorized(0);
            $frostCmd->setIsVisible(1);
            $frostCmd->setDisplay('generic_type','');
            $frostCmd->save();

		// Ajout d'une commande dans le tableau pour l'alerte rosée
            $AlertRoseeCmd = new roseeCmd();
            $AlertRoseeCmd->setName(__('Alerte rosée', __FILE__));
            $AlertRoseeCmd->setEqLogic_id($this->id);
            $AlertRoseeCmd->setLogicalId('alerte_rosee');
            $AlertRoseeCmd->setConfiguration('data', 'alert_r');
            $AlertRoseeCmd->setType('info');
            $AlertRoseeCmd->setSubType('binary');
            $AlertRoseeCmd->setUnite('');
            $AlertRoseeCmd->setIsHistorized(0);
            $AlertRoseeCmd->setIsVisible(1);
            $AlertRoseeCmd->setDisplay('generic_type','GENERIC_INFO');
            $AlertRoseeCmd->save();

		// Ajout d'une commande dans le tableau pour l'alerte givrage
            $AlertGivreCmd = new roseeCmd();
            $AlertGivreCmd->setName(__('Alerte givre', __FILE__));
            $AlertGivreCmd->setEqLogic_id($this->id);
            $AlertGivreCmd->setLogicalId('alerte_givre');
            $AlertGivreCmd->setConfiguration('data', 'alert_g');
            $AlertGivreCmd->setType('info');
            $AlertGivreCmd->setSubType('binary');
            $AlertGivreCmd->setUnite('');
            $AlertGivreCmd->setIsHistorized(0);
            $AlertGivreCmd->setIsVisible(1);
            $AlertGivreCmd->setDisplay('generic_type','GENERIC_INFO');
            $AlertGivreCmd->save();
        
		// Ajout d'une commande dans le tableau pour l'humidité absolue
            $AbsHumiCmd = new roseeCmd();
            $AbsHumiCmd->setName(__('Humidité absolue', __FILE__));
            $AbsHumiCmd->setEqLogic_id($this->id);
            $AbsHumiCmd->setLogicalId('humidite_absolue');
            $AbsHumiCmd->setConfiguration('data', 'humidite_a');
            $AbsHumiCmd->setType('info');
            $AbsHumiCmd->setSubType('numeric');
            $AbsHumiCmd->setUnite('g/m3');
            $AbsHumiCmd->setIsHistorized(0);
            $AbsHumiCmd->setIsVisible(1);
            $AbsHumiCmd->setDisplay('generic_type','WEATHER_HUMIDITY');
            $AbsHumiCmd->save();

        // Ajout d'une commande message
            $givre_infosCmd = new roseeCmd();
            $givre_infosCmd->setName(__('Message Alerte givre', __FILE__));
            $givre_infosCmd->setEqLogic_id($this->id);
            $givre_infosCmd->setLogicalId('message_givre');
            $givre_infosCmd->setConfiguration('data', 'message_givre');
            $givre_infosCmd->setUnite('');
            $givre_infosCmd->setType('info');
            $givre_infosCmd->setSubType('string');
            $givre_infosCmd->setIsHistorized(0);
            $givre_infosCmd->setIsVisible(0);
            $givre_infosCmd->setDisplay('generic_type','WEATHER_CONDITION');
            $givre_infosCmd->save();
	}

	public function postSave(){
        $refresh = $this->getCmd(null, 'refresh');
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

	/*  **********************Getteur Setteur*************************** */
	public function postUpdate() {
		foreach (eqLogic::byType('rosee') as $rosee) {
				$rosee->getInformations();
		}
	}

	public function getInformations() {
        if (!$this->getIsEnable()) return;
        $_eqName = $this->getName();
        log::add('rosee', 'debug', '┌───────── CONFIGURATION EQUIPEMENT : '.$_eqName );
        
        /*  ********************** TEMPERATURE *************************** */
            $idvirt = str_replace("#","",$this->getConfiguration('temperature'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $temperature = $cmdvirt->execCmd();
                log::add('rosee', 'debug', '│ Temperature : ' . $temperature.' °C');
            } else {
                log::add('rosee', 'error', '│ Configuration : temperature non existante : ' . $this->getConfiguration('temperature'));
            }
      
        /*  ********************** HUMIDITE *************************** */
            $idvirt = str_replace("#","",$this->getConfiguration('humidite'));
            $cmdvirt = cmd::byId($idvirt);
            if (is_object($cmdvirt)) {
                $humidite = $cmdvirt->execCmd();
                log::add('rosee', 'debug', '│ Humidite : ' . $humidite.' %');
            } else {
                log::add('rosee', 'error', '│ Configuration : humidite non existante : ' . $this->getConfiguration('humidite'));
            }

        /*  ********************** PRESSION *************************** */
            $pression = $this->getConfiguration('pression');
            if ($pression == '') {
                //valeur par défaut de la pression atmosphérique : 1013.25 hPa
                    $pression=1013.25;
                    log::add('rosee', 'debug', '│ Pression aucun équipement de sélectionner ');
                    log::add('rosee', 'debug', '│ Pression par défaut : ' . $pression. ' hPa');
            } else {
                $idvirt = str_replace("#","",$this->getConfiguration('pression'));
                $cmdvirt = cmd::byId($idvirt);
                if (is_object($cmdvirt)) {
                    $pression = $cmdvirt->execCmd();
                    log::add('rosee', 'debug', '│ Pression : ' . $pression.' hPa');
                } else {
                    log::add('rosee', 'error', '│ Configuration : Pression non existante : ' . $this->getConfiguration('pression'));
                }
            }
		 
        /*  ********************** SEUIL D'ALERTE ROSEE *************************** */          
            $dpr=$this->getConfiguration('DPR');
            if ($dpr == '') {
                //valeur par défaut du seuil d'alerte rosée = 2°C
                $dpr=2.0;
                log::add('rosee', 'debug', '│ Seuil DPR Aucune valeur de saisie');
                log::add('rosee', 'debug', '│ Seuil DPR par défaut : ' . $dpr.' °C');       
		      } else {
                log::add('rosee', 'debug', '│ Seuil DPR : ' . $dpr.' °C'); 
            }
                log::add('rosee', 'debug', '└─────────');
        
        // Calcul de l'humidité absolue
            $terme_pvs1 = 2.7877 + (7.625 * $temperature) / (241.6 + $temperature);
            $pvs = pow(10,$terme_pvs1);                                             // pression de saturation de la vapeur d'eau
            $pv = ($humidite * $pvs) / 100.0;                                       // pression partielle de vapeur d'eau
            $pression = $pression * 100.0;                                          // conversion de la pression en Pa
            $humi_a = 0.622 * ($pv / ($pression - $pv));                            // Humidité absolue en kg d'eau par kg d'air
            $v = (461.24 * (0.622 + $humi_a) * ($temperature+273.15)) / $pression;  // Volume specifique en m3 / kg
            $p = 1.0 / $v;                                                          // Poids spécifique en kg / m3
            $humi_a_m3 = 1000.0 * $humi_a * $p;                                     // Humidité absolue en gr / m3
            $humi_a_m3 = round(($humi_a_m3), 1);                                    // Humidité absolue en gr / m3 (1 chiffre après la virgule)
                
                log::add('rosee', 'debug', '┌───────── CALCUL DE L HUMIDITE ABSOLUE : '.$_eqName);
                log::add('rosee', 'debug', '│ terme_pvs1 : ' . $terme_pvs1);
                log::add('rosee', 'debug', '│ pvs : ' . $pvs);
                log::add('rosee', 'debug', '│ pv : ' . $pv);
                log::add('rosee', 'debug', '│ Pression : ' . $pression.' Pa');
                log::add('rosee', 'debug', '│ humi_a : ' . $humi_a);
                log::add('rosee', 'debug', '│ v : ' . $v);
                log::add('rosee', 'debug', '│ p : ' . $p);
                log::add('rosee', 'debug', '│ Humidite Absolue : ' . $humi_a_m3.' g/m3');
                log::add('rosee', 'debug', '└─────────');
        
		/* calcul du point de rosee
			paramètres de MAGNUS pour l'air saturé (entre -45°C et +60°C) :
			alpha  = 6.112 hPa
			beta   = 17.62
			lambda = 243.12 °C
		*/
            log::add('rosee', 'debug', '┌───────── CALCUL DU POINT DE ROSEE : '.$_eqName);
                $alpha = 6.112;
                    log::add('rosee', 'debug', '│ alpha : ' . $alpha );
                $beta = 17.62;
                    log::add('rosee', 'debug', '│ beta : ' . $beta );
                $lambda = 243.12;
                    log::add('rosee', 'debug', '│ Lambda : ' . $lambda );
                $Terme1 = log($humidite/100);
                    log::add('rosee', 'debug', '│ Terme1 : ' . $Terme1 );
                $Terme2 = ($beta * $temperature) / ($lambda + $temperature);
                    log::add('rosee', 'debug', '│ Terme2 : ' . $Terme2 );
                $rosee = $lambda * ($Terme1 + $Terme2) / ($beta - $Terme1 - $Terme2);
                    log::add('rosee', 'debug', '│ rosee : ' . $rosee );
                $rosee_point = round(($rosee), 1);
        
            
            log::add('rosee', 'debug', '│ Point de Rosée : ' . $rosee_point);
        
        // Calcul de l'alerte rosée en fonction du seuil d'alerte
            $frost_alert_rosee = $temperature - $rosee_point;
                log::add('rosee', 'debug', '│ Calcul point de rosee (Température - point de Rosée) : ' . $frost_alert_rosee );
        
                if ($frost_alert_rosee <= $dpr) {
                    $alert_r = 1;
                    log::add('rosee', 'debug', '│ RESULTAT Calcul point de rosee (Calcul point de Rosée  <= Seuil DPR)');
                } else {
                    $alert_r = 0;
                    log::add('rosee', 'debug', '│ RESULTAT Calcul point de rosee (Calcul point de Rosée  > Seuil DPR)');
                }
        
                    log::add('rosee', 'debug', '│ Etat alerte rosée : ' . $alert_r);
        
            //Mise à jour de l'équipement
                  $cmd = $this->getCmd('info', 'alerte_rosee');
                  if(is_object($cmd)) {
                      $cmd->setConfiguration('value', $alert_r);
                      $cmd->save();
                      $cmd->setCollectDate('');
                      $cmd->event($alert_r);
                        log::add('rosee', 'debug', '│ Mise à jour Valeur de l\'équipement avec la valeur : ' . $alert_r);
                  };
        
            log::add('rosee', 'debug', '└─────────');
        
        

		/* calcul du point de givrage
		*/
            $temp_kelvin = $temperature + 273.15;
            $rosee_kelvin = $rosee + 273.15;
            $frost_kelvin = 2954.61 / $temp_kelvin;
            $frost_kelvin = $frost_kelvin + 2.193665 * log($temp_kelvin);
            $frost_kelvin = $frost_kelvin - 13.3448;
            $frost_kelvin = 2671.02 / $frost_kelvin;
            $frost_kelvin = $frost_kelvin + $rosee_kelvin - $temp_kelvin;
            $frost = $frost_kelvin -273.15;
            $frost_point = round(($frost), 1);
        
            log::add('rosee', 'debug', '┌───────── CALCUL DU POINT DE GIVRAGE : '.$_eqName);
            log::add('rosee', 'debug', '│ Point de Givrage :' . $frost_point.' °C');

        // Calcul de l'alerte givrage en fonction du seuil d'alerte
            $frost_alert_givrage = $temperature - $frost_point;
                log::add('rosee', 'debug', '│ Calcul point de givrage (Température - point de givrage) : ' . $frost_alert_givrage);
            if ($frost_alert_givrage <= $dpr) {
                $alert_g = 1;
                    log::add('rosee', 'debug', '│ RESULTAT Calcul point de givrage (Calcul point de givrage  <= Seuil DPR)');
                    log::add('rosee', 'debug', '│ ┌───────── Désactivation Alerte Point de rosée : ');
                $alert_r = 0;
                    log::add('rosee', 'debug', '│ │Etat alerte rosée : ' . $alert_r);
                    log::add('rosee', 'debug', '│ └─────────');
            } else {
                $alert_g = 0;
                    log::add('rosee', 'debug', '│ RESULTAT Calcul point de givrage (Calcul point de givrage  > Seuil DPR)');
            };
        
                    log::add('rosee', 'debug', '│ Etat alerte gel : ' . $alert_g);
            $cmd = $this->getCmd('info', 'alerte_givre');
            if (is_object($cmd)) {
                $cmd->setConfiguration('value', $alert_g);
                $cmd->save();
                $cmd->setCollectDate('');
                $cmd->event($alert_g);
                    log::add('rosee', 'debug', '│ Mise à jour Valeur de l\'équipement avec la valeur : ' . $alert_g);
            };
                    log::add('rosee', 'debug', '└─────────');
        
        // Message Givrage
            // Explication des cas
                log::add('rosee', 'debug', '┌───────── MESSAGE : '.$_eqName);
                
                // Cas 1
                    $message_givre_1 = 'Givre, Présence de givre';
                        log::add('rosee', 'debug', '│ ┌───────── CAS 1 : ' .$message_givre_1);
                        log::add('rosee', 'debug', '│ │ Calcul     : (Température <=1 et Point de Givrage <= 0) et (Humidité absolue en (gr/m3) > Seuil DPR)');
                        log::add('rosee', 'debug', '│ │ Résultat : (' .$temperature .' <= 1 et ' .$frost_point .' <=0) et (' .$humi_a_m3 .' > ' . $dpr .')');
                        log::add('rosee', 'debug', '│ └─────────');
                
                // Cas 2
                    $message_givre_2 = 'Givre peu probable malgré la température';    
                        log::add('rosee', 'debug', '│ ┌───────── CAS 2 : '.$message_givre_2);
                        log::add('rosee', 'debug', '│ │ Calcul    : (Température <=1 et Point de Givrage <= 0) et (Humidité absolue en (gr/m3) < Seuil DPR)');
                        log::add('rosee', 'debug', '│ │ Résultat : (' .$temperature .' <= 1 et ' .$frost_point .' <=0) et (' .$humi_a_m3 .' < ' . $dpr .')');
                        log::add('rosee', 'debug', '│ └─────────');
                
                // Cas 3
                    $message_givre_3 = 'Risque de givre';
                        log::add('rosee', 'debug', '│ ┌───────── CAS 3 : '.$message_givre_3);
                        log::add('rosee', 'debug', '│ │ Calcul    : (Température <=4 et Point de Givrage <= 0.5)');
                        log::add('rosee', 'debug', '│ │ Résultat : (' .$temperature .' <= 4 et ' .$frost_point .' <=0.5)');
                        log::add('rosee', 'debug', '│ └─────────');
        
                // Cas 4
                    $message_givre_4 = 'Aucun risque de Givre';
                        log::add('rosee', 'debug', '│ ┌───────── CAS 4 : '.$message_givre_4);
                        log::add('rosee', 'debug', '│ │ Aucun risque de Givre');
                        log::add('rosee', 'debug', '│ └─────────');                
                
            // Cas Actuel
                if($temperature <= 1 && $frost_point <= 0) {
                    if ($humi_a_m3 > $dpr) {
                        // Cas 1
                            $message_givre_Cas = 'CAS 1';
                            $message_givre = $message_givre_1;
                    };
                    if ($humi_a_m3 < $dpr) {
                        // Cas 2
                            $message_givre_Cas = 'CAS 2';
                            $message_givre = $message_givre_2;
                    };

                 } elseif ($temperature <= 4 && $frost_point <= 0.5) {
                        // Cas 3
                            $message_givre_Cas = 'CAS 3';
                            $message_givre = $message_givre_3;
                } else {
                        // Cas 4
                            $message_givre_Cas = 'CAS 4';
                            $message_givre = $message_givre_4;
                 };
                    log::add('rosee', 'debug', '│ ┌───────── CAS ACTUEL : ' .$message_givre_Cas);
                    log::add('rosee', 'debug', '│ │ Message : ' .$message_givre );
                    log::add('rosee', 'debug', '│ └─────────');
        
            //Mise à jour de l'équipement
                    log::add('rosee', 'debug', '│ ┌───────── Mise à jour Message');
                  
                $cmd = $this->getCmd('info', 'message_givre');
                  if(is_object($cmd)) {
                      $cmd->setConfiguration('value', $message_givre);
                      $cmd->save();
                      $cmd->setCollectDate('');
                      $cmd->event($message_givre);
                  };
                    log::add('rosee', 'debug', '│ │ Mise à jour Valeur de l\'équipement avec la valeur : ' . $message_givre);
                    log::add('rosee', 'debug', '│ └─────────');
                    log::add('rosee', 'debug', '└─────────');
        
        log::add('rosee', 'debug', '┌───────── MISE A JOUR : '.$_eqName);

        //Mise à jour de l'équipement Alerte rosée
            $cmd = $this->getCmd('info', 'alerte_rosee');
            if(is_object($cmd)) {
                $cmd->setConfiguration('value', $alert_r);
                $cmd->save();
                $cmd->setCollectDate('');
                $cmd->event($alert_r);
                log::add('rosee', 'debug', '│ Alerte Rosée : ' . $alert_r);
            }
        
        //Mise à jour de l'équipement Humidité absolue
            $cmd = $this->getCmd('info', 'humidite_absolue');
            if(is_object($cmd)) {
                $cmd->setConfiguration('value', $humi_a_m3);
                $cmd->save();
                $cmd->event($humi_a_m3);
                    log::add('rosee', 'debug', '│ Humidite Absolue : ' . $humi_a_m3.' g/m3');
            }
        
        //Mise à jour de l'équipement Givrage
            $cmd = $this->getCmd('info', 'givrage');
            if(is_object($cmd)) {
                $cmd->setConfiguration('value', $frost_point);
                $cmd->save();
                $cmd->event($frost_point);
                    log::add('rosee', 'debug', '│ Givrage : ' . $frost_point.' °C');
            }
        
        //Mise à jour de l'équipement point de rosée
            $cmd = $this->getCmd('info', 'rosee');
            if(is_object($cmd)) {
                $cmd->setConfiguration('value', $rosee_point);
                $cmd->save();
                $cmd->event($rosee_point);
                    log::add('rosee', 'debug', '│ Rosée : ' . $rosee_point.' °C');       
             }
                    log::add('rosee', 'debug', '└─────────');

        log::add('rosee', 'debug', '================ FIN CRON =================');
		return;
	}
}

class roseeCmd extends cmd {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */

	/*     * *********************Methode d'instance************************* */
	public function dontRemoveCmd()
    {
        return true;
    }

	public function execute($_options = null) {
		if ($this->getLogicalId() == 'refresh') {
			$this->getEqLogic()->getInformations();
			return;
		}
	}
}
?>