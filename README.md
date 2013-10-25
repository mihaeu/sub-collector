
# Sub-Collector

[![Build Status](https://travis-ci.org/mihaeu/sub-collector.png)](https://travis-ci.org/mihaeu/sub-collector)

Sub-Collector is a small project that I started, because I was sick of manually searching for subtitles for my ever growing collection of movies.

Current stable release is v0.1 and the feature set is very, very minimal, but is under active development. There are other tools out there which are way more mature and which have a wide selection of features. This is PHP however and it's console based, so let's see where it goes.

## Installation

### Quick install

Assuming you have the usual dev tools (git, global composer, ...) installed:

```sh
git clone https://github.com/mihaeu/sub-collector.git
cd sub-collector
composer install
```

### Detailed install

Run this in your terminal to get the latest Composer version:

```sh
curl -sS https://getcomposer.org/installer | php
```

Or if you don't have curl:

```sh
php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
```
Clone the repository:

```sh
git clone https://github.com/mihaeu/sub-collector.git
```

Or if you feel like playing around with the app, create a composer.json file and add Sub-Collector as a dependency to use it in your app:

```json
{
    "require": {
        "mihaeu/sub-collector": "*"
    }
}
```

Finally install Sub-Collector using:

```sh
php composer.phar install
```

## Usage
For now Sub-Collector's functionality is very limited. To download subtitles for all the movies (or a single movie) within a path just use:

```sh
cd [SUB_COLLECTOR_PATH]
php bin/sub-collector download [PATH_TO_YOUR_MOVIE_COLLECTION]
```

## Open Tasks
- [x] Tests, tests, tests
- [x] Create a separate movie class
- [ ] Add further subtitle sources
- [ ] Create a composer installer
- [ ] Add a DIC

## Tests
I'm aiming for as close to 100% test coverage as is sensible. If you want to check out the tests for yourself install the require-dev deps from composer (default) and run:

```sh
cd [PATH_TO_YOUR_SUB_COLLECTOR_INSTALLATION]
php vendor/bin/phpunit --testdoc --coverage-text
```

The `--testdoc` produces a more "agile" (buzzzz!) output which is a good way to get started if you're trying to check out the functionality. The whole command will produce something like this:

```sh
# ...

DownloadCommand
 [x] Downloading of subtitles for a movie without subtitle and existing subtitle on server
 [x] Download of subtitles for movie without subtitle but no subtitle on server
 [x] Downloading of subtitles for a movie which has subtitles is skipped

Finder
 [x] Only accepts directories
 [x] Only movies are found in a folder
 [x] Movies can be nested deeply inside a folder
 [x] Movie with subtitle will be detected
 [x] Movie without subtitle will be detected
 [x] Custom file extensions will be detected

SubCollector
 [x] Subtitle can be downloaded if exists
 [x] Subtitle cannot be downloaded if it does not exist
 [x] Downloaded subtitle will be saved as a srt file
 [x] Subtitle file will only be saved when subtitle was found

# ...

Code Coverage Report

 Summary:
  Classes: 83.33% (5/6)
  Methods: 88.24% (15/17)
  Lines:   84.00% (84/100)

```
