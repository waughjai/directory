<?php

use PHPUnit\Framework\TestCase;
use WaughJ\Directory\Directory;

class DirectoryTest extends TestCase
{
	public function testEmptyDirectory()
	{
		$dir = new Directory( '' );
		$this->assertEquals( 0, count( $dir->getDirectoryChain() ) );
		$this->assertEquals( ( string )( $dir ), '' );
	}

	public function testConsistency()
	{
		$dir1 = new Directory( '/var/www/html/' );
		$dir2 = new Directory( 'var\www\html' );
		$dir3 = new Directory([ 'var', 'www', 'html' ]);
		$this->assertEquals( $dir1->getDirectoryChain(), $dir2->getDirectoryChain() );
		$this->assertEquals( $dir2->getDirectoryChain(), $dir3->getDirectoryChain() );
		$this->assertEquals( $dir1->getDirectoryChain(), $dir3->getDirectoryChain() );
		$this->assertEquals( ( string )( $dir1 ), ( string )( $dir2 ) );
		$this->assertEquals( ( string )( $dir2 ), ( string )( $dir3 ) );
		$this->assertEquals( ( string )( $dir1 ), ( string )( $dir3 ) );
		$this->assertEquals( $dir1, $dir2 );
		$this->assertEquals( $dir2, $dir3 );
		$this->assertEquals( $dir1, $dir3 );
	}

	public function testWindows()
	{
		$dir1 = new Directory([ 'C:', 'Program Files', 'Directory Test' ]);
		$this->assertEquals( 'C:\Program Files\Directory Test', $dir1->getStringWindows() );
	}

	public function testURL()
	{
		$dir1 = new Directory([ 'https://www.jaimeson-waugh.com', 'index.html' ]);
		$this->assertEquals( 'https://www.jaimeson-waugh.com/index.html', $dir1->getStringURL() );
	}

	public function testAdding()
	{
		$dir1 = new Directory([ 'var', 'www', 'html' ]);
		$dir2 = '/jaimeson-waugh.com/public';
		$full_dir = $dir1->addDirectory( $dir2 );
		$this->assertEquals( $full_dir, "/var/www/html/jaimeson-waugh.com/public/" );
		$dir3 = new Directory( 'example.com' );
		$this->assertTrue( is_a( $dir3, Directory::class ) );
		$this->assertEquals( ['example.com'], $dir3->getDirectoryChain() );
		$full_dir_2 = $dir1->addDirectory( $dir3 );
		//$this->assertEquals( $full_dir, "/var/www/html/example.com/" );
	}

	public function testParent()
	{
		$dir1 = new Directory([ 'var', 'www', 'html' ]);
		$this->assertEquals( new Directory([ 'var', 'www']), $dir1->getParent() );
		// Make sure original array hasn't been mutated.
		$this->assertEquals( new Directory([ 'var', 'www', 'html' ]), $dir1 );
	}

	public function testLocal()
	{
		$dir1 = new Directory([ 'var', 'www', 'html' ]);
		$this->assertEquals( 'html', $dir1->getLocal() );
	}

	public function testGetAndPrint()
	{
		$dir1 = new Directory([ 'var', 'www', 'html' ]);
		$this->assertEquals( '/var/www/html/', $dir1->getString() );
		$this->assertEquals( '/var/www/html/', $dir1->getString([ 'divider' => '/', 'starting-slash' => true, 'ending-slash' => true ]) );
		$dir2 = new Directory([ 'https://www.jaimeson-waugh.com' ]);
		$this->assertEquals( 'https://www.jaimeson-waugh.com/', $dir2->getString([ 'divider' => '/', 'starting-slash' => false, 'ending-slash' => true ]) );
		$dir3 = new Directory([ 'https://www.jaimeson-waugh.com', 'index.html' ]);
		$this->assertEquals( 'https://www.jaimeson-waugh.com/index.html', $dir3->getString([ 'divider' => '/', 'starting-slash' => false, 'ending-slash' => false ]) );
		$dir4 = new Directory([ 'C:', 'Program Files', 'Directory Test' ]);
		$this->assertEquals( 'C:\Program Files\Directory Test', $dir4->getString([ 'divider' => '\\', 'starting-slash' => false, 'ending-slash' => false ]) );
		ob_start();
		$dir1->print();
		$this->assertEquals( '/var/www/html/', ob_get_clean() );
		ob_start();
		$dir2->print([ 'starting-slash' => false ]);
		$this->assertEquals( 'https://www.jaimeson-waugh.com/', ob_get_clean() );
		ob_start();
		$dir3->print([ 'starting-slash' => false, 'ending-slash' => false ]);
		$this->assertEquals( 'https://www.jaimeson-waugh.com/index.html', ob_get_clean() );
		ob_start();
		$dir4->print([ 'divider' => '\\', 'starting-slash' => false, 'ending-slash' => false ]);
		$this->assertEquals( 'C:\Program Files\Directory Test', ob_get_clean() );
	}

	public function testCloning()
	{
		$dir1 = new Directory([ 'var', 'www', 'html' ]);
		$dir2 = clone $dir1;
		$this->assertEquals( $dir1, $dir2 );
		$dir2 = $dir2->addDirectory( 'public' );
		// Make sure original hasn't been changed.
		$this->assertEquals( $dir1, new Directory([ 'var', 'www', 'html' ]) );
	}

	public function testExcessSlashes()
	{
		$dir = new Directory([ '/en.wikipedia.org/', '//w/index.php?title=PHP&action=edit&section=6' ]);
		$this->assertEquals( 'en.wikipedia.org/w/index.php?title=PHP&action=edit&section=6', $dir->getStringURL() );
	}
}
