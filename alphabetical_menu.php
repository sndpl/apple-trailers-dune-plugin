<?php
	require_once "base_menu.php";
	require_once "trailer_database.php";
	
	class AlphabeticalMenu extends BaseMenu
	{
		public function generate_menu()
		{
			$database = new TrailerDatabase();
			
			$items = array();
			foreach ($database->database as $movie)
			{
				$items[] = array("caption" => $movie->title, "url" => "movie:".$movie->location);
			}
			
			usort($items, array("BaseMenu", "CompareCaption"));
			
			return $this->create_folder_view($items);
		}
	}
?>