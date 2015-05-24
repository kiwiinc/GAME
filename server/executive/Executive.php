<?php
class Executive
{
	private $db = null;
	
	public function __construct($db) {
		$this->db = $db;
	}
	
	function getEvents () {
		$executive = $this->db->query("
				SELECT * FROM executives
				");
		return $executive;
	}
	
	function addEvent ($req) {
		$name = $req["executive_name"];
		$title = $req["executive_title"]; 
		$description = $req["executive_description"];
		$executive = $this->db->query("
				INSERT INTO 
				executives (executive_name, executive_title, executive_description) 
				VALUES (:executive_name, :executive_title, :executive_description)
				", array(
						"executive_name" => $name,
						"executive_title" => $title,
						"executive_description" => $description
				));
		return $executive;
		
	}
	
	function updateEvent () {
		
	}
}
?>