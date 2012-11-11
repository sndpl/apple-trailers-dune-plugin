<?php
	require_once "lib/utils.php";
	
	class TrailerDatabase
	{
		public $database;
		private $nullReturn = null;
		
		function __construct()
		{
			global $appleTrailerDatabase;
			if (isset($appleTrailerDatabase))
			{
				$this->database = $appleTrailerDatabase;
			}
			else
			{
				$this->loadTrailers();
			}
		}
		
		private function loadTrailers()
		{
			$this->database = array();
			
			$this->addTrailers("http://trailers.apple.com/trailers/home/feeds/studios.json");
			$this->addTrailers("http://trailers.apple.com/trailers/home/feeds/just_added.json");
			$this->addTrailers("http://trailers.apple.com/trailers/home/feeds/genres.json");
			$this->addTrailers("http://trailers.apple.com/trailers/home/feeds/exclusive.json");
			$this->addTrailers("http://trailers.apple.com/trailers/home/feeds/just_hd.json");
			$this->addTrailers("http://trailers.apple.com/trailers/home/feeds/most_pop.json");

			global $appleTrailerDatabase;
			$appleTrailerDatabase = $this->database;
		}
		
		public function &findMovie($location)
		{
			foreach ($this->database as &$movie)
			{
				if ($movie->location == $location)
				{
					return $movie;
				}
			}
			
			return $this->nullReturn;
		}
		
		public function hasMovie($location)
		{
			$movie = $this->findMovie($location);
			return isset($movie);
		}
		
		public function getDates()
		{
			$dates = array();
			foreach ($this->database as $movie)
			{
				foreach ($movie->trailers as $trailer)
				{
					$time = new DateTime($trailer->postdate);
					$time->setTime(0, 0, 0);
					$dates[] = $time->getTimestamp();
				}
			}
			
			return array_unique($dates);
		}
		
		public function getGenres()
		{
			$genres = array();
			foreach ($this->database as $movie)
			{
				foreach ($movie->genre as $genre)
				{
					$genres[] = $genre;
				}
			}
			
			return array_unique($genres);
		}
		
		public function &findTrailer($location, $trailerType)
		{
			$movie = &$this->findMovie($location);
			if (isset($movie))
			{
				return $this->findTrailerInMovie($movie, $trailerType);
			}
			
			return $this->nullReturn;
		}
		
		private function &findTrailerInMovie(&$movie, $trailerType)
		{
			foreach ($movie->trailers as &$movieTrailer)
			{
				if ($movieTrailer->type == $trailerType)
				{
					return $movieTrailer;
				}
			}
			
			return $this->nullReturn;
		}
		
		private function hasTrailer($movie, $trailerType)
		{
			$trailer = $this->findTrailerInMovie($movie, $trailerType);
			return isset($trailer);
		}
		
		private function mergeMovies(&$movie, $merge)
		{
			foreach ($merge->trailers as $trailer)
			{
				if (!$this->hasTrailer($movie, $trailer->type))
				{
					$movie->trailers[] = $trailer;
				}
			}
		}
		
		private function addTrailers($url)
		{
			$data = HD::http_get_document($url);
			$movies = json_decode($data);
			
			foreach ($movies as $movie)
			{
				$foundMovie = &$this->findMovie($movie->location);
				if (isset($foundMovie))
				{
					$this->mergeMovies($foundMovie, $movie);
				}
				else
				{
					$this->database[] = $movie;
				}
			}
		}
		
		private function parseTrailerXml($trailer)
		{
			try
			{
				$data = HD::http_get_document(str_replace("/trailers/", "http://trailers.apple.com/moviesxml/s/", $trailer->url).strtolower(str_replace(" ", "", $trailer->type)).".xml");
				$xml = HD::parse_xml_document($data);
				
				$trailerFiles = array();
				foreach ($xml->TrackList->plist->dict->array->children() as $trailerFile)
				{
					$trailerInfo = array();
					$currentKey = null;
					
					foreach ($trailerFile->children() as $dictItem)
					{
						if (!isset($currentKey))
						{
							$currentKey = (string) $dictItem;
						}
						else
						{
							$trailerInfo[$currentKey] = (string) $dictItem;
							$currentKey = null;
						}
					}
					
					$trailerFiles[] = array("name" => $trailerInfo["songName"], "url" => $trailerInfo["previewURL"]);
				}
				
				return array_reverse($trailerFiles);
			}
			catch(Exception $ex)
			{
				hd_print($ex->getMessage());
				return null;
			}
		}
		
		private function parseTrailerHTML($trailer)
		{
			try
			{
				$data = HD::http_get_document("http://trailers.apple.com".$trailer->url."includes/playlists/web.inc");
				preg_match_all('#<a href="(.*\.mov)" rel="\./itsxml/[0-9]*-'.strtolower(str_replace(" ", "", $trailer->type)).'\.xml"[^>]*>([^\(]*).*</a>#', $data, $matches, PREG_SET_ORDER);
				
				$trailerFiles = array();
				foreach ($matches as $trailerFile)
				{
					$trailerFiles[] = array("name" => trim($trailerFile[2]), "url" => $trailerFile[1]);
				}
				
				return array_reverse($trailerFiles);
			}
			catch(Exception $ex)
			{
				hd_print($ex->getMessage());
				return null;
			}
		}
		
		public function getTrailerFiles($location, $trailerType)
		{
			$trailerFiles = null;
			
			$trailer = &$this->findTrailer($location, $trailerType);
			if (isset($trailer))
			{
				if (isset($trailer->trailerFiles))
				{
					$trailerFiles = $trailer->trailerFiles;
				}
				else
				{
					$trailerFiles = $this->parseTrailerXml($trailer);
					
					if (!isset($trailerFiles))
					{
						$trailerFiles = $this->parseTrailerHTML($trailer);
					}
					
					if (isset($trailerFiles))
					{
						$trailer->trailerFiles = $trailerFiles;
					}
				}
			}
			
			return $trailerFiles;
		}
	}
?>