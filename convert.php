<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require 'nanodicom.php';

$dir = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;
$dirjpeg = realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'samplesjpeg'.DIRECTORY_SEPARATOR;

$files = array();
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) 
	{
        if ($file != "." && $file != ".." && is_file($dir.$file)) 
		{
			$files[] = $file;
		}
	}
    closedir($handle);
}

foreach ($files as $file)
{
	$filename = $dir.$file;
	// 18) Pass your own list of elements to anonymizer
	try
	{
		echo "18) Pass your own list of elements to anonymizer\n";
		// Own tag elements for anonymizing
		$tags = array(
			array(0x0008, 0x0020, '{date|Ymd}'),			// Study Date
			array(0x0008, 0x0021, '{date|Ymd}'),			// Series Date
			array(0x0008, 0x0090, 'physician{random}'),		// Referring Physician
			array(0x0010, 0x0010, 'patient{consecutive}'),  // Patient Name
			array(0x0010, 0x0020, 'id{consecutive}'), 		// Patient ID
			array(0x0010, 0x0030, '{date|Ymd}'), 			// Patient Date of Birth
		);
		$dicom  = Nanodicom::factory($filename, 'anonymizer');
		$dicom1 = Nanodicom::factory($dicom->anonymize($tags), 'dumper', 'blob');
		echo $dicom1->dump();
		unset($dicom);
		unset($dicom1);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n";
	}

	// 19) Pass your own list of mappings to anonymizer. Patient Name should be replace to
	// 'Mapped' if 'Anonymized' is found. Case sensitive
	try
	{
		echo "19) Pass your own list of mappings to anonymizer\n";
		// Own tag elements for anonymizing
		$tags = array(
			array(0x0008, 0x0020, '{date|Ymd}'),			// Study Date
			array(0x0008, 0x0021, '{date|Ymd}'),			// Series Date
			array(0x0008, 0x0090, 'physician{random}'),		// Referring Physician
			array(0x0010, 0x0010, 'patient{consecutive}'),  // Patient Name
			array(0x0010, 0x0020, 'id{consecutive}'), 		// Patient ID
			array(0x0010, 0x0030, '{date|Ymd}'), 			// Patient Date of Birth
		);
		$replacements = array(
			array(0x0010, 0x0010, 'anonymized', 'Mapped'),
		);
		$dicom  = Nanodicom::factory($filename, 'anonymizer');
		$dicom1 = Nanodicom::factory($dicom->anonymize($tags, $replacements), 'dumper', 'blob');
		echo $dicom1->dump();
		//file_put_contents($filename.'.ex19', $dicom1->write());
		unset($dicom);
		unset($dicom1);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n";
	}

	// 20) Gets the images from the dicom object if they exist. This example is for gd
	try
	{
		echo "20) Gets the images from the dicom object if they exist. This example is for gd\n";
		$dicom  = Nanodicom::factory($filename, 'pixeler');
		if ( ! file_exists($filename.'.0.jpg'))
		{
			
			$images = $dicom->get_images();
			// If using another library, for example, imagemagick, the following should be done:
			// $images = $dicom->set_driver('imagick')->get_images();

			if ($images !== FALSE)
			{
				foreach ($images as $index => $image)
				{
					// Defaults to jpg
					$dicom->write_image($image, $dirjpeg.$file.'.'.$index);
					// To write another format, pass the format in second parameter.
					// This will write a png image instead
					// $dicom->write_image($image, $dir.$file.'.'.$index, 'png');
				}
			}
			else
			{
				echo "There are no DICOM images or transfer syntax not supported yet.\n";
			}
			$images = NULL;
		}
		else
		{
			echo "Image already exists\n";
		}
		unset($dicom);
	}
	catch (Nanodicom_Exception $e)
	{
		echo 'File failed. '.$e->getMessage()."\n";
	}
} 

echo memory_get_usage() . "\n"; // 36640
?>