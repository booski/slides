# Slides

### A simple web-based slideshow tool

This tool allows users to create image-based slideshows in a simple drag and drop-based interface.  
Drop a slide on a show to add it, drop a slide or a show on the trash icon to remove it.

## Requirements

* A webserver running PHP with imagemagick  
* You also probably want to protect the `admin` directory with some sort of authentication

## Installation

1. Clone the repo to an appropriate location
1. Navigate to the `admin` directory
1. Import `database.sql` into a previously empty database
1. Copy `config.php.example` to `config.php`
1. Change settings in `config.php` to fit your needs
1. Create (or symlink) a directory in the root of the project matching the value of `$uldir` in `config.php`
1. Make sure the web server can write to that directory
1. Make sure the web server accepts image uploads of the size you want
1. Done!
