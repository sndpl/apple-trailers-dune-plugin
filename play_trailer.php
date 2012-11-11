<?php
	require_once "trailer_database.php";

	class PlayTrailer
	{
		private $location;
		private $trailerType;
		private $fileIndex;
		
		function __construct($info)
		{
			$delimiter = strpos($info, ":");
			$this->location = substr($info, 0, $delimiter);
			$delimiter2 = strpos($info, ":", $delimiter + 1);
			$this->trailerType = substr($info, $delimiter + 1, $delimiter2 - $delimiter - 1);
			$this->fileIndex = substr($info, $delimiter2 + 1);
		}
		
		private function startServer()
		{
			exec("pgrep dunetrailers", $processes);
			if (count($processes) == 0)
			{
				$path = DuneSystem::$properties["install_dir_path"]."/bin";
				hd_print("Starting Server: ".$path."/dunetrailers");
				exec("( cd ".$path."; ./dunetrailers ) &> /dev/null 2>&1 &");
			}
		}
		
		public function generatePlayInfo()
		{
			$this->startServer();
			
			$database = new TrailerDatabase();
			$movie = $database->findMovie($this->location);
			$trailer = $database->findTrailer($this->location, $this->trailerType);
			$files = $database->getTrailerFiles($this->location, $this->trailerType);
			$file = $files[$this->fileIndex];
			
			return array(
					PluginVodInfo::name					=>	$movie->title,
					PluginVodInfo::description			=>	"",
					PluginVodInfo::poster_url			=>	$movie->poster,
					PluginVodInfo::series				=>
						array(
							array(
								PluginVodSeriesInfo::name						=>	$trailer->type,
								PluginVodSeriesInfo::playback_url				=>	"http://mp4://127.0.0.1:7070/?trailer=".$file["url"],
								PluginVodSeriesInfo::playback_url_is_stream_url	=>	true
							),
						),
					PluginVodInfo::initial_series_ndx	=>	0,
					PluginVodInfo::initial_position_ms	=>	0,
					PluginVodInfo::advert_mode			=>	false,
					PluginVodInfo::ip_address_required	=>	true,
					PluginVodInfo::valid_time_required	=>	false
					);
		}
	}
?>