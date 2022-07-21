<?php
namespace TJM\WikiConsole\Command;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\Wiki\Wiki;

class CommitCommand extends Command{
	static public $defaultName = 'commit';
	protected $wiki;
	public function __construct(Wiki $wiki){
		$this->wiki = $wiki;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Commit staged wiki changes.')
			->addArgument('files', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Files to commit.')
			->addOption('message', 'm', InputOption::VALUE_REQUIRED, 'Commit message text.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		if($input->getArgument('files')){
			$this->wiki->stage($input->getArgument('files'));
		}
		if(!$this->wiki->commit($input->getOption('message'))){
			throw new Exception("Failed to commit wiki");
		}
	}
}
