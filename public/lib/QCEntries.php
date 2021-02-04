<?php
//SET GLOBAL max_allowed_packet=1524288000;
ini_set('memory_limit', '4096M');

class QCEntries extends Dbmethods{
	
	function __construct($code){
		$this->code = $code;
		$this->AD = Dbmethods::ADConn($code);
		$this->PR = Dbmethods::PRConn($code);
		$this->RPP = Dbmethods::RPPConn($code);
		$this->settings = Dbmethods::Settings($code);
	}

	public function tempTable(){
		$con = $this->PR;
		$code = $this->code;
		$rand = $this->generateRandomString();
		$table = 'tempTable_'.$code."_".$rand;

		$sq = "CREATE TEMPORARY  TABLE IF NOT EXISTS ".  $table ." (auto_id int(100) not null auto_increment primary key,qc_name varchar(200) not null,station varchar(200) not null,editor_name varchar(200) not null,`date` date not null,`time` time not null,brand varchar(100) not null,entry_type varchar(200) not null,edit_date date not null)";
		$q= $con->query($sq) or die("<br>Error Create: <br>".$con->error."<br><br>$sq<br>");
		return $table;
	}

	public function getQCusers(){
		$con = $this->RPP;

		$sq = "SELECT user_id,firstname,username FROM users WHERE `status` = 1 and `level` = 0 order by firstname";
		$q = $con->query($sq) or die("Error :<br>".$con->error);
		$results= [];
		if ($q && $q->num_rows > 0) {
			while ($row = $q->fetch_assoc()) {
				$results[] = $row;
			}
		}
		return $results;
	}

	public function GetEntries($table,$sdate,$edate){
		$users = $this->getQCusers();

		foreach ($users as  $value) {
			$user_id = $value['user_id'];
			$firstname = rtrim($value['firstname']);
			$username = $value['username'];echo "User:- $username || User ID:- $user_id<br>";

			$qc_name = $firstname;

			if(!strpos($firstname, ' ')  || strlen($firstname) < 5){
				$qc_name = $username;
			}
			echo "<br><br>Analysing For :- $qc_name<br>";			

			$manualsAds = $this->getAds($user_id,$sdate,$edate);
			$manualPrs = $this->getPrs($username,$sdate,$edate);
			$size = sizeof($manualsAds);
			$prsize = sizeof($manualPrs);
			if ($size > 0) {
				$admgs = "Number Of AD Entries:- $size <br> ";

				//Inserting Records
				$this->InsertRecords($table,$qc_name,$manualsAds,'ad');
			} else {
				$admgs = "No AD Entries <br>";
			}

			if ($prsize > 0) {
				$prms =  "Number Of PR Entries:- $prsize <br> ";

				//Inserting Records
				$this->InsertRecords($table,$qc_name,$manualPrs,'pr');
			} else {
				$prms = "No PR Entries <br> ";
			}
			
			echo "$admgs $prms";
		}
	}

	public function getAds($user_id,$sdate,$edate){
		$con = $this->AD;

		$sq = "SELECT djmentions_2021_01.`date` AS work_date,djmentions_2021_01.`time` AS reel_time,station_name,brand_name,entry_type,djmentions_editor.date as edit_date,station_code as station_code  FROM 
				djmentions_2021_01 
				INNER JOIN djmentions_editor ON djmentions_2021_01.auto_id = djmentions_editor.manual_id
				INNER JOIN station ON station.station_id = djmentions_2021_01.station_id
				INNER JOIN brand_table ON brand_table.brand_id = djmentions_2021_01.brand_id
				WHERE djmentions_2021_01.date BETWEEN '$sdate' AND '$edate'
				AND djmentions_editor.editor_id = $user_id
				UNION
				SELECT reelforge_sample.`reel_date` AS work_date,reelforge_sample.`reel_time` AS reel_time,station_name,brand_name,entry_type,
				djmentions_editor.date as edit_date,station_code as station_code  				FROM reelforge_sample 
				INNER JOIN djmentions_editor ON reelforge_sample.reel_auto_id = djmentions_editor.manual_id
				INNER JOIN station ON station.station_id = reelforge_sample.station_id
				INNER JOIN djmentions_entry_types ON djmentions_entry_types.entry_type_id = reelforge_sample.entry_type_id
				INNER JOIN brand_table ON brand_table.brand_id = reelforge_sample.brand_id
				WHERE reelforge_sample.reel_date BETWEEN '$sdate' AND '$edate'
				AND djmentions_editor.editor_id = $user_id
				ORDER BY work_date,reel_time,station_name;";
		$q=$con->query($sq) or die("Error Getting Ads: ".$con->error);
		$results = [];
		if ($q && $q->num_rows > 0) {
			while ($row = $q->fetch_assoc()) {
				$results[] = $row;
			}
		}
		return $results;
	}

	public function generateRandomString($length = 4) {
    	return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}

	public function getPrs($username,$sdate,$edate){
		$con = $this->PR;

		$sq = "SELECT StoryDate AS work_date,storytime AS reel_time,storypage,Media_House_List as station_name,'News' as brand_name,'News' as entry_type,StoryDate as edit_date,media_code as station_code
				FROM story
				INNER join mediahouse ON mediahouse.Media_House_ID = story.Media_House_ID
				WHERE editor = '$username' 
				AND storyDate BETWEEN '$sdate' AND '$edate'
				AND story.Media_ID != 'mp01'
				ORDER BY work_date,reel_time,station_name";
		$q = $con->query($sq) or die("Error Pulling News".$con->error);
		$results = [];
		if ($q && $q->num_rows > 0) {
			while ($row = $q->fetch_assoc()) {
				$results[] = $row;
			}
		}
		return $results;
	}

	public function InsertRecords($table,$qc_name,$array,$dept){
		$con = $this->PR;
		echo "Inserting Records<br>";

		$count = 0;
		foreach ($array as  $row) {
			$reel_date = $row['work_date'];
			$reel_time = $row['reel_time'];
			$brand_name = $con->escape_string(htmlentities($row['brand_name']));
			$entry_type = $row['entry_type'];
			$edit_date = $row['edit_date'];
			$station= $con->escape_string(htmlentities($row['station_name']));
			$station_code = $row['station_code'];
			$location = $reel_time;
			if ($dept == 'pr') {
				$page = $row['storypage'];

				if (!isset($reel_time) || empty($reel_time) || $reel_time == '') {
					$location = $page;
				}
			}
			$editor = $this->getWorkAssignTO($reel_date,$reel_time,$station_code);
			echo "Station Assigned to: $editor <br>";

			$sq = "INSERT IGNORE INTO $table (qc_name,station,editor_name,`date`,`time`,brand,entry_type,edit_date)
					VALUES('$qc_name','$station','$editor','$reel_date','$reel_time','$brand_name','$entry_type','$edit_date')";
			$q = $con->query($sq) or die("Error Inserting ".$con->error);
			if ($q) {
				echo "$count . Successfully Inserted...<br>";
			}
			$count ++;
		}
	}

	public function getWorkAssignTO($reel_date,$reel_time,$station_code){
		$con = $this->RPP;
		$shiftID = $this->getShift($reel_time);

		$sq = "SELECT username FROM assignments
				INNER JOIN users ON assignments.editor_id = users.user_id 
				WHERE start_date = '$reel_date'
				AND shift = $shiftID 
				AND station_code = '$station_code'"; 
		$q = $con->query($sq) or ("Error Getting User ".$con->error);
		//$username = 'Unknown';
		if ($q && $q->num_rows > 0) {
			while ($row = $q->fetch_assoc()) {
				$username = $row['username'];
			}
		}else{
			$sq1 = "SELECT username FROM assignments 
					INNER JOIN users ON assignments.editor_id = users.user_id 
					WHERE start_date = '$reel_date' 
					AND station_code= '$station_code'";
			$q1 = $con->query($sq1) or ("Error Getting User ".$con->error);
				if ($q1 && $q1->num_rows > 0) {
					while ($row1 = $q1->fetch_assoc()) {
						$username = $row1['username'];
					}
				}else{}
					$username = "$station_code Not Assigned On $reel_date";
				}		

		return $username;
	}

	public function getShift($time){
		switch ($time) {
			case ($time>= '00:00:00' && $time <='14:59:59'):
				$shiftID = 1;
				return $shiftID;
				break;
			case ($time>= '15:00:00' && $time <='23:59:59'):
				$shiftID = 2;
				return $shiftID;
				break;
		}
	}

	public function generateHTML($sdate,$edate){
		$con = $this->PR;
		$code = $this->code;
		$config = $this->settings;

		$phpMailer = $config->phpMailer;
		$lib = $config->spreadsheet;
		$From = $config->From;
		$Name = $config->Name;
		$contacts = $config->contacts;

		$table = $this->tempTable();
		$this->GetEntries($table,$sdate,$edate);

		echo "Generating HTML Data<br>";
		$sq = "SELECT qc_name,station,editor_name,date,time,brand,entry_type,edit_date FROM $table ORDER BY editor_name,`date`,`time`,qc_name,station";
		$q = $con->query($sq) or die("Error Getting Data<br>".$con->error);
		if ($q && $q->num_rows > 0) {
			$runner = new HTMLCreater;
			$PostMan = new SendAlerts($phpMailer,$contacts,$From,$Name);
			$sheet = new SpreadsheetCreator($lib);

			$results = [];
			while ($row = $q->fetch_assoc()) {
				$results[]= $row;
			}
			$message = $runner->GenerateMessage($results,$sdate,$edate);
			$attachment = $sheet->CreateExcelSheet($code,$results,$sdate,$edate);
			
			$PostMan->SendthisAlert($message,$attachment);
		}

	}



}
