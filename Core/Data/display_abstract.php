<?php

abstract class data_display{

	protected $indexes;
	private $recusive = true;
	

	public function __construct($indexes, $recursive) {
		$this->indexes = $indexes;
		$this->recursive = $recursive;
		$this->init();
	}

	abstract public function init();
	
	public function populateItem($key, $value){
		$type = gettype($value);
		$functype = "populate_".$type;
		strpos($this->indexes["type"], "array")
		if(strpos($i["type"], "integer:") === 0 && $recursive){
			$clasname = explode(":",$indexes["type"]);
			$obj = Extended_Object::find(array("classname"=>$clasname[1]));
			include_once($obj->location);
			$classname = $obj->classname;
			$value = new $classname($value);
			$functype = "populate_object";
		}
		$this->$functype($key, $value);
		
	}
	abstract public function getMimeExstention();
	abstract public function populate_array($key, $value);
	abstract public function populate_object($key, $value);
	abstract public function populate_integer($key, $value);
	abstract public function populate_string($key, $value);
	
	abstract public function getDisplay();


}

class xml_display extends data_display{
	
	private $xmlDoc;
	private $root;
	
	public function init(){
		$this->xmlDoc = new DOMDocument();
		$this->xmlDoc->loadXML('<?xml version="1.0" encoding="ISO-8859-1"?><'.get_called_class().'></'.get_called_class().'>');
		$this->root = $this->xmlDoc->documentElement;
	}
	
	public function populate_array($key, $value){
		$this->root->appendChild(($temp = $this->xmlDoc->createElement($key)));
		foreach($value as $ak=>$av){
			$ak = (is_numeric($ak))?"item":$ak;
			$temp->appendChild($this->xmlDoc->createElement($ak, $av));					
		}
	}
	public function populate_object($key, $value){
		$this->root->appendChild(($temp = $this->xmlDoc->createElement($key)));
		$xml = $value->displayAs("xml",false);
		$node = $xmlDoc->importNode($xml->documentElement, true);
		$temp->appendChild($node);
	}
	public function populate_integer($key, $value){
		$this->root->appendChild(($temp = $this->xmlDoc->createElement($key)));
		$temp->appendChild($this->xmlDoc->createTextNode($value));
	}
	public function populate_string($key, $value){
		$this->root->appendChild(($temp = $this->xmlDoc->createElement($key)));
		$temp->appendChild($this->xmlDoc->createTextNode($value));	
	}
	
	public getDisplay(){
		return $xml->saveXML();
	}
}