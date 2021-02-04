<?php
		use PhpOffice\PhpSpreadsheet\Spreadsheet;
		use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class SpreadsheetCreator {
	function __construct($lib){
		$this->lib = $lib;
	}
	
	public function CreateExcelSheet($code,$dataArray,$start,$end){
		$lib = $this->lib;
		require_once $lib;
		

		$filename = "public/excel/".$code."_Errors_".date('YmdHis').".xlsx";

		$spreadsheet = new Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0);
		$sheet = $spreadsheet->getActiveSheet()->setTitle("QC Errors");
		$sheet->setCellValue('A1', "Reelanalytics LTD | $code");
		$sheet->setCellValue('A2', "Error For The Period Between $start And $end");
		$styleArray = array(
		    'font'  => array(
		        'bold'  => true,
		        'color' => array('rgb' => 'ffffff'),
		    ));
		$sheet->setCellValue('A3', "Editor");
		$sheet->setCellValue('B3', "Station");
		$sheet->setCellValue('C3', "Date");
		$sheet->setCellValue('D3', "Time");
		$sheet->setCellValue('E3', "Brand");
		$sheet->setCellValue('F3', "Entry Type");
		$sheet->setCellValue('G3', "QC Name");
		$sheet->setCellValue('H3', "Edit Date");
		$spreadsheet->getActiveSheet()->getStyle('A3:H3')->applyFromArray($styleArray);
		$spreadsheet->getActiveSheet()->getStyle('A3:H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('1c3a6d');

		$count =3;

		//$sheet = $spreadsheet->getActiveSheet();
		foreach ($dataArray as $key => $row) {
			$count++;
			$qc_name = $row['qc_name'];
			$station = $row['station'];
			$editor_name = $row['editor_name'];
			$date = $row['date'];
			$time = $row['time'];
			$brand = $row['brand'];
			$entry_type = $row['entry_type'];
			$edit_date = $row['edit_date'];

			
			$sheet->setCellValue("A$count", "$editor_name");
			$sheet->setCellValue("B$count", "$station");
			$sheet->setCellValue("C$count", "$date");
			$sheet->setCellValue("D$count", "$time");
			$sheet->setCellValue("E$count", "$brand");
			$sheet->setCellValue("F$count", "$entry_type");
			$sheet->setCellValue("G$count", "$qc_name");
			$sheet->setCellValue("H$count", "$edit_date");

			$spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(100);
			$spreadsheet->getActiveSheet()->getStyle("C$count")->getFont()->getColor()->setARGB('3633FF');
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);
			$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
			$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);			
			$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);			
		}
			$writer = new Xlsx($spreadsheet);
			$writer->save($filename);
			return $filename;
	}
	
}