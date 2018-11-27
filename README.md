Directory
=========================

Simple class for encapsulating file directory object.

Pass its constructor an array representing a chain o' folders or a string & it will form a consistent directory object that can be used by other code without having to manually mess round with directory inconsistencies.

It also has methods for adding directories, getting the parent directory, or getting the local directory.

Is immutable. Methods that "change" instance, like "addDirectory", return new directory.

When getting or printing a string version o' a directory, you can pass a hash map o' options to the functions:
* "divider": Determines the divider 'tween subdirectories. Defaults to "/".
* "starting-slash": Boolean that determines whether there should be a leading divider or not. Defaults to true.
* "ending-slash": Boolean that determines whether there should be an ending divider or not. Defaults to true.

## Example

	use WaughJ\Directory\Directory;

	$directory = new Directory([ 'C:', 'Program Files', 'Directory Test' ]);
	$directory->print([ 'divider' => '\\', 'starting-slash' => false, 'ending-slash' => false ]);

This will print "C:\Program Files\Directory Test".
