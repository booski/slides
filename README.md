# Slides

### A simple web-based slideshow tool

This tool allows users to create image-based slideshows in a simple drag and drop-based interface.  
Drop a slide on a show to add it, drop a slide or a show on the trash icon to remove it.

## Requirements

* A webserver running PHP with imagemagick
* An empty mysql database
* You also probably want to protect the `admin` directory with some sort of authentication  
  The application will check users against the `$_SERVER['REMOTE_USER']` variable, so any authentication that populates that field should work. Authentication won't be enforced if the list of authorized users is empty.

## Installation

1. Clone the repo to an appropriate location
1. Navigate to the `include` directory
1. Import `database.sql` into a previously empty database
1. Copy `config.php.example` to `config.php`
1. Change settings in `config.php` to fit your needs
1. Make sure the web server can write to the `uploads` directory
1. Make sure the web server accepts image and video uploads of the size you want
1. Done!

## Upgrading to a new version

1. Check out the appropriate version, e.g. `git fetch; git checkout v2.0`
1. Run the appropriate database upgrade script, e.g. `mysql [dbname] < include/dbupdate-v1-to-v2.sql`
