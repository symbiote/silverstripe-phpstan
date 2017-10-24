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
    - vendor/silbinarywolf/silverstripe-phpstan/extension.neon
```
