<?php
	require 'fpdf.php';
	$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'samplespdf'.DIRECTORY_SEPARATOR;
	
	if(isset($_POST['var'])) 
		$var=$_POST['var'];
	
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
	$pdf->image($var,10,40,0,0,'jpg');
	$pdf->Output('Filename.pdf', 'S');
?>