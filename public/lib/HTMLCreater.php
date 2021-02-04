<?php
ini_set('memory_limit', '4096M');

class HTMLCreater{
	public function generatehtml($dataArray){
		echo "Formulating And Populating Table ...\n";

		$table = "";
		$table = "<html>
						<head>
							<style>
									table {
	  									font-family: arial, sans-serif;
	  									border-collapse: collapse;
	  									width: 100%;
									}

									td, th {
									  border: 1px solid #dddddd;
									  text-align: left;
									  padding: 8px;
									}

									tr:nth-child(even) {
	 								 background-color: #dddddd;
									}
							</style>
						</head>
						<body>
							<table>
								<thead>
									<tr>
										<td>#</td>
										<td>Editor</td>
										<td>Station</td>
										<td>Date</td>
										<td>Time</td>
										<td>Brand</td>
										<td>Entry Type</td>
										<td>QC Name</td>
										<td>Edit Date</td>
									</tr>
								</thead>
								<tbody>";

		$count = 1;
		foreach ($dataArray as  $row) {
			$qc_name = $row['qc_name'];
			$station = $row['station'];
			$editor_name = $row['editor_name'];
			$date = $row['date'];
			$time = $row['time'];
			$brand = $row['brand'];
			$entry_type = $row['entry_type'];
			$edit_date = $row['edit_date'];

			$table .="<tr>
						<td>$count</td>
						<td>$editor_name</td>
						<td>$station</td>
						<td>$date</td>
						<td>$time</td>	
						<td>$brand</td>
						<td>$entry_type</td>
						<td>$qc_name</td>
						<td>$edit_date</td>
					</tr>
			";
			$count++;

		}
		$table .= "</tbody></table></body>";
		return $table;
	}

	public function GenerateMessage($dataArray,$sdate,$edate){
		$greetings = $this->Greetings;
		$table = $this->generatehtml($dataArray);

		$msg = "$greetings Sir/Madam,<br>
		Below and attached are errors captured by the QC team for the period of $sdate and $end. <br><br>$table";
	}

	public function Greetings(){
        $hours = date('H');
            if ($hours >= 0 && $hours <= 12) {
                return "Good Morning ";
            } else {
                if ($hours > 12 && $hours <= 17) {
                    return "Good Afternoon ";
                } else {
                    if ($hours > 17 && $hours <= 20) {
                        return "Good Evening ";
                    } else {
                        return "Hello ";
                    }
                }
            }
        }
}