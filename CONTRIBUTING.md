# Contributing

We love pull requests from everyone! Here are some basic tips and tricks for constructive contribution.

## Prerequisites

* Git
* Ability to switch versions of PHP CLI for unit testing purposes (e.g. `brew uninstall php70 & brew install php53`)

## Fork, Clone and Install

Fork, then clone the repo:

    git clone git@github.com:your-username/FMPDO.git

If you don't have composer installed in your path already (e.g. via `brew install composer`), you can install it inside
the cloned project with this command:

    php -r "readfile('https://getcomposer.org/installer');" | php

We suggest you always use [Composer](https://getcomposer.org/) `update` in the project (as opposed to `install`):

    composer update
    
Since this is a library, we `.gitignore` both `composer.phar` and `composer.lock`, in case you've downloaded the .phar: 

    php ./composer.phar update

## Unit Tests and Coding Style Tests

The project is setup to run all the PHPUnit and PHPCS tests via [Apache ant](http://brewformulas.org/Ant). (See `build.xml` in the project root.)

Before you start make sure the tests run using ant:

    ant

Alternatively, if you don't want to install Java to run ant, the tests run like this:

    php ./vendor/bin/phpunit -c phpunit.xml
    php ./vendor/bin/phpcs -p --standard=PSR2 library/ tests/

## Branch, Change and Test

Before you make your changes, please create a new branch. Example:

    git checkout -b feature/my-thing

Make your change. Add tests for your change. Make sure the tests pass (or run both manually as above):

    ant

## Create Pull Request

Push your new branch to your fork and [submit a pull request][pr].

[pr]: https://github.com/rjakes/FMPDO/compare/

At this point you're waiting on us. We try to at least comment on pull requests within three business days (and,
typically, one business day). If you don't get any response within three days, feel free to bump it with a comment. We
may suggest some changes or improvements or alternatives.

## Tips

Some things that will increase the chance that your pull request is accepted:

* Don't break existing tests.
* Write test coverage for your change(s).
* Follow the PSR-2 [coding standards][style].
* Write [good commit messages][commit].
* Explain and/or justify the reason for the change in your PR description.

[style]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[commit]: https://git-scm.com/book/ch5-2.html#Commit-Guidelines
