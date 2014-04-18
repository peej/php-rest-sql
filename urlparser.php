<?php
/**
 * This class provides all functionality for parsing the incoming URL.
*/
class RequestParser {
	
	var $URL = NULL;
	var $DIRS = NULL;
	var $PARAMS = NULL;
	var $BASEURL = NULL;
	
	function RequestParser($URL, $baseUrl = '') {
		$this->URL = $URL;
		$this->BASEURL = $baseUrl;
		$this->parse();
	}
	
	function parse() {
		$pos = strpos($this->URL, $this->BASEURL);
		if($pos == 0) {
			$substr = substr($this->URL, strlen($this->BASEURL));
			$this->URL = $substr;
		}
		
		// load all get parameters in arrays
		foreach ($_GET as $key => $value) {
			//TODO: hash map: check if it's e predefined parameter... if no, add to filter bundle
			$this->PARAMS[$key] = $value;
		}
		
		if($this->URL == '/') {
			return;
		} else {
			$urlParts = explode('/', $this->URL);
			$i = 0;
			foreach($urlParts as $val) {
				// making sure /?param=value is not a added as direction
				if($val !== '' && isset($val) && ($val[0] !== '?' && $val[0] !== '&')) {
					// making sure we cut off the params
					$pos = strpos($val, "?");
					if($pos == false) $this->DIRS[$i++] = $val;
					else $this->DIRS[$i++] = substr($val, 0, $pos);
				}
			}
		}

	}
	
	function getDirs() {
		return $this->DIRS;
	}
	
	function getParameters() {
		return $this->PARAMS;
	}

}
?>