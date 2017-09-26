<html>
	<head>
		<title></title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.11/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.10.11/css/jquery.dataTables.min.css"></script>
		<script>
			$(document).ready(function() {
				var table = $('#data').DataTable(
					{
						fixedHeader: true
					}
				);
			} );
		</script>
		<style>
			.carousel-inner > .item > img,
			.carousel-inner > .item > a > img {
				width: 100%;
				margin: auto;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<div class="table-responsive" align="center">
				<table id ="data" class="table table-striped table-condensed" cellspacing=0 width="100%">
					<thead>
					<tr>
						<th class="col-xs-1">Patient ID</th>
						<th class="col-xs-1">Patient's Name</th>
						<th class="col-xs-1">Patient Age</th>
						<th class="col-xs-1">Patient Sex</th>
						<th class="col-xs-1">Patient Birth Date</th>
						<th class="col-xs-1">Study Date</th>
						<th class="col-xs-1">Series in Study</th>
						<th class="col-xs-1">Thumbnail</th>
						<th class="col-xs-1">Action</th>
					</tr>
					</thead>
					<tbody>
					<?php
						ini_set('display_errors', 'On');
						error_reporting(E_ALL | E_STRICT);

						require 'nanodicom.php';
				
						$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;
						$dirjpeg = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'samplesjpeg'.DIRECTORY_SEPARATOR;

						function DataExist($dataInput)
						{
							if(strlen(trim($dataInput,' ')) == 0)
							{
								return "--";
							}
							else
							{
								return $dataInput;
							}
						}
						function AgeDataExist($ageInput)
						{
							if($ageInput/1 == 0)
							{
								return '--';
							}
							else
							{
								return str_replace('Y',' years old',trim($ageInput,'0'));
							}
						}

						function ChangeDateFormat($dateInput)
						{
							$dateInput = trim($dateInput, '?');
							if(substr($dateInput,4,1)=='.' || substr($dateInput,4,1)=='-')
							{
								$DD = substr($dateInput,8,2);
								$MM = substr($dateInput,5,2);
								$YYYY = substr($dateInput,0,4);
								if(substr($YYYY,0,2)=='00')
								{
									$getCurrentYear = substr(date("Y"),0,2);
									$getLastYear = $getCurrentYear-1;
									$YYYY = str_replace('00',$getLastYear,$YYYY);
								}
								$changeFormat = $DD."-".$MM."-".$YYYY;
							}
							else if(substr($dateInput,2,1)!='.')
							{
								$DD = substr($dateInput,6,2);
								$MM = substr($dateInput,4,2);
								$YYYY = substr($dateInput,0,4);
								$changeFormat = $DD."-".$MM."-".$YYYY;
							}
							else
							{
								$changeFormat = str_replace('.','-',$dateInput);
							}
							return $changeFormat;
						}

						$files = array();
						if ($handle = opendir($dir)) 
						{
							while (false !== ($file = readdir($handle))) 
							{
								if ($file != "." && $file != ".." && is_file($dir.$file)) 
								{
									$files[] = $file;
								}
							}
							closedir($handle);
						}
						$row = 1;
						foreach ($files as $file)
						{
							$filename = $dir.$file;
					
							// 3) Load only given tags by name. Stops once all tags are found. Not so fast.
							try
							{
								//echo "3) Load only given tags by name. Stops once all tags are found. Not so fast.<br/>";
								$dicom = Nanodicom::factory($filename, 'simple');
								$dicom->parse(array('PatientName','PatientID','PatientAge','PatientSex','PatientBirthDate', 'StudyID', 'StudyDate', 'SeriesInStudy'));
						
								//echo $dicom->profiler_diff('parse')."\n";
								//echo 'Patient name if exists: '.$dicom->value(0x0010, 0x0010)."\n"; // Patient Name if exists
								//
								//Or
								
								echo "<tr>";
								$row++;
								echo '<td class="col-xs-1"><p>'.DataExist($dicom->PatientID)."</p></td>"; // Patient ID if exists
								echo '<td class="col-xs-1"><p>'.DataExist($dicom->PatientName)."</p></td>"; // Patient Name if exists
								echo '<td class="col-xs-1"><p>'.AgeDataExist($dicom->PatientAge)."</p></td>"; // Patient Age if exists
								echo '<td class="col-xs-1"><p>'.DataExist($dicom->PatientSex)."</p></td>"; // Patient Sex if exists
								echo '<td class="col-xs-1"><p>'.ChangeDateFormat($dicom->PatientBirthDate)."</p></td>"; // Patient Sex if exists
								echo '<td class="col-xs-1"><p>'.ChangeDateFormat($dicom->StudyDate)."</p></td>"; // Patient Sex if exists
								echo '<td class="col-xs-1"><p>'.$dicom->SeriesInStudy."</p></td>"; // Patient Sex if exists
	?>
								<td class="col-xs-1">
									<a class="thumbnail" href="#" data-image-id="" data-toggle="modal" data-target="#image-gallery<?php echo $row?>">
										<img class="img-responsive" src="http://localhost:88/dicom-master/samplesjpeg/<?php echo $file.'.0.jpg';?>" alt="Short alt text" width="50" height="50">
									</a>
									<div class="modal fade" id="<?php echo 'image-gallery'.$row?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
													<h4 class="modal-title" id="image-gallery-title">Patient Name : <?php echo $dicom->PatientName?></h4>
												</div>
												<div class="modal-body">
													<div id="myCarousel<?php echo $row;?>" class="carousel slide" data-ride="carousel">
														<div class="carousel-inner" role="listbox">
																<?php
																	$k = 0;
																	$end = 0;
																	while($end == 0){
																		if(file_exists($dirjpeg.$file.".".$k.".jpg"))
																		{
																			echo "<div class='item ";
																			if($k == 0)
																				echo "active'>";
																			else
																				echo "'>";
																			echo "<img id='image-gallery-image' class='img-responsive' src='http://localhost:88/dicom-master/samplesjpeg/".$file.".".$k.".jpg'>";
																			/*echo '<div class="carousel-caption">
																				<h3>'.$k.'</h3>
																			 </div>';*/
																			echo "</div>";
																		}
																		else
																			$end = 1;
																		$k++;
																	}
																?>
														</div>
														<a class="left carousel-control" href="#myCarousel<?php echo $row;?>" data-slide="prev">
														  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
														  <span class="sr-only">Previous</span>
														</a>
														<a class="right carousel-control" href="#myCarousel<?php echo $row;?>" role="button" data-slide="next">
														  <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
														  <span class="sr-only">Next</span>
														</a>
													</div>
												</div>
												<div>
													<h5 class="modal-footer" id="image-gallery-footer"><b>Filename : <?php echo substr($file,0,-4); ?></b></h5>
												</div>
											</div>
										</div>
									</div>
								</td>
								<td class="col-xs-1">
									<form action="printpdf.php" method="post">
										<input type='hidden' name='var' value="<?php echo $file; ?>" /> 
										<input type='hidden' name='varDir' value="<?php echo 'http://localhost:88/dicom-master/samplesjpeg/'; ?>" /> 
										<input type='hidden' name='dirjpeg' value="<?php echo $dirjpeg; ?>" /> 
										<input type='hidden' name='varPatientName' value="<?php echo $dicom->PatientName; ?>" /> 
										<input type='hidden' name='varPatientID' value="<?php echo $dicom->PatientID; ?>" /> 
										<input type='hidden' name='varStudyID' value="<?php echo $dicom->StudyID; ?>" /> 
										<button class = "btn btn-info btn-lg" type = "submit"><span class="glyphicon glyphicon-print"></span></button>
									</form>

									<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#<?php echo 'myModal'.$row?>"><span class="glyphicon glyphicon-envelope"></span></button>

									<!-- Modal -->
									<div class="modal fade" id="<?php echo 'myModal'.$row?>" role="dialog">
										<div class="modal-dialog">
											<!-- Modal content-->
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title">Send Email</h4>
												</div>
												<div class="modal-body">
													<form action="sendpdf.php" method="POST"  enctype="multipart/form-data" data-toggle="validator" role="form">
															<input type='hidden' name='varPDF' value="<?php echo $file; ?>" /> 
															<input type='hidden' name='varDirPDF' value="<?php echo 'http://localhost:88/dicom-master/samplesjpeg/'; ?>" /> 
															<input type='hidden' name='dirjpeg' value="<?php echo $dirjpeg; ?>" /> 
															<input type='hidden' name='varPatientNamePDF' value="<?php echo $dicom->PatientName; ?>" /> 
															<input type='hidden' name='varPatientIDPDF' value="<?php echo $dicom->PatientID; ?>" /> 
															<input type='hidden' name='varStudyIDPDF' value="<?php echo $dicom->StudyID; ?>" />
															<input type='hidden' name='varStudyFilePDF' value="<?php echo substr($file,0,-4).'.pdf' ?>" />
														<!--<div class="form-group">
															<label for="inputEmail" class="control-label">From</label>
															<input type="email" class="form-control" name="inputEmail" placeholder="From" required>
														<div class="help-block with-errors"></div>-->
														<div class="form-group">
															<label for="toEmail" class="control-label">To</label>
															<input type="email" class="form-control" name="toEmail" placeholder="To" required>
														<div class="help-block with-errors"></div>
														<!--<div class="form-group">
															<label for="filePdf" class="control-label">Result (PDF) :</label>
															<input type ="file" name='attachment' id='uploaded_file'>
														</div>-->
												</div>
												<div class="modal-footer">
														<input class="btn btn-info" type="submit" value="Send" />
													</form>
												</div>
										  </div>
										</div>
									</div>
								</td>
	<?php
								unset($dicom);
								echo "</tr>";
							}
							catch (Nanodicom_Exception $e)
							{
								echo 'File failed. '.$e->getMessage()."<br/>";
							}
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>