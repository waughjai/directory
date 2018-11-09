<?php

declare( strict_types = 1 );
namespace WaughJ\Directory
{
	class Directory
	{
		public function __construct( $directories )
		{
			if ( is_array( $directories ) )
			{
				$this->directories = $directories;
			}
			else if ( is_string( $directories ) )
			{
				$this->directories = [];
				$directories = explode( '/', $directories );
				foreach ( $directories as $directory )
				{
					if ( $directory !== '' )
					{
						$this->directories[] = $directory;
					}
				}
			}
		}

		public function __toString()
		{
			return '/' . implode( '/', $this->directories ) . '/';
		}

		public function getDirectoryChain() : array
		{
			return $this->directories;
		}

		public function addDirectory( $directory ) : Directory
		{
			$directory = new Directory( $directory );
			return new Directory( array_merge( $this->directories, $directory->getDirectoryChain() ) );
		}

		private $directories;
	}
}
