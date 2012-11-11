<?php
	require_once "base_menu.php";
	require_once "trailer_database.php";
	
	class MovieMenu extends BaseMenu
	{
		private $location;
		
		function __construct($location)
		{
			$this->location = $location;
		}
		
		public function generate_menu()
		{
			$database = new TrailerDatabase();
			$movie = $database->findMovie($this->location);
			
			foreach ($movie->trailers as $trailer)
			{
				$items[] = array("caption" => $trailer->type, "url" => "trailer:".$this->location.":".$trailer->type);
			}
			
			usort($items, array("BaseMenu", "CompareCaption"));
			return $this->create_folder_view($items);
		}
	}
?>