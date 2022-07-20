<?php
namespace TJM\WikiConsole\Tests;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use TJM\Wiki\Wiki;
use TJM\WikiConsole\Console;

class PageTest extends TestCase{
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

	public function testCommitPage(){
		$wiki = new Wiki(self::WIKI_DIR);
		$console = new Console($wiki);
		$name = 'foo';
		$content = "test\n{$name}\n123";
		mkdir(self::WIKI_DIR . '/' . $name);
		file_put_contents(self::WIKI_DIR . '/' . $name . '/' . $name . '.md', $content);
		$tester = new CommandTester($console->find('page:commit'));
		$tester->execute([
			'--message'=> 'Initial commit',
			'name'=> $name,
		]);
		$this->assertEquals("Initial commit", $wiki->runGit('log --pretty="%s"'));
		$content .= "\n456";
		file_put_contents(self::WIKI_DIR . '/' . $name . '/' . $name . '.md', $content);
		$tester->execute([
			'name'=> $name,
		]);
		$this->assertMatchesRegularExpression("/content\\({$name}\\): [\d]{4}-[\d]{2}-[\d]{2} [\d]{2}:[\d]{2}:[\d]{2}\nInitial commit/m", $wiki->runGit('log --pretty="%s"'));
	}
	public function testCreatePage(){
		$wiki = new Wiki(self::WIKI_DIR);
		$console = new Console($wiki);
		$tester = new CommandTester($console->find('page:create'));
		$tester->execute([
			'name'=> 'foo',
		]);
		$this->assertEquals("stub", file_get_contents(self::WIKI_DIR . '/foo/foo.md'));
		$tester->execute([
			'--content'=> "asdf\n1234",
			'name'=> 'bar',
		]);
		$this->assertEquals("asdf\n1234", file_get_contents(self::WIKI_DIR . '/bar/bar.md'));
	}
	public function testOpenPage(){
		$wiki = new Wiki(self::WIKI_DIR);
		$console = new Console($wiki);
		$tester = new CommandTester($console->find('page:open'));
		$tester->execute([
			'--command'=> 'cat',
			'name'=> 'foo',
		], [
			'interactive'=> false,
		]);
		$this->assertEquals("\n", $tester->getDisplay());
		file_put_contents(self::WIKI_DIR . '/foo/foo.md', '1234');
		$tester->execute([
			'--command'=> 'cat',
			'name'=> 'foo',
		], [
			'interactive'=> false,
		]);
		$this->assertEquals("1234\n", $tester->getDisplay());
	}
	public function testPagePath(){
		$wiki = new Wiki(self::WIKI_DIR);
		$console = new Console($wiki);
		$tester = new CommandTester($console->find('page:path'));
		foreach([
			'foo'=> self::WIKI_DIR . "/foo/foo.md\n",
			'bar'=> self::WIKI_DIR . "/bar/bar.md\n",
			// 'foo.bar'=> self::WIKI_DIR . "/foo.bar/foo.bar.md\n",
			'foo-bar'=> self::WIKI_DIR . "/foo-bar/foo-bar.md\n",
		] as $given=> $expect){
			$tester->execute([
				'name'=> $given,
			]);
			$this->assertEquals($expect, $tester->getDisplay());
		}
	}


	/*=====
	==assert
	=====*/
	protected function assertException($expect, $run, $message = null){
		try{
			$run();
		}catch(Exception $e){
			$this->assertInstanceOf($expect, $e, "Exception should be instance of {$expect}");
			return true;
		}
		$this->fail($message ?: "No exception thrown, {$expect} expected");
	}
}
