<?php
namespace TJM\WikiConsole\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\Wiki\Wiki;

class CreatePageCommand extends Command{
	static public $defaultName = 'page:create';
	protected $wiki;
	public function __construct(Wiki $wiki){
		$this->wiki = $wiki;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Create wiki page.')
			->addArgument('name', InputArgument::REQUIRED, 'Name of wiki page to create.')
			->addOption('content', 'c', InputOption::VALUE_REQUIRED, 'Content to set for page.', 'stub')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$name = $input->getArgument('name');
		$page = $this->wiki->getPage($name);
		$page->setContent($input->getOption('content'));
		$this->wiki->setPage($name, $page);
		$output->writeln("Page created at " . $this->wiki->getPageFilePath($name, $page));
	}
}
