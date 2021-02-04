<?php
/**
 * 
 */
class Dbmethods {
	
	public function ADConn($code){
		global $config;

		$server = $config->server;
		$username = $config->user;
		$pass = $config->password;

		$dbs= $this->myDBs($code);
		$db = $dbs->ad;

		$con = new mysqli($server,$username,$pass,$db) or die($con->error);
		
		return $con;
	}

	public function PRConn($code){
		global $config;

		$server = $config->server;
		$username = $config->user;
		$pass = $config->password;

		$dbs= $this->myDBs($code);
		$db = $dbs->pr;

		$con = new mysqli($server,$username,$pass,$db) or die($con->error);
		
		return $con;
	}

	public function RPPConn($code){
		global $config;

		$server = $config->server;
		$username = $config->user;
		$pass = $config->password;


		$dbs= $this->myDBs($code);
		$db = $dbs->rpp;

		$con = new mysqli($server,$username,$pass,$db) or die($con->error);
		
		return $con;
	}

	public function myDBs($code){
		$code = strtoupper($code);

		switch ($code) {
			case 'KE':
				$mydbs = (object)array(
					'ad'=>'forgedb',
					'pr'=>'reelmedia',
					'rpp'=>'rpp'
				);
				return $mydbs;
				break;
			case 'UG':
				$mydbs = (object)array(
					'ad'=>'forgedb_ug',
					'pr'=>'reelmedia_ug',
					'rpp'=>'rpp_ug'
				);
				return $mydbs;
				break;
			case 'TZ':
				$mydbs = (object)array(
					'ad'=>'forgedb_tz',
					'pr'=>'reelmedia_tz',
					'rpp'=>'rpp_tz'
				);
				return $mydbs;
				break;
			
			default:
				# code...
				break;
		}
	}

	public function Settings($code){
		$code = strtoupper($code);

		switch ($code) {
			case 'KE':
				global $settingsKE;
				return $settingsKE;
				break;
			case 'UG':
				global $settingsUG;
				return $settingsUG;
				break;
			case 'TZ':
				global $settingsTZ;
				return $settingsTZ;
				break;
			
			default:
				# code...
				break;
		}
	}
}