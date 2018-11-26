Directory
=========================

Simple class for encapsulating file directory object.

Pass its constructor an array representing a chain o' folders or a string & it will form a consistent directory object that can be used by other code without having to manually mess round with directory inconsistencies.

It also has methods for adding directories, getting the parent directory, or getting the local directory.

Is immutable. Methods that "change" instance, like "addDirectory", return new directory.
