<?php
	require_once "base_menu.php";
	require_once "trailer_database.php";
	
	class GenresMenu extends BaseMenu
	{
		public function generate_menu()
		{
			$database = new TrailerDatabase();
			$genres = $database->getGenres();
			sort($genres);
			
			foreach ($genres as $genre)
			{
				$items[] = array("caption" => $genre, "url" => "genre:".$genre);
			}
			
			return $this->create_folder_view($items);
		}
	}
?>