
# Sub-Collector

Sub-Collector is a small project that I started, because I was sick of manually searching for subtitles for my ever growing collection of movies.

## Installation
Run this in your terminal to get the latest Composer version:

```sh
curl -sS https://getcomposer.org/installer | php
```

Or if you don't have curl:

```sh
php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"
```

Create a composer.json file and add Sub-Collector as a dependecy:

```json
{
    "require": {
        "mihaeu/sub-collector": "*"
    }
}
```

And install Sub-Collector using:

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
