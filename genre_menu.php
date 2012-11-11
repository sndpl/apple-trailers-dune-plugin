<?php
	require_once "base_menu.php";
	require_once "trailer_database.php";
	
	class GenreMenu extends BaseMenu
	{
		private $genre;
		
		function __construct($genre)
		{
			$this->genre = $genre;
		}
		
		public function generate_menu()
		{
			$database = new TrailerDatabase();
			
			foreach ($database->database as $movie)
			{
				if (in_array($this->genre, $movie->genre))
				{
					$items[] = array("caption" => $movie->title, "url" => "movie:".$movie->location);
				}
			}
			
			usort($items, array("BaseMenu", "CompareCaption"));
			return $this->create_folder_view($items);
		}
	}
?>