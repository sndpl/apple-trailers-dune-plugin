<?php
	require_once "base_menu.php";
	require_once "trailer_database.php";
	
	class TrailerMenu extends BaseMenu
	{
		private $location;
		private $trailerType;
		
		function __construct($location)
		{
			$delimiter = strpos($location, ":");
			$this->location = substr($location, 0, $delimiter);
			$this->trailerType = substr($location, $delimiter + 1);
		}
		
		public function generate_menu()
		{
			$database = new TrailerDatabase();
			$trailers = $database->getTrailerFiles($this->location, $this->trailerType);
			
			$index = 0;
			foreach ($trailers as $trailer)
			{
				$items[] = array("caption" => $trailer["name"], "url" => $this->location.":".$this->trailerType.":".$index);
				++$index;
			}
			
			$this->iconFile = "gui_skin://small_icons/video_file.aai";
			$this->action = PLUGIN_VOD_PLAY_ACTION_ID;
			return $this->create_folder_view($items);
		}
	}
?>