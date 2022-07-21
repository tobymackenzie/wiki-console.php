<?php
namespace TJM\WikiConsole\Tests;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TJM\Wiki\Wiki;
use TJM\WikiConsole\Console;

class ConsoleTest extends TestCase{
	const WIKI_DIR = __DIR__ . '/tmp';
	static public function setUpBeforeClass(): void{
		mkdir(self::WIKI_DIR);
	}
	protected function tearDown(): void{
		shell_exec("rm -rf " . self::WIKI_DIR . "/.git && rm -rf " . self::WIKI_DIR . "/*");
	}
	static public function tearDownAfterClass(): void{
		rmdir(self::WIKI_DIR);
	}

	public function testCommit(){
		$wiki = new Wiki(self::WIKI_DIR);
		$console = new Console($wiki);
		$file = self::WIKI_DIR . '/1.md';
		$content = "test\nbar\n123";
		$tester = new CommandTester($console->find('commit'));
		file_put_contents($file, $content);
		$wiki->runGit('add ' . $file);
		$tester->execute([
			'--message'=> 'Initial commit',
		]);
		$this->assertEquals("Initial commit", $wiki->runGit('log --pretty="%s"'));
		$content .= "\n456";
		file_put_contents($file, $content);
		$tester->execute([
			'files'=> ['1.md'],
		]);
		$this->assertMatchesRegularExpression("/content: [\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2}\nInitial commit/m", $wiki->runGit('log --pretty="%s"'));
	}
	public function testStage(){
		$wiki = new Wiki(self::WIKI_DIR);
		$console = new Console($wiki);
		$statusCommand = '-c color.status=false status --short';
		$file1 = self::WIKI_DIR . '/1.md';
		$tester = new CommandTester($console->find('stage'));
		$this->assertEquals("", $wiki->runGit($statusCommand));
		file_put_contents($file1, 'abc');
		$tester->execute([
			'files'=> ['1.md'],
		]);
		$this->assertEquals("A  1.md", $wiki->runGit($statusCommand));
		$file2 = self::WIKI_DIR . '/2.md';
		file_put_contents($file2, 'abc');
		$tester->execute([
			'files'=> ['1.md', '2.md'],
		]);
		$this->assertEquals("A  1.md\nA  2.md", $wiki->runGit($statusCommand));
		file_put_contents($file2, 'abcd');
		$tester->execute([
			'--all'=> true,
		]);
		$this->assertEquals("A  1.md\nA  2.md", $wiki->runGit($statusCommand));
	}
}
