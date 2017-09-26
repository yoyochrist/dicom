<html>
	<head>
		<title></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php
			require_once('phpmailer/PHPMailerAutoload.php');
			require 'fpdf.php';
			
			if(isset($_POST['varPDF'])) 
				$varPDF=$_POST['varPDF'];
			
			if(isset($_POST['varPatientNamePDF'])) 
				$varPatientName=$_POST['varPatientNamePDF'];
			
			if(isset($_POST['varPatientIDPDF'])) 
				$varPatientID=$_POST['varPatientIDPDF'];
			
			if(isset($_POST['varStudyIDPDF'])) 
				$varStudyID=$_POST['varStudyIDPDF'];
			
			if(isset($_POST['varStudyFilePDF'])) 
				$varStudyPDF=$_POST['varStudyFilePDF'];
			
			if(isset($_POST['dirjpeg'])) 
				$dirjpeg=$_POST['dirjpeg'];
			
			if(isset($_POST['varDirPDF'])) 
				$varDirPDF=$_POST['varDirPDF'];
			
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
				if(file_exists($dirjpeg.$varPDF.".".$k.".jpg"))
				{
					//echo "<img id='image-gallery-image' class='img-responsive' src='".$varDir.$var.".".$k.".jpg'>";
					$file = $varDirPDF.$varPDF.".".$k."."."jpg";
					$pdf->image($file,10,$y,0,0,'jpg');
					$pdf->Ln(2);
					$y+=10;
				}
				else
					$end = 1;
				$k++;
			}
			
			$filecontent = $pdf->Output($varStudyPDF, 'S');
			
			define('GUSER', 'yoh.christiant@gmail.com'); // email username
			define('GPWD', 'a06071140'); // email password

			//jika username dan password SMTP sama dengan username dan password email.
			define('SMTPUSER', GUSER); // smtp username
			define('SMTPPWD', GPWD); // password
			define('SMTPSERVER', 'smtp.domain.com'); // smtp server
			define('SMTPPORT', 587); // port SMTP
			
			$msg = 'Hello World';
			$subj = 'test mail message';
			
			$from = 'yoh.christianto@gmail.com';
			$to = $_POST['toEmail'];
			$name = 'TEST MAIL';
			/* $namefile = $_FILES['attachment']['name'];
			$namepath = $_FILES['attachment']['tmp_name']; */
			
			echo "from :".$from;
			echo "<br/>to :".$to;
			echo "<br/>filename :".$varStudyPDF;
			
			if (smtpmailer($to, $from, $name, $subj, $msg, $filecontent, $varStudyPDF, true))
			{
				echo '<br/>Pesan terkirim ke alamat';
		?>
				<form action="http://localhost:88/dicom-master/summary.php">
					<input class="btn btn-info btn-md" type="submit" value="Back to summary">
				</form>
		<?php
			}
			else
			{
				echo 'Pesan gagal terkirim';
		?>
			<form submit="location.refresh()">
					<input class="btn btn-info btn-md" type="submit" value="Try Again">
			</form>
			<form action="http://localhost:88/dicom-master/summary.php">
				<input class="btn btn-info btn-md" type="submit" value="Back to summary">
			</form>
		<?php
			}
			
			function smtpmailer($to, $from, $from_name, $subject, $body, $attachment, $attachmentname, $is_gmail = true)
			{
				global $error;
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPDebug   = 0; // 2 to enable SMTP debug information
				if ($is_gmail)
				{
					$mail->SMTPSecure = 'ssl';
					$mail->Host = 'smtp.gmail.com';
					$mail->Port = 465;
					$mail->Username = GUSER;
					$mail->Password = GPWD;
				} 
				else
				{
					$mail->SMTPSecure = 'tls';
					$mail->SMTPAuth = true;
					$mail->Host = SMTPSERVER;
					$mail->Username = SMTPUSER;
					$mail->Password = SMTPPWD;
					$mail->Port = SMTPPORT;
				}
				
				$mail->SetFrom($from, $from_name);
				$mail->Subject = $subject;
				$mail->Body = $body;
				$mail->AddAddress($to);
				$mail->AddStringAttachment($attachment, $attachmentname);
				//$mail->AddAttachment($pathname, $filename);
				
				/* $mail->AddAttachment('attachment/phpmailer.gif');      // attachment
				$mail->AddAttachment('attachment/satu.txt'); // attachment */
				if(!$mail->Send()) {
					$error = 'Mail error: '.$mail->ErrorInfo;
					return false;
				} 
				else
				{
					$error = 'Message sent!';
					return true;
				}
			}
		?>
	</body>
</html>