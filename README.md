# srvcleaner

CLI tool to clean up your server (e.g. remove files and directories that are overdue)

### Installation

Requires PHP 5.5 or later. There are no plans to support PHP 5.4 or PHP 5.3. In case this is an obstacle for you,
conversion should be no problem. The library is very small.

It is installable and autoloadable via Composer as [genkgo/srvcleaner](https://packagist.org/packages/genkgo/srvcleaner).

### Quality

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/genkgo/srvcleaner/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/genkgo/srvcleaner/)
[![Code Coverage](https://scrutinizer-ci.com/g/genkgo/srvcleaner/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/genkgo/srvcleaner/)
[![Build Status](https://travis-ci.org/genkgo/srvcleaner.png?branch=master)](https://travis-ci.org/genkgo/srvcleaner)

To run the unit tests at the command line, issue `phpunit -c tests/`. [PHPUnit](http://phpunit.de/manual/) is required.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

## Getting Started

### Build your phar

```
vendor/bin/box build -c box.json
```

### Create a config file

The config containing your cleanup tasks. Remove directories (including contents) or remove files.

```php
{
  "name": "Test Cleaner",
  "tasks": [{
      "name" : "removeTmp",
      "src": "CleanUpDirectories",
      "config": {
        "path": "/tmp",
        "match": "srvcleaner*"
      }
    },{
      "name" : "removeTmp",
      "src": "CleanUpFiles",
      "config": {
        "path": "/tmp",
        "*.tmp"
      }
    }
  ]
}
```

If you are removing backups that are overdue, use the following settings to remove backups older than 30 days.

```php
{
  "name": "Test Cleaner",
  "tasks": [{
      "name" : "removeTmp",
      "src": "CleanUpFiles",
      "config": {
        "path": "/tmp/*.tar.gz",
        "modifiedAt": "P30D"
      }
    }
  ]
}
```


### Run the command

```
phar/srvcleaner.phar clean -c srvcleaner.json
```

## Contributing

- Found a bug? Please try to solve it yourself first and issue a pull request. If you are not able to fix it, at least
  give a clear description what goes wrong. We will have a look when there is time.
- Want to see a feature added, issue a pull request and see what happens. You could also file a bug of the missing
  feature and we can discuss how to implement it.
