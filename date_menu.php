<?php
	require_once "base_menu.php";
	require_once "trailer_database.php";
	
	class DateMenu extends BaseMenu
	{
		public function generate_menu()
		{
			$database = new TrailerDatabase();
			$dates = $database->getDates();
			rsort($dates);
			
			foreach ($dates as $date)
			{
				$items[] = array("caption" => strftime("%Y-%m-%d", $date), "url" => "date:".$date);
			}
			
			return $this->create_folder_view($items);
		}
	}
?>