HydraWiki coding conventions
============================

Abstract
--------
This project implements a set of rules for use with [PHP CodeSniffer][0].

It applies additional coding standards on top of the MediaWiki Standard.

See [MediaWiki conventions][1] on our wiki for a detailed description of the
coding conventions that are validated by these rules. :-)

How to install
--------------
1. Create a composer.json which adds this project as a dependency:

    ```
    {
    	"require-dev": {
    		"hydrawiki/hydrawiki-codesniffer": "1.0.0"
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
    	<arg name="bootstrap" value="./vendor/mediawiki/mediawiki-codesniffer/utils/bootstrap-ci.php"/>
    	<arg name="extensions" value="php,php5,inc"/>
    	<arg name="encoding" value="UTF-8"/>
    </ruleset>
    ```
3. Install: `composer update`
4. Run: `composer test`
5. Run: `composer fix` to auto-fix some of the errors, others might need
   manual intervention.
6. Commit!

Note that for most MediaWiki projects, we'd also recommend adding a PHP linter
to your `composer.json` – see the [full documentation][2] for more details.

---
[0]: https://packagist.org/packages/squizlabs/php_codesniffer
[1]: https://www.mediawiki.org/wiki/Manual:Coding_conventions/PHP
[2]: https://www.mediawiki.org/wiki/Continuous_integration/Entry_points#PHP
