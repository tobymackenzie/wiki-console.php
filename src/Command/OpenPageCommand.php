<?php
namespace TJM\WikiConsole\Command;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TJM\Wiki\Wiki;

class OpenPageCommand extends Command{
	static public $defaultName = 'page:open';
	protected $wiki;
	public function __construct(Wiki $wiki){
		$this->wiki = $wiki;
		parent::__construct();
	}
	protected function configure(){
		$this
			->setDescription('Open wiki page in editor.')
			->addArgument('name', InputArgument::REQUIRED, 'Name of wiki page to edit.')
			->addOption('command', 'c', InputOption::VALUE_REQUIRED, 'Command to run to edit file.')
		;
	}
	protected function execute(InputInterface $input, OutputInterface $output){
		$name = $input->getArgument('name');
		$page = $this->wiki->getPage($name);
		if(!$this->wiki->hasPage($name)){
			$this->wiki->setPage($name, $page);
		}
		$command = $input->getOption('command') ?: 'open {{path}} || vi {{path}} || ed {{path}}';
		if(strpos('{{', $command) === false){
			$command .= ' {{path}}';
		}
		$result = $this->wiki->run([
			'command'=> $command,
			'interactive'=> $input->isInteractive(),
		], $name, $page);
		if(!$input->isInteractive()){
			$output->writeln($result);
		}
	}
}
