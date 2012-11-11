<?php
	require "main_menu.php";
	require "alphabetical_menu.php";
	require "date_menu.php";
	require "genres_menu.php";
	require "movie_menu.php";
	require "trailer_menu.php";
	require "on_date_menu.php";
	require "genre_menu.php";
	require "play_trailer.php";
	
	class AppleTrailersPlugin implements DunePlugin
	{
		public function get_folder_view($media_url, &$plugin_cookies)
		{
			if (strpos($media_url, "movie:") === 0)
			{
				$menu = new MovieMenu(substr($media_url, 6));
			}
			else if (strpos($media_url, "trailer:") === 0)
			{
				$menu = new TrailerMenu(substr($media_url, 8));
			}
			else if (strpos($media_url, "date:") === 0)
			{
				$menu = new OnDateMenu(substr($media_url, 5));
			}
			else if (strpos($media_url, "genre:") === 0)
			{
				$menu = new GenreMenu(substr($media_url, 6));
			}
			else if ($media_url == "alphabetically")
			{
				$menu = new AlphabeticalMenu();
			}
			else if ($media_url == "bydate")
			{
				$menu = new DateMenu();
			}
			else if ($media_url == "genres")
			{
				$menu = new GenresMenu();
			}
			else
			{
				$menu = new MainMenu();
			}
			
			return $menu->generate_menu();
		}

		public function get_next_folder_view($media_url, &$plugin_cookies)
		{
		}

		public function get_tv_info($media_url, &$plugin_cookies)
		{
		}

		public function get_tv_stream_url($media_url, &$plugin_cookies)
		{
		}

		public function get_vod_info($media_url, &$plugin_cookies)
		{
			$play = new PlayTrailer($media_url);
			return $play->generatePlayInfo();
		}

		public function get_vod_stream_url($media_url, &$plugin_cookies)
		{
		}

		public function get_regular_folder_items($media_url, $from_ndx, &$plugin_cookies)
		{
		}

		public function get_day_epg($channel_id, $day_start_tm_sec, &$plugin_cookies)
		{
		}

		public function get_tv_playback_url($channel_id, $archive_tm_sec, $protect_code, &$plugin_cookies)
		{
		}

		public function change_tv_favorites($op_type, $channel_id, &$plugin_cookies)
		{
		}

		public function handle_user_input(&$user_input, &$plugin_cookies)
		{
		}
	}
?>
