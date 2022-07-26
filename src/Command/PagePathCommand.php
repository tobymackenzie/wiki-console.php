<?php
namespace TJM\WikiConsole\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\Wiki\Wiki;

class PagePathCommand extends Command{
	static public $defaultName = 'page:path';
	protected $wiki;
	public function __construct(Wiki $wiki){
		$this->wiki = $wiki;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Get wiki page file path.')
			->addArgument('name', InputArgument::REQUIRED, 'Name of wiki page to get path for.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$name = $input->getArgument('name');
		$output->writeln($this->wiki->getPageFilePath($name));
	}
}
