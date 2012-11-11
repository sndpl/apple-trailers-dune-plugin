<?php
	require_once "base_menu.php";
	require_once "trailer_database.php";
	
	class OnDateMenu extends BaseMenu
	{
		private $date;
		
		function __construct($date)
		{
			$this->date = $date;
		}
		
		public function generate_menu()
		{
			$database = new TrailerDatabase();
			
			foreach ($database->database as $movie)
			{
				foreach ($movie->trailers as $trailer)
				{
					$postDate = new DateTime($trailer->postdate);
					$postDate->setTime(0, 0, 0);
					if ($this->date == $postDate->getTimestamp())
					{
						$items[] = array("caption" => $movie->title." - ".$trailer->type, "url" => "trailer:".$movie->location.":".$trailer->type);
					}
				}
			}
			
			usort($items, array("BaseMenu", "CompareCaption"));
			return $this->create_folder_view($items);
		}
	}
?>