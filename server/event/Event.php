<?php
class Event {
	private $db = null;
	public function __construct($db) {
		$this->db = $db;
	}
	function getActiveEvents() {
		$event = $this->db->query ( "
				SELECT * FROM events
				WHERE event_date >= NOW()
				ORDER BY event_date ASC
				LIMIT 5
				" );
		return $event;
	}
	function getAllActiveEvents() {
		$event = $this->db->query ( "
				SELECT * FROM events
				WHERE event_date >= NOW()
				ORDER BY event_id ASC
				" );
		return $event;
	}
	function getAllEvents() {
		$event = $this->db->query ( "
				SELECT * FROM events
				ORDER BY event_id DESC
				" );
		return $event;
	}
	function getPastEvents() {
		$event = $this->db->query ( "
				SELECT * FROM events
				WHERE event_date < NOW()
				ORDER BY event_date DESC
				LIMIT 5
				" );
		return $event;
	}
	function addEvent($req) {
		$name = $req ["event_name"];
		$date = $req ["event_date"];
		$description = $req ["event_description"];
		$event = $this->db->query ( "
				INSERT INTO 
				events (event_name, event_date, event_description) 
				VALUES (:event_name, :event_date, :event_description)
				", array (
				"event_name" => $name,
				"event_date" => $date,
				"event_description" => $description 
		) );
		return $event;
	}
	function updateEvent($req) {
		$id = $req ["event_id"];
		$name = $req ["event_name"];
		$date = $req ["event_date"];
		$description = $req ["event_description"];
		$event = $this->db->query ( "
				UPDATE events
				SET
				event_name = :event_name,
				event_date = :event_date,
				event_description = :event_description
				WHERE event_id = :event_id;
				", array (
				"event_id" => $id,
				"event_name" => $name,
				"event_date" => $date,
				"event_description" => $description 
		) );
		return $event;
	}
	function deleteEvent($req) {
		$id = $req ["event_id"];
		$event = $this->db->query ( "
				DELETE FROM events
				WHERE event_id = :event_id;
				", array (
							"event_id" => $id
					) );
		return $event;
	}
}
?>