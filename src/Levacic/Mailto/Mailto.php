<?php namespace Levacic\Mailto;

use InvalidArgumentException;

class Mailto {

	/**
	 * The recipients of the email.
	 *
	 * @var array
	 */
	protected $recipients = array(
		'to' => array(),
		'cc' => array(),
		'bcc' => array(),
	);

	/**
	 * The email subject.
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * The email body.
	 *
	 * @var string
	 */
	protected $body = '';

	/**
	 * Creates a new class instance.
	 *
	 * Provided as a utility method, to allow easy chaining when creating a
	 * mailto URI.
	 *
	 * @return \Levacic\Mailto\Mailto
	 */
	public static function create()
	{
		return new static;
	}

	/**
	 * Adds recipients to the "to" group.
	 *
	 * @param array|string $recipients
	 * @return \Levacic\Mailto\Mailto
	 */
	public function to($recipients)
	{
		$this->appendRecipients('to', $recipients);

		return $this;
	}

	/**
	 * Adds recipients to the "cc" group.
	 *
	 * @param array|string $recipients
	 * @return \Levacic\Mailto\Mailto
	 */
	public function cc($recipients)
	{
		$this->appendRecipients('cc', $recipients);

		return $this;
	}

	/**
	 * Adds recipients to the "bcc" group.
	 *
	 * @param array|string $recipients
	 * @return \Levacic\Mailto\Mailto
	 */
	public function bcc($recipients)
	{
		$this->appendRecipients('bcc', $recipients);

		return $this;
	}

	/**
	 * Sets the email subject.
	 *
	 * @param string $subject
	 * @return \Levacic\Mailto\Mailto
	 */
	public function subject($subject)
	{
		$this->subject = $subject;

		return $this;
	}

	/**
	 * Sets the email body.
	 *
	 * @param string $body
	 * @return \Levacic\Mailto\Mailto
	 */
	public function body($body)
	{
		$this->body = $body;

		return $this;
	}

	/**
	 * Returns the compiled URI.
	 *
	 * The returned string is safe to use in `href` attributes and doesn't need
	 * any additional escaping/encoding.
	 *
	 * @return string
	 */
	public function compileUri()
	{
		return 'mailto:'.$this->compilePrimaryRecipients().'?'.$this->compileQueryString();
	}

	/**
	 * Compiles the primary recipients' addresses into a single string.
	 *
	 * @return string
	 */
	protected function compilePrimaryRecipients()
	{
		return rawurlencode(implode(',', $this->recipients['to']));
	}

	/**
	 * Compiles the query string part of the mailto URI.
	 *
	 * @return string
	 */
	protected function compileQueryString()
	{
		$parameters = array();

		if ($this->recipients['cc'])
		{
			$parameters['cc'] = implode(',', $this->recipients['cc']);
		}

		if ($this->recipients['bcc'])
		{
			$parameters['bcc'] = implode(',', $this->recipients['bcc']);
		}

		if ($this->subject)
		{
			$parameters['subject'] = $this->subject;
		}

		if ($this->body)
		{
			$parameters['body'] = $this->body;
		}

		return $this->httpBuildQuery($parameters);
	}

	/**
	 * Builds the required query according to the specification in RFC 6068.
	 *
	 * Uses a built-in PHP function, if available, or manually compiles the
	 * string otherwise.
	 *
	 * PHP versions 5.4.0 and greater implement an additional parameter to the
	 * built-in `http_build_query` function - `$enc_type`, which may be used to
	 * specify the desired encoding. The two possible values are
	 * `PHP_QUERY_RFC1738`, which encodes spaces as plus signs (`+`), and
	 * `PHP_QUERY_RFC3986`, which percent encodes spaces (`%20`). The 'mailto'
	 * URI scheme document maintains that spaces *must* be percent encoded, so
	 * we must use the latter. Unfortunately, this isn't available in prior
	 * versions of PHP, in which case we must fallback to manually building the
	 * query string, which just means we have to use the built-in `rawurlencode`
	 * function, which does conform to RFC 3986.
	 *
	 * @link http://php.net/http_build_query
	 * @link http://tools.ietf.org/search/rfc6068
	 * @param array $parameters
	 * @return string
	 */
	protected function httpBuildQuery($parameters)
	{
		if (version_compare(PHP_VERSION, '5.4.0') >= 0)
		{
			return http_build_query($parameters, '', '&amp;', PHP_QUERY_RFC3986);
		}

		$output = array();

		foreach ($parameters as $key => $value)
		{
			$output[] = $key.'='.rawurlencode($value);
		}

		return implode('&amp;', $output);
	}

	/**
	 * Appends additional recipients to the specified group.
	 *
	 * Doesn't check for duplicates, but validates every provided recipient.
	 * The `$group` argument must be one of: "to", "cc", "bcc".
	 *
	 * @param string $group
	 * @param array|string $recipients
	 * @return void
	 */
	protected function appendRecipients($group, $recipients)
	{
		if (is_string($recipients))
		{
			$recipients = explode(',', $recipients);
			$recipients = array_map('trim', $recipients);
		}

		foreach ($recipients as $recipient)
		{
			if (!filter_var($recipient, FILTER_VALIDATE_EMAIL))
			{
				throw new InvalidArgumentException('The provided email address is not valid: '.$recipient);
			}
		}

		$this->recipients[$group] = array_merge($this->recipients[$group], $recipients);
	}

	/**
	 * Returns the compiled URI.
	 *
	 * The returned string is safe to use in `href` attributes and doesn't need
	 * any additional escaping/encoding.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->compileUri();
	}

}
