<?php
namespace TJM\WikiConsole;
use TJM\Component\Console\Application;
use TJM\Wiki\Wiki;

class Console extends Application{
	protected $wiki;
	public function __construct(Wiki $wiki, $config = null){
		$this->wiki = $wiki;
		parent::__construct($config);
		foreach(glob(__DIR__ . '/Command/*Command.php') as $file){
			$class = "TJM\\WikiConsole\\Command\\" . pathinfo($file, PATHINFO_FILENAME);
			$this->add(new $class($this->wiki));
		}
	}
}
