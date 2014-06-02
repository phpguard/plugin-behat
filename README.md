# Behat Plugin

Behat plugin for PhpGuard

[![License](https://poser.pugx.org/phpguard/plugin-behat/license.png)](https://packagist.org/packages/phpguard/plugin-behat)
[![Latest Stable Version](https://poser.pugx.org/phpguard/plugin-behat/v/stable.png)](https://packagist.org/packages/phpguard/plugin-behat)
[![HHVM Status](http://hhvm.h4cc.de/badge/phpguard/plugin-behat.png)](http://hhvm.h4cc.de/package/phpguard/plugin-behat)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpguard/plugin-behat/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpguard/plugin-behat/?branch=master)
[![Master Build Status](https://secure.travis-ci.org/phpguard/plugin-behat.png?branch=master)](http://travis-ci.org/phpguard/plugin-behat)
[![Coverage Status](https://coveralls.io/repos/phpguard/plugin-behat/badge.png?branch=master)](https://coveralls.io/r/phpguard/plugin-behat?branch=master)

## Installation
Using composer:
```shell
$ cd /path/to/project
$ composer require --dev "phpguard/plugin-behat @dev"
```

## Options

* `cli` The options passed to the `behat` command. Default is: `null`
* `all_on_start` Run all on `phpguard` startup. Default: `false`
* `all_after_pass` Run all after changed file pass. Default: `false`
* `keep_failed` Remember failed tests and keep running them until pass. Default: `false`
* `always_lint` Always check file syntax with `php -l` before run. If check syntax failed, `behat` command will not running. Default: `true`
* `run_all_cli` The options to passed to the `behat` command when running all specs. Default value will be using `cli` options

## Config Sample

```yaml
# /path/to/project/phpguard.yml
behat:
    options:
        cli:                "--format=pretty"
        all_on_start:       true
        all_after_pass:     true
        keep_failed:        true
        import_suites:      false
        run_all_cli:        "--format=dot"

    watch:
        - { pattern: "#^src\/(.+)\.php$#", transform: "spec/PhpGuard/Plugins/behat/${1}Spec.php" }
        - { pattern: "#^spec\/(.+)\.php$#" }
```
