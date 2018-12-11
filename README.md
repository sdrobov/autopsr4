## About
[![Build Status](https://travis-ci.org/sdrobov/autopsr4.svg?branch=master)](https://travis-ci.org/sdrobov/autopsr4)

AutoPsr4 is a small tool intended to help to convert legacy non PSR4 projects to PSR4.
Project must have a compatible structure: all files should follow one-class-per-file rule,
if file is under subdirectory `Subdir` and called `Filename.php` it must have classname
`Subdir_Filename`. Pretty strong rules and I don't know if this utility be useful for someone
except me, but if it is I will be glad.
