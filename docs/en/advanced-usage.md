# Advanced Usage

## Example Project Configuration

```
parameters:
    ignoreErrors:
        # Most QueuedJobs don't call the parent::__construct() method
        - '#does not call parent constructor from AbstractQueuedJob#'
        
        # SS_List is basically ArrayList and DataList. Consider filter/sort/etc supported.
        - '#Call to an undefined method SS_List::#'
includes:
    - vendor/symbiote/silverstripe-phpstan/extension.neon
```

# PHPUnit testing with a base SilverStripe project

1) Navigate to a new project folder and execute the following:
```
composer create-project silverstripe/installer . 3.6.5
```
NOTE: The '.' in the above command means it'll output the files in the current directory.

2) Follow the installation steps in [Quick Start](/docs/en/quick-start.md).

3) Execute the following from the project directory to run tests:
```
vendor/bin/phpunit -c vendor/symbiote/silverstripe-phpstan/tests/phpunit.xml vendor/symbiote/silverstripe-phpstan/tests/
```

# Debugging

By adding --debug to the list of parameters, you can make PHPStan crash when it hits an error.
This is incredibly useful for PRs or helping raise issues.

`./vendor/bin/phpstan.phar.bat analyse ptv-mpw/src -c "phpstan.neon" -a "vendor/symbiote/silverstripe-phpstan/bootstrap.php" --level 2 --debug`