<?php


class Zipper {
	
	protected $filesArray;
	protected $numFiles;
	protected $destination;
	protected $basepath;
	protected $zipURI;

	public function __construct($filesArray, $zipDir = NULL, $zipName = NULL) {
		$this->filesArray = $filesArray;  // do some checking
		$this->numFiles = count($filesArray);
		$this->zipDir = ($zipDir) ? $zipDir : realpath(dirname(__FILE__));
		$this->zipName = ($zipName) ? $zipName : mt_rand(1000000, 9999999) . ".zip";
		if (!stristr($this->zipName, ".zip")) $this->zipname = $this->zipName . ".zip";
		$this->basepath = realpath(dirname(__FILE__)) . "/";

	}

	public function troubleshoot() {
		echo "TROUBLESHOOT";
		var_dump($this->filesArray);
		echo "numFiles :: " . $this->numFiles . "<br>";
		echo "zipDir :: " . $this->zipDir . "<br>";
		echo "zipName :: " . $this->zipName . "<br>";
		echo "basepath :: " . $this->basepath . "<br>";
	}

	public function makeZipArchive($overwrite = false) {

		$zip = new zipArchive();
		
		if (!file_exists($this->basepath . $this->zipDir)
			&& !is_dir($this->basepath . $this->zipDir))
		{
			mkdir($this->basepath . $this->zipDir);
		}
		
		$zipPath = $this->basepath . $this->zipDir . $this->zipName;
		
		if ($zip->open($zipPath, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true)
		{
			return false;
		}			
		
		// check zip destination for ok
		if (!is_writable($this->zipDir)) {return "Zip directory not writeable";}
		
		for ($i = 0; $i < $this->numFiles; $i++)
		{
			// check to make sure valid
			if ($this->filesArray[$i] === ""
				|| $this->filesArray[$i] === " ")
				{
					continue;
				}
			// zip files and add to destination
			$zip->addFile($this->filesArray[$i]);
		}
		// set URI
		$this->zipURI = $zipPath;
	}

	public function getDownloadLink() {

		if (!is_readable($this->zipURI)) {return false;}
		return "<a href=\"$this->zipURI\" title=\"download link\">Link to your .zip</a>";
	}

	public function setDestination($string) {
		// set new destination folder
		if(is_writeable($string)) {
			$this->destination = $string;
		} else {
			return "That location is not writeable";
		}
	}

	public static function generateHTML($formId) {
		echo("\n<form action=\"zipper.php\" method=\"POST\" enctype=\"multipart/form-data\" id=\"uploadForm\">\n");
		echo("\t<div id=\"inputs\">\n");
		echo("\t\t<input type=\"file\" name=\"userfile[]\" multiple=\"true\">\n");
		echo("\t\t<input type=\"file\" name=\"userfile[]\" multiple=\"true\">\n");
		echo("\t</div>\n");
		echo("\t<input type=\"submit\" value=\"submit\">\n");
		echo("</form>\n");
		echo("<button type=\"button\" id=\"addInput\">Add Another file</button>\n\n");
	}

	/***
	* Echos the JS script for adding input fields to form
	* @param   boolean	Wether or not the script tags should be outputted, or just the JS code
	* @return  void
	*/
	public static function generateJS($withTags = false) {
		// echo JS for form
		if ($withTags) echo("\n<script type=\"text/javascript\">\n");
		echo("document.addEventListener('DOMContentLoaded', function() {var addInput = document.getElementById('addInput');addInput.addEventListener('click', function(){var newInput = document.createElement('input');newInput.type = 'file';newInput.name = 'userfile[]';newInput.setAttribute('multiple', 'true');var inputs = document.getElementById('inputs');inputs.appendChild(newInput);});}, false);\n");
		if ($withTags) echo("</script>");
	}


}




// $zipper = new Zipper($filesAray);
// $zipper->makeURI();
// $zipper->setDestination($path);
?>
