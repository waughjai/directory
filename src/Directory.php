<?php

declare( strict_types = 1 );
namespace WaughJ\Directory
{
	use WaughJ\VerifiedArgumentsSameType\VerifiedArgumentsSameType;

	class Directory
	{
		public function __construct( $directories, $protocol = null )
		{
			if ( is_array( $directories ) )
			{
				$this->directories = self::breakDownDirectoryString( implode( '/', $directories ) );
			}
			else if ( is_string( $directories ) )
			{
				$this->directories = [];
				$directories = self::breakDownDirectoryString( $directories );
				foreach ( $directories as $directory )
				{
					if ( $directory !== '' )
					{
						$this->directories[] = $directory;
					}
				}
			}
			else if ( is_a( $directories, Directory::class ) )
			{
				$this->directories = $directories->getDirectoryChain();
			}
			else
			{
				throw new \Exception( "Invalid type: " . gettype( $directories ) );
			}

			$this->protocol = null;
			if ( $protocol )
			{
				$this->protocol = $protocol;
			}
			else if ( count( $this->directories ) > 0 ) // If we have any directory items.
			{
				$matches = [];
				preg_match( '/^([a-z]+):/', $this->directories[ 0 ], $matches ); // Look for protocol-like string in 1st directory item.
				if ( count( $matches ) > 1 ) // If matches found
				{
					$this->protocol = $matches[ 1 ]; // Only want 1st match.
					array_shift( $this->directories ); // Take protocol out o' directory chain.
				}
			}
		}

		public function __toString()
		{
			return $this->getString();
		}

		public function getString( array $arguments = [] ) : string
		{
			$settings = new VerifiedArgumentsSameType( $arguments, self::DEFAULT_ARGUMENTS );
			$empty_string = count( $this->directories ) <= 0 && $this->protocol === null;
			// Format with slashes depending on properties.
			return
				( ( $settings->get( 'starting-slash' ) && !$empty_string ) ? $settings->get( 'divider' ) : '' ) .
				$this->getFormattedProtocol() .
				implode( $settings->get( 'divider' ), $this->directories ) .
				( ( $settings->get( 'ending-slash' ) && !$empty_string  ) ? $settings->get( 'divider' ) : '' );
		}

		public function getStringWindows() : string
		{
			return implode( '\\', $this->directories );
		}

		public function getStringURL() : string
		{
			return $this->getString( [ 'starting-slash' => false, 'ending-slash' => false, 'divider' => '/' ] );
		}

		public function print( array $arguments = [] ) : void
		{
			echo $this->getString( $arguments );
		}

		public function getDirectoryChain() : array
		{
			return $this->directories;
		}

		// Note that this doesn't mutate directory, but creates a new 1 based on this.
		// After construction, directory should be immutable.
		public function addDirectory( $directory ) : Directory
		{
			$directory = new Directory( $directory );
			return new Directory( array_merge( $this->directories, $directory->getDirectoryChain() ), $this->protocol );
		}

		public function getParent() : Directory
		{
			$number_of_subdirectories = count( $this->directories );
			if ( $number_of_subdirectories > 1 )
			{
				// Make a copy o' directories array.
				$new_array = $this->directories;
				// Remove last directory o' list ( since this function mutates array, we need to use a copy )
				array_pop( $new_array );
				return new Directory( $new_array, $this->protocol );
			}
			return new Directory( '/', $this->protocol );
		}

		public function getLocal() : string
		{
			$number_of_subdirectories = count( $this->directories );
			if ( $number_of_subdirectories >= 1 )
			{
				return $this->directories[ $number_of_subdirectories - 1 ];
			}
			return "/";
		}

		private function getFormattedProtocol() : string
		{
			return ( $this->protocol !== null ) ? $this->protocol . '://' : '';
		}

		private static function breakDownDirectoryString( string $directory_string ) : array
		{
			$directories = [];
			// Split string by both types o' dividers.
			// ( Note: much shorter preg_split doesn't work with backslash )
			$directories_multi = explode( "/", $directory_string );
			foreach ( $directories_multi as &$directory )
			{
				$directory = explode( "\\", $directory );
			}

			// Make multidimensional array into flat array.
			foreach ( $directories_multi as $directory_list )
			{
				foreach ( $directory_list as $single_directory )
				{
					if ( !empty( $single_directory ) )
					{
						array_push( $directories, $single_directory );
					}
				}
			}
			return $directories;
		}

		private $directories;
		private $protocol;

		const DEFAULT_ARGUMENTS =
		[
			'divider' => '/',
			'starting-slash' => true,
			'ending-slash' => true
		];
	}
}
