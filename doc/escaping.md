# Escaping Guide

Outputting variables in php can always be a risk if their values are provided by the user. If you don't know why, the 
[OWASP XSS attacks](https://owasp.org/www-community/attacks/xss/) page gives a good overview of the problem.

NoTmpl cannot automatically escape template variables because it has no context. It does however provide an `esc` 
function to escape values for HTML output and `text` + `text_end` tags for escaping a larger output.

## Escaping a value
```php
namespace StefGodin\NoTmpl;

/**
 * @var string $userInput - We can't expect the value to be sanitized at this point
 */

?>
<div data-attr="<?php esc($userInput) ?>">
    <span>The value is '<?php esc($userInput) ?>'</span>
</div>
```

> Warning!\
> Escaping only works for outputting in HTML. For escaping in other contexts such as javascript or CSS, use a well 
> tested library such as [laminas-escaper](https://packagist.org/packages/laminas/laminas-escaper).
