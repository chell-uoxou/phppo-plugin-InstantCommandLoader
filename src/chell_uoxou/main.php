<?php
namespace InstantCommandLoader;
use phppo\system\systemProcessing as systemProcessing;
use phppo\command\plugincommand\addcommand as addcommand;
$pluginAddCommand = new addcommand;
$pluginAddCommand -> addcommand("InstantCommandLoader","icl","plugin","InstantCommandLoader plugin command.","<reload|help>");
$icl_extensions = array();
class ICL extends systemProcessing{

	protected $icl_extensions;
	function __construct(){
		# code...
	}
	public function onLoad(){
		global $poPath;
		global $plugindata;
		global $pluginAddCommand;
		$version = $plugindata["InstantCommandLoader"]["version"];
		// $this->addlog("loaded");
		$iniPath = $poPath . "/" . "root/bin/InstantCommandLoader/";
		if (!is_file($iniPath . "config.ini")){
			if (!is_dir($poPath . "/root/bin/InstantCommandLoader")) {
				mkdir($poPath . "/root/bin/InstantCommandLoader");
			}
			$this->addlog("Thank you for downloading ICL v.{$version}!");
			$this->addlog("Preparing...");
			// $this->addlog("Creating plugin config file...");
			touch($iniPath . "config.ini");
			// $this->addlog("Creating extensions ini file...");
			touch($iniPath . "extensions.ini");
			// $this->addlog("Creating descriptions ini file...");
			touch($iniPath . "extension_descriptions.ini");
 		// 	$this->addlog("Creating descriptions ini file...");
			mkdir($iniPath . "includes");
			$this->addlog("Preparation has been completed! : {$iniPath}");
		}
		$this->loadFiles();
	}
	public function onCommand_event(){
		global $baseCommand;
		global $icl_extensions;
		$exarray = $icl_extensions;
		// var_dump($icl_extensions);////////////////////////
		if (array_key_exists($baseCommand,$exarray)) {
			// echo "exist";//////////////////
			include $exarray[$baseCommand]["path"];
			return true;
		}else{
			return false;
		}
	}

	public function onCommand(){
		global $icl_extensions;
		global $tipe_text;
		global $baseCommand;
		global $icl_extensions;
		$exarray = $icl_extensions;
		$input = substr($tipe_text,4);
		if (array_key_exists($baseCommand,$exarray)) {
			$this->onCommand_event();
		}else{
			switch ($input) {
				case 'reload':
					$this->addlog("Extension file reloading...");
					$this->loadFiles();
					$this->addlog("Reload completed");
					break;
				case 'help':
					$this->addlog("=== ICL command usage ===");
					$this->info("icl reload : reloadcommands.");
					$this->info("icl help   : show help of ICL.");
					$this->info('===== How to install =====
1. Put on "root/plugins" directory.
2. Boot PHP Prompt OS and check the existence of "root/bin/InstantCommandLoader" directory.
3. Put raw PHP files on "root/bin/InstantCommandLoader/includes".
4. Write extension command and path (like "command = path") on "root/bin/InstantCommandLoader/extensions.ini"
5. Write command description (like "command = description") on "root/bin/InstantCommandLoader/extension_descriptions.ini" (optional)
6. Run "icl reload" command.
7. Enjoy!');
					break;
				default:
					$this->addlog("=== ICL command usage ===");
					$this->info("icl reload : reloadcommands.");
					$this->info("icl help   : show help of ICL.");
					break;
			}
		}
	}
	public function icl_protected_addlog($value,$type = "info"){
		$this->addlog($value,$type);
	}

	protected function loadFiles(){
		global $poPath;
		global $icl_extensions;
		$pluginAddCommand = new addcommand;
		$ini_path = $poPath . "/root/bin/InstantCommandLoader/extensions.ini";
		if (is_file($ini_path)) {
			$this->icl_extension_ini_ary = parse_ini_file($ini_path);
		}else{
			$this->icl_protected_addlog("\"extensions.ini\" does not exist! Createing new file...","error");
			touch($ini_path);
			$this->icl_extension_ini_ary = array();
		}

		$ini_des_path = $poPath . "/root/bin/InstantCommandLoader/extension_descriptions.ini";
		if (is_file($ini_des_path)) {
			$this->icl_extension_des_ini_ary = parse_ini_file($ini_des_path);
		}else{
			$this->icl_protected_addlog("\"extensions.ini\" does not exist! Createing new file...","error");
			touch($ini_des_path);
			$this->icl_extension_des_ini_ary = array();
		}

		foreach ($this->icl_extension_ini_ary as $key => $value) {
			$icl_command = $key;
			$icl_path = $value;
			if (is_file($poPath . "/root/bin/InstantCommandLoader/includes/" . $value)) {
				$icl_extensions[$key]["command"] = $key;
				$icl_extensions[$key]["path"] = $poPath . "/root/bin/InstantCommandLoader/includes/" . $value;
				// var_dump($this->icl_extension_des_ini_ary);//////////////////////////////////////
				if (array_key_exists($key,$this->icl_extension_des_ini_ary)) {
					$icl_des = $this->icl_extension_des_ini_ary[$key];
				}else{
					$icl_des = "a InstantCommandLoader plugin command.";
				}
				$pluginAddCommand -> addcommand("InstantCommandLoader",$key,"plugin",$icl_des);
			}else{
				$this->icl_protected_addlog("The php does not exist! : [{$icl_command}] command = {$icl_path}","error");
			}
		}
		// $icl_extensions = array();
		// $includespath = $poPath . "/root/bin/InstantCommandLoader/includes";
		// $files = scandir($includespath);
		// foreach ($files as $value) {
		// 	if ($value != "." && $value != "..") {
		// 		$icl_fullpath_array[] = $value;
		// 	}
		// }
	}
}
