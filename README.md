HydraWiki Coding Conventions
============================
![Release Version](https://img.shields.io/github/release/HydraWiki/hydrawiki-codesniffer.svg?style=flat)
[![Build Status](https://travis-ci.com/HydraWiki/hydrawiki-codesniffer.svg?branch=master)](https://travis-ci.com/HydraWiki/hydrawiki-codesniffer)

Abstract
--------
This project implements a set of rules for use with [PHP CodeSniffer][0].

HydraWiki is based on MediaWiki's Coding Standards, it applies additional coding standards on top of the [MediaWiki Standard][3].

See [MediaWiki conventions][1] from MediaWiki for a detailed description of the
coding conventions that are validated by these rules. :-)

How to install
--------------
1. Create a composer.json which adds this project as a dependency:

    ```
    {
    	"require-dev": {
    		"hydrawiki/hydrawiki-codesniffer": "1.0.9"
    	},
    	"scripts": {
    		"test": [
    			"phpcs -p -s"
    		],
    		"fix": "phpcbf"
    	}
    }
    ```
2. Create a .phpcs.xml with our configuration:

    ```
    <?xml version="1.0"?>
    <ruleset>
    	<rule ref="./vendor/hydrawiki/hydrawiki-codesniffer/HydraWiki"/>
    	<file>.</file>
    	<arg name="extensions" value="php,php5,inc"/>
    	<arg name="encoding" value="UTF-8"/>
    </ruleset>
    ```
3. Install: `composer update`
4. Run: `composer test`
5. Run: `composer fix` to auto-fix some of the errors, others might need
   manual intervention.
6. Commit!

Note that for most MediaWiki projects, MediaWiki also recommend to add a PHP linter
to your `composer.json` – see the [full documentation][2] for more details.

## Acknowledgements

This extension is based off of the `mediawiki-codesniffer` extension created by [MediaWiki][3] and some parts of [PHP CodeSniffer][0].

## Contributing and Licensing
This coding standard is used internally on HydraWiki projects in support of the Gamepedia Platform. This project is made public for ease of use by our team and it is not our intention to maintain it for public use as an alternative to the [original project][3] from which it was derived. However, if you do find it useful and want to contribute improvements to it, any merge request will be consider against our internal needs for this project before public considerations. 

The project is available under [MIT license][4] unless otherwise noted in files that are used from other projects. These files retain the original license under which they were created.

---
[0]: https://packagist.org/packages/squizlabs/php_codesniffer
[1]: https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP
[2]: https://www.mediawiki.org/wiki/Continuous_integration/Entry_points#PHP
[3]: https://github.com/wikimedia/mediawiki-tools-codesniffer
[4]: https://github.com/HydraWiki/hydrawiki-codesniffer/blob/master/LICENSE
