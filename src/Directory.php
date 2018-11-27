<?php

declare( strict_types = 1 );
namespace WaughJ\Directory
{
	use WaughJ\VerifiedArgumentsSameType\VerifiedArgumentsSameType;

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
		}

		public function __toString()
		{
			return $this->getString();
		}

		public function getString( array $arguments = [] ) : string
		{
			$settings = new VerifiedArgumentsSameType( $arguments, self::DEFAULT_ARGUMENTS );
			return
				( ( $settings->get( 'starting-slash' ) ) ? $settings->get( 'divider' ) : '' ) .
				implode( $settings->get( 'divider' ), $this->directories ) .
				( ( $settings->get( 'ending-slash' ) ) ? $settings->get( 'divider' ) : '' );
		}

		public function getStringWindows() : string
		{
			return implode( '\\', $this->directories );
		}

		public function getStringURL() : string
		{
			return implode( '/', $this->directories );
		}

		public function print() : void
		{
			echo $this;
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
			return new Directory( array_merge( $this->directories, $directory->getDirectoryChain() ) );
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
				return new Directory( $new_array );
			}
			return new Directory( '/' );
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
					array_push( $directories, $single_directory );
				}
			}
			return $directories;
		}

		private $directories;

		const DEFAULT_ARGUMENTS =
		[
			'divider' => '/',
			'starting-slash' => true,
			'ending-slash' => true
		];
	}
}
