<?php
namespace TJM\WikiConsole\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\Wiki\Wiki;

class CommitPageCommand extends Command{
	static public $defaultName = 'page:commit';
	protected $wiki;
	public function __construct(Wiki $wiki){
		$this->wiki = $wiki;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Commit wiki page changes.')
			->addArgument('name', InputArgument::REQUIRED, 'Name of wiki page to commit.')
			->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Commit message text.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$name = $input->getArgument('name');
		if(!$this->wiki->hasPage($name)){
			throw new Exception("Page {$name} doesn't exist");
		}
		if(!$this->wiki->commitPage($name, $this->wiki->getPage($name), $input->getOption('message'))){
			throw new Exception("Failed to commit page");
		}
	}
}
