<?php
	require_once "base_menu.php";
	
	class MainMenu extends BaseMenu
	{
		public function generate_menu()
		{
			$menu_items = array(
							array("caption" => "Alphabetically", "url" => "alphabetically"),
							array("caption" => "By Date", "url" => "bydate"),
							array("caption" => "Genres", "url" => "genres")
						);
			
			return $this->create_folder_view($menu_items);
		}
	}
?>