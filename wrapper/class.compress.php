<?php
/**
 * Class: compress [PHP]
 * NE PAS OUBLIER D'APPELER :
 * 		if(phpversion()<5){
 * 			require './lib/class.JavaScriptPacker.php4';
 * 		}
 * 		else require './lib/class.JavaScriptPacker.php';
 * AVANT D'APPELER :
 * 		include_once './wrapper/class.compress.php';
 */
class compress{

	/**
	 * Property: revision
	 * Numéro de révision des fichiers
	 */
	var $revision;
	
	/**
	 * Property: rootDir
	 * {String}
	 */
	var $rootDir="";
	
	/**
	 * Constructor: compress
	 */
	function compress(){}
	
	/**
	 * Function: getRevision
	 * Retourne le numéro de la révision courrante
	 */
	function getRevision(){
		//On va chercher la version de la révision
		$url=APP_PATH."/.svn/entries";
		$revision=null;
		if(file_exists($url)){
			$handle =fopen($url,"r");
			$aData=array();
			if ($handle) {
				while (!feof($handle)) {
					$aData[]  = fgets($handle, 4096);
				}
				fclose($handle);
			}
			$revision=trim($aData[3]);
			$this->revision=$revision;
			$this->dateRevision=trim($aData[9]);
			$chem=explode("/",trim($aData[5]));
			$this->dossierDepot=$chem[(count($chem)-1)];
		}
		return $revision;
	}
	
	/**
	 * Function: compressJS
	 * Va compressé et crypter un fichier JS
	 * 	Exemple :
	 *     $extras=new stdclass();
	 *     //Chemin du fichier de destination compressé
	 *     $extras->out="../../extras/extras_".$revision.".js";
	 *     //Chemin du fichier source NON compressé
	 *     $extras->src[]="../../extras/agenda/javascript/agendaAdmin.js";
	 *
	 * Parameters:
	 *	$oFic - {object}
	 */
	function compressJS($oFic){
		$cpt=0;
		$out=$this->rootDir.$oFic->out.$oFic->name;
		if($oFic->version==true){
			$out.="_".$this->version;
		}
		$out.=".js";;
		
		$url=$oFic->out.$oFic->name;
		if($oFic->version==true){
			$url.="_".$this->version;
		}
		$url.=".js";;
		
		$t1=microtime(true);
		$fd=fopen($out,"w");
		$aSrc=$oFic->src;
		
		$message="";
		
		for($i=0; $i<count($aSrc); $i++){
			//Fichier source
			$src=$this->rootDir.$aSrc[$i];
			if($src!=""){
				//On va chercher le script du fichier source
				$script = file_get_contents($src);
				$packer = new JavaScriptPacker($script, 'Normal', true, false);
				$packed = $packer->pack();
				fwrite($fd, $packed);
				$message.='Script \''.$src.'\' insere \n';
				$cpt++;
			}
		}
		fclose($fd);
		$t2 = microtime(true);
		$time = sprintf('%.4f', ($t2 - $t1) );
		$message.= $cpt.' insertions \''.$out.'\' en '.$time.' s. \n\n';
		$result=new stdclass;
		$result->message=$message;
		$result->file=$url;
		$result->format=$oFic->format;
		return $result;
	}
	
	/**
	* Function: compressYUI
	* Va compressé et crypter un fichier JS en utilisant YUI compressor
	*/
	function compressYUI($oFic){
		$cpt=0;
		$out=$this->rootDir.$oFic->out.$oFic->name;
		if($oFic->version==true){
			$out.="_".$this->version;
		}
		$out.=".".$oFic->format;
		
		$url=$oFic->out.$oFic->name;
		if($oFic->version==true){
			$url.="_".$this->version;
		}
		$url.=".".$oFic->format;
		
		$t1=microtime(true);
		$fd=fopen($out,"w");
		$aSrc=$oFic->src;
		
		
		$charset="--charset UTF8";
		if(isset($oFic->charset) && $oFic->charset !='' ){
			$charset="--charset ".$oFic->charset;
		}
		
		$message="";
		
		for($i=0; $i<count($aSrc); $i++){
			//Fichier source
			$src=$this->rootDir.$aSrc[$i];		
			if($src!=""){
				$t=explode(".", $src);
				$type=$t[(count($t)-1)];
				$dest='../../'.$oFic->out.'tmp.'.$type;
				if($type=="css" || 	$type=="js"){
					$cmd="java -jar ..\..\extras\yuicompressor\build\yuicompressor.jar ".$charset." --type ".$type." ".$src." -o ".$dest;//--nomunge
										// $cmd="java -jar ..\..\extras\closure_complier\compiler.jar --js ".$src." --js_output_file ".$dest;

					// echo $cmd."\n";
					exec($cmd);

					if(file_exists($dest)){						
						fwrite($fd, file_get_contents($dest)."\n");						
					}
				}				
				$message.='Script \''.$src.'\' insere \n';
				$cpt++;
			}
		}
		fclose($fd);
		if(isset($dest) && $dest!=""){
			unlink($dest);
		}
		
		$t2 = microtime(true);
		$time = sprintf('%.4f', ($t2 - $t1) );
		$message.= $cpt.' insertions \''.$out.'\' en '.$time.' s. \n\n';
		$result=new stdclass;
		$result->message=$message;
		$result->file=$url;
		$result->format=$oFic->format;
		return $result;
	}
	
	/**
	 * Function: load
	 */
	function load($groupe){
		$file=$this->rootDir.$groupe->out.$groupe->name;
		if($groupe->version==true){
			$file.="_".$this->version;
		}
		
		if($groupe->format == 'css'){
			$file.=".css";
		}
		else{
			$file.=".js";
		}
		
		$opt='';
		if(isset($groupe->opt) && $groupe->opt!== ''){
			$opt=$groupe->opt;
		}
		
	
		if(file_exists($file)){
			if($groupe->format == 'css'){
				echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$file."\" />";
			}
			else{
				echo "<script ".$opt." type=\"text/javascript\" src=\"".$file."\"></script>"; 				
			}
		}
		else{
			for($i=0;$i<count($groupe->src);$i++){
				if($groupe->format == 'css'){
					echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$this->rootDir.$groupe->src[$i]."\" />";
				}
				else{
					echo "<script ".$opt." type=\"text/javascript\" src=\"".$this->rootDir.$groupe->src[$i]."\"></script>";
				}
			}
		}			
	}
	/**
	 * Function: getFiles
	 */
	function getFiles($groupe){
		$file=$this->rootDir.$groupe->out.$groupe->name;
		if($groupe->version==true){
			$file.="_".$this->version;
		}
		
		if($groupe->format == 'css'){
			$file.=".css";
		}
		else{
			$file.=".js";
		}

		$ret=array();		
		if(file_exists($file)){
			$ret[]= $file; 
		}
		else{
			for($i=0;$i<count($groupe->src);$i++){
				$ret[]= $this->rootDir.$groupe->src[$i]; 
			}
		}			
		return $ret;
	}
	/**
	 * Function: getSrc
	 */
	function getSrc($groupe){
		$tab=array();
		$file=$groupe->out.$groupe->name;
		if($groupe->version==true){
			$file.="_".$this->version;
		}
		$file.=".js";
		
		if(file_exists($this->rootDir.$file)){
			$tab[0]=$file;		
		}
		else{			
			$tab=$groupe->src;
		}
		if(count($tab)<1){
			return false;
		}
		else{
			return $tab;
		}
	}
	
	/**
	 * Function: fileExists
	 */
	function fileExists($groupe){
		$file=$this->rootDir.$groupe->out.$groupe->name;
		if($groupe->version==true){
			$file.="_".$this->version;
		}
		$file.=".js";
		//echo $file."\n";
		if(file_exists($file)){
			return true;
		}
		return false;
	}
	
	/**
	 * Function: getFileName
	 */
	function getFileName($groupe){
		$filename=$this->rootDir.$groupe->out.$groupe->name;
		if($groupe->version==true){
			$filename.="_".$this->version;
		}
		$filename.=".js";		
		
		return $filename;
	}	
		
	/**
	* Function: minified_all
	* Va parcourir tout le PATH pour minimifier les JS, CSS et PNG
	* en utilisnat yuicompressor ou pngout
	*
	* Parameters:
	*/
	function minified_all($path, $exclude = ".|..|.svn", $recursive = true, $genre="ALL", $fd) {
		$path = rtrim($path, "/") . "/";
		$folder_handle = opendir($path);
		$exclude_array = explode("|", $exclude);
		
		$result = array();
		while(false !== ($filename = readdir($folder_handle))) {
			if(!in_array(strtolower($filename), $exclude_array) && !in_array($path, $exclude_array)) {
				if(is_dir($path . $filename . "/")) {
					// Need to include full "path" or it's an infinite loop
					if($recursive){
						$result[] = $this->minified_all($path . $filename . "/", $exclude, true, $genre, $fd);	
					}					
				} 
				else {
					$result[] = $filename;
					$cmd="";
					
					$t=explode(".", $filename);
					$type=strtoupper($t[(count($t)-1)]);
					
					if($genre=="JS" && $type=="JS"){
						$cmd="java -jar \"".APP_PATH."extras\yuicompressor\build\yuicompressor.jar\" --charset UTF8 --type ".$type." ".$path.$filename." -o ".$path.$filename;				
					}
					else if($genre=="CSS" && $type=="CSS"){
						$cmd="java -jar \"".APP_PATH."extras\yuicompressor\build\yuicompressor.jar\" --type ".$type." ".$path.$filename." -o ".$path.$filename;	//--charset LATIN1 		
					}
					else if($genre=="PNG" && $type=="PNG"){
						$cmd=APP_PATH."admin\maintenance\lib\pngout.exe ".$path.$filename;
					}
					else if($genre=="ALL"){
						if($type=="CSS" || 	$type=="JS"){
							$charset='';
							if($type=="JS"){
								$charset='--charset UTF8';
							}
							$cmd="java -jar \"".APP_PATH."extras\yuicompressor\build\yuicompressor.jar\" ".$charset." --type ".$type." ".$path.$filename." -o ".$path.$filename;					
						}					
						else if($type=="PNG"){
							$cmd="..\maintenance\lib\pngout.exe ".$path.$filename;
						}
					}
					if($cmd!=""){
						//echo $cmd."\n";
						fwrite($fd, $cmd."\n");
						// exec($cmd);
					}
				}
			}
		}
		return $result;
	}
}
?>