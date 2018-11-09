<?php

use PHPUnit\Framework\TestCase;
use WaughJ\Directory\Directory;

class DirectoryTest extends TestCase
{
	public function testConsistency()
	{
		$dir1 = new Directory( '/var/www/html/' );
		$dir2 = new Directory( '/var/www/html' );
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

	public function testAdding()
	{
		$dir1 = new Directory([ 'var', 'www', 'html' ]);
		$dir2 = '/jaimeson-waugh.com/public';
		$full_dir = $dir1->addDirectory( $dir2 );
		$this->assertEquals( $full_dir, "/var/www/html/jaimeson-waugh.com/public/" );
	}
}
