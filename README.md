# Mailto URI generator

This class allows easy creation of `mailto:` links, and supports recipients within `to:`, `cc:`, and `bcc:` fields, and setting the subject and body of an email.


## Example

```php
<?php

$mailto = Levacic\Mailto\Generator::create()
	->to('person@example.com')
	->subject('Subject')
	->body('Hi there!');

?>

<a href="<?php echo $mailto; ?>">Send email!</a>
```


## License

The code is licensed under the MIT license, which is available in the `LICENSE` file.
