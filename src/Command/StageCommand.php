<?php
namespace TJM\WikiConsole\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\Wiki\Wiki;

class StageCommand extends Command{
	static public $defaultName = 'stage';
	protected $wiki;
	public function __construct(Wiki $wiki){
		$this->wiki = $wiki;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Stage wiki changes.')
			->addArgument('files', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Files to stage.')
			->addOption('all', 'a', InputOption::VALUE_NONE, 'Add all files.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$files = $input->getArgument('files');
		if($input->getOption('all')){
			$files[] = Wiki::STAGE_ALL;
		}
		$this->wiki->stage($files);
	}
}
