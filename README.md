
# Sub-Collector

[![Build Status](https://travis-ci.org/mihaeu/sub-collector.png)](https://travis-ci.org/mihaeu/sub-collector)
[![Coverage Status](https://coveralls.io/repos/mihaeu/sub-collector/badge.png)](https://coveralls.io/r/mihaeu/sub-collector)

![sub-collector usage example](http://kimai.mike-dev.info/sub-collector-example.gif)

Sub-Collector is a small project that I started, because I was sick of manually searching for subtitles for my ever growing collection of movies.

There are other tools out there which are way more mature and which have a wide selection of features. These tools however were not written in PHP and this is console based, so let's see where it goes.

## Installation

> I'm assuming you're familiar with Composer. If you are not and you made it here, I strongly advise you to take 5 minutes to read up on the [Getting Started](https://getcomposer.org/doc/00-intro.md) section. It'll change your life (or at least the way you write PHP).

Make sure `~/.composer/bin` is in your `$PATH` and then simply execute:

```bash
composer global require mihaeu/sub-collector:dev-master
```

## Usage
For now Sub-Collector's functionality is very limited. To download subtitles for all the movies (or a single movie) within a path just use:

```bash
sub-collector download [PATH_TO_YOUR_MOVIE_COLLECTION]
```

## Tests
I'm aiming for as close to 100% test coverage as is sensible. If you want to check out the tests for yourself install the require-dev deps from composer (default) and run:

```bash
vendor/bin/phpunit --testdoc --coverage-text
```

The `--testdox` produces a more "agile" (buzzzz!) output which is a good way to get started if you're trying to check out the functionality. The whole command will produce something like this:

```bash
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

## Thanks to

 - [themoviedb.org](http://www.themoviedb.org/) for providing a free-to-use API
 - [Symfony](http://symfony.com/)/[SensioLabs](http://sensiolabs.com/en) and especially [Fabien Potencier](http://fabien.potencier.org/) for what he does for PHP (for this particular project the [DomCrawler](https://github.com/symfony/DomCrawler))
 - the [Composer](https://getcomposer.org/) team for revolutionizing the way I and many others write PHP
 - [GitHub](https://github.com) for redefining collaboration
 - [Travis CI](https://travis-ci.org/) for improving the quality and compatibility of thousands of open source projects
 - [Sebastian Bergmann](http://sebastian-bergmann.de/) for [PHPUnit](http://phpunit.de) and many other awesome QA tools

## License

MIT, see `LICENSE` file.
