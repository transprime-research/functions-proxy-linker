## Functions Proxy Linker

Link PHP global functions to your class.

### Examples

Makes it possible to have these and have a linkable references to PHP inbuilt functions:

```php
Piper::on('I am ok')
    ->strlen() // 7
    ->in_array([1, 4, 7, 5]) // true
    ->up(); // true
```
> Piper is at https://github.com/transprime-research/piper.

Using this code:

```php
Linker::on(PiperLinker::class) // the class to populate with functions
    ->skipFirstParameter() // So that we can use them as a chained and piped methods
    ->link()
    ->save('/dir/piper/PiperLinker.php');
```

### Install

```shell
composer require transprime-research/functions-proxy-linker
```

### Other Usage

#### Generate all the functions signatures

````php
Linker::on(LinkerStubber::class) // the class to populate with functions
    ->link()
    ->save('/dir/stub/LinkerStubber.php');
````

Gives something like below:

```php
/**
 * ...
 * @method self is_array($value)
 * @method self in_array($needle, array $haystack, bool $strict = false)
 */
class LinkerStubber
{

}
```

Optionally you can skip some functions from being populated by sending arrays as value of `$exceptFunctions` on `link()` method.

```php
Linker::on(LinkerStubber::class) // the class to populate with functions
    ->link(['is_array'])
    ->save('/dir/stub/LinkerStubber.php');
// is_array will not appear int the final generated code.
```

## Additional Information

This package is part of a series of "The Code Dare".

See other packages in this series here:

- https://github.com/transprime-research/piper [Smart Piping in PHP]
- https://github.com/transprime-research/arrayed [A smarter Array now like an object]
- https://github.com/omitobi/conditional [A smart PHP if...elseif...else statement]
- https://github.com/omitobi/carbonate [A smart Carbon + Collection package]
- https://github.com/omitobi/laravel-habitue [Jsonable Http Request(er) package with Collections response]
- https://github.com/transprime-research/attempt [Try and catch in php objected oriented way]

### Licence

MIT (See LICENCE file)
