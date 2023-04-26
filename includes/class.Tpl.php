<?php
	Class tpl{
		private $CONTENT;
		private $BLOCK_CONTENT;
		private $BLOCK_OPENED;
		const VAR_PREFIX = "var_";
		const LBL_PREFIX = "lbl_";
		const BLOCK_BEGIN = "<!--BEGIN BLOCK";
		const BLOCK_END = "<!--END BLOCK";
		
		function __construct($sFile){
			$this->CONTENT = file_get_contents($sFile);
		}
		
		function __destruct(){
			$this->CONTENT = "";
		}

		public function clearVars(){
			$iFind = strpos($this->CONTENT, "{" . self::VAR_PREFIX);
			
			while($iFind !== false){
				$iVarEnd = strpos($this->CONTENT, "}", $iFind);
				$iIni = $iFind + strlen(self::VAR_PREFIX) + 1;
				$sVar = substr($this->CONTENT, $iIni, $iVarEnd - $iIni);
				$this->setVar($sVar, "");
				$iFind = strpos($this->CONTENT, "{" . self::VAR_PREFIX, $iVarEnd);
			}
		}
		
		public function clearLbls(){
			$iFind = strpos($this->CONTENT, "{" . self::LBL_PREFIX);
			
			while($iFind !== false){
				$iVarEnd = strpos($this->CONTENT, "}", $iFind);
				$iIni = $iFind + strlen(self::LBL_PREFIX) + 1;
				$sVar = substr($this->CONTENT, $iIni, $iVarEnd - $iIni);
				$this->setVar($sVar, "");
				$iFind = strpos($this->CONTENT, "{" . self::LBL_PREFIX, $iVarEnd);
			}
		}
		
		public function clearFields(){
			$this->clearVars();
			$this->clearLbls();
		}
		
		public function setVar($sVar, $sValue){
			$this->CONTENT = str_replace("{" . self::VAR_PREFIX . $sVar . "}", $sValue, $this->CONTENT);
		}

		public function beginBlock($sBlock){
			$sBegin =  self::BLOCK_BEGIN . " $sBlock-->";
			$sEnd = self::BLOCK_END . " $sBlock-->";
			
			$iFindB = strpos($this->CONTENT, $sBegin);
			$iFindE = strpos($this->CONTENT, $sEnd);
			
			$iIni = $iFindB + strlen($sBegin);
			$iFin = $iFindE - $iIni;
			
			$sReturn = substr($this->CONTENT, $iIni, $iFin);
			$sTemp = $sBegin . substr($this->CONTENT, $iIni, $iFin) . $sEnd;
			
			$this->CONTENT = str_replace($sTemp, "{_BLOCK_OPEN}", $this->CONTENT);
			
			return $sReturn;
		}

		public function addToBlock($sBlockData, $vVars){
			foreach($vVars as $sVar=>$sValue){
				$sBlockData = str_replace("{" . self::VAR_PREFIX . $sVar . "}", $sValue, $sBlockData);
			}
			$this->BLOCK_CONTENT = $this->BLOCK_CONTENT . $sBlockData;
			//echo $this->BLOCK_CONTENT;
		}
		
		public function endBlock(){
			$this->CONTENT = str_replace("{_BLOCK_OPEN}", $this->BLOCK_CONTENT, $this->CONTENT);
			$this->BLOCK_CONTENT = "";
		}

		public function deleteBlock($sBlock){
			$sBegin =  self::BLOCK_BEGIN . " $sBlock-->";
			$sEnd = self::BLOCK_END . " $sBlock-->";
			
			$iFindB = strpos($this->CONTENT, $sBegin);
			$iFindE = strpos($this->CONTENT, $sEnd);
			
			$iIni = $iFindB + strlen($sBegin);
			$iFin = $iFindE - $iIni;
			
			$sTemp = $sBegin . substr($this->CONTENT, $iIni, $iFin) . $sEnd;
			
			$this->CONTENT = str_replace($sTemp, "", $this->CONTENT);
			
		}

		function getContent($archivo){
		}
	 
		public function openFile($sVar, $sFile, $bd){
			if(file_exists($sFile)){
				ob_start();
				include_once($sFile);
				$out = ob_get_clean();
				ob_end_clean();
				$this->setVar($sVar, $out);		
			}
		}
		
		public function printTpl(){
			echo $this->CONTENT;
		}

		public function returnTpl(){
			return $this->CONTENT;
		}
	}
?>
