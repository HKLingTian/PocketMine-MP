<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace PocketMine\Wizard;

use PocketMine;

class InstallerLang{
	public static $languages = array(
		"en" => "English",
		"es" => "Español",
		"zh" => "中文",
		"ru" => "Pyccĸий",
		"ja" => "日本語",
		"de" => "Deutsch",
		//"vi" => "Tiếng Việt",
		"ko" => "한국어",
		"fr" => "Français",
		"it" => "Italiano",
		//"lv" => "Latviešu",
		"nl" => "Nederlands",
		//"pt" => "Português",
		"sv" => "Svenska",
		"fi" => "Suomi",
		"tr" => "Türkçe",
		//"et" => "Eesti",
	);
	private $texts = array();
	private $lang;
	private $langfile;

	public function __construct($lang = ""){
		if(file_exists(\PocketMine\PATH . "src/lang/Installer/" . $lang . ".ini")){
			$this->lang = $lang;
			$this->langfile = \PocketMine\PATH . "src/lang/Installer/" . $lang . ".ini";
		}else{
			$l = glob(\PocketMine\PATH . "src/lang/Installer/" . $lang . "_*.ini");
			if(count($l) > 0){
				$files = array();
				foreach($l as $file){
					$files[$file] = filesize($file);
				}
				arsort($files);
				reset($files);
				$l = key($files);
				$l = substr($l, strrpos($l, "/") + 1, -4);
				$this->lang = isset(self::$languages[$l]) ? $l : $lang;
				$this->langfile = \PocketMine\PATH . "src/lang/Installer/" . $l . ".ini";
			}else{
				$this->lang = "en";
				$this->langfile = \PocketMine\PATH . "src/lang/Installer/en.ini";
			}
		}

		$this->loadLang(\PocketMine\PATH . "src/lang/Installer/en.ini", "en");
		if($this->lang !== "en"){
			$this->loadLang($this->langfile, $this->lang);
		}

	}

	public function getLang(){
		return ($this->lang);
	}

	public function loadLang($langfile, $lang = "en"){
		$this->texts[$lang] = array();
		$texts = explode("\n", str_replace(array("\r", "\/\/"), array("", "//"), file_get_contents($langfile)));
		foreach($texts as $line){
			$line = trim($line);
			if($line === ""){
				continue;
			}
			$line = explode("=", $line);
			$this->texts[$lang][array_shift($line)] = str_replace(array("\\n", "\\N",), "\n", implode("=", $line));
		}
	}

	public function get($name, $search = array(), $replace = array()){
		if(!isset($this->texts[$this->lang][$name])){
			if($this->lang !== "en" and isset($this->texts["en"][$name])){
				return $this->texts["en"][$name];
			}else{
				return $name;
			}
		}elseif(count($search) > 0){
			return str_replace($search, $replace, $this->texts[$this->lang][$name]);
		}else{
			return $this->texts[$this->lang][$name];
		}
	}

	public function __get($name){
		return $this->get($name);
	}

}