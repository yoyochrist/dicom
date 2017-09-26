<?php
	require 'fpdf.php';
	
	if(isset($_POST['var'])) 
		$var=$_POST['var'];
	
	if(isset($_POST['dirjpeg'])) 
		$dirjpeg=$_POST['dirjpeg'];
	
	if(isset($_POST['varDir'])) 
		$varDir=$_POST['varDir'];
	
	if(isset($_POST['varPatientName'])) 
		$varPatientName=$_POST['varPatientName'];
	
	if(isset($_POST['varPatientID'])) 
		$varPatientID=$_POST['varPatientID'];
	
	if(isset($_POST['varStudyID'])) 
		$varStudyID=$_POST['varStudyID'];
	
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$pdf->Cell(40,10,'PatientID :'.$varPatientID);
	$pdf->Ln(2);
	$pdf->Cell(0,25,'Patient Name :'.$varPatientName);
	$pdf->Ln(2);
	$pdf->Cell(0,40,'Image : ');
	$pdf->Ln(2);
	
	$k = 0;
	$end = 0;
	$y = 40;
	while($end == 0){
		if(file_exists($dirjpeg.$var.".".$k.".jpg"))
		{
			//echo "<img id='image-gallery-image' class='img-responsive' src='".$varDir.$var.".".$k.".jpg'>";
			$file = $varDir.$var.".".$k."."."jpg";
			$pdf->image($file,10,$y,0,0,'jpg');
			$pdf->Ln(2);
			$y+=10;
		}
		else
			$end = 1;
		$k++;
	}
	
	$pdf->Output();
?>