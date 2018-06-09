# PHP-JSON
PHP-module for parsing JSON
Author: Ihar Areshchankau, 2017.

Mailto: r0bur@tut.by

One pretty simple but useful function for parsing JSON in PHP:

## Usage:
```php
...
require 'iaparser.php';
require 'iajson.php';
...
$array = iaJsonDecode ($json);
...
```

## Example.

*Source JSON:*

```
{
	field1: 1234.5678,
	field2: "String value",
	field3: [1, 2, 3, 4],
	field4: {a: 1, b: 2, c: 3, d: 4}
}
```

*Resulting PHP array:*

```
Array
(
    [field1] => 1234.5678
    [field2] => String value
    [field3] => Array
        (
            [0] => 1
            [1] => 2
            [2] => 3
            [3] => 4
        )
    [field4] => Array
        (
            [a] => 1
            [b] => 2
            [c] => 3
            [d] => 4
        )
)
```
Please look at 'demo.php' and 'demo.json' for reference.
