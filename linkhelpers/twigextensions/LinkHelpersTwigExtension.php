<?php 
namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;
use Twig_Markup;
use Twig_Function_Method;

class LinkHelpersTwigExtension extends \Twig_Extension
{
	/**
	 * @var No friggin clue what this is
	 */
	static $charset;

	/**
	 * Get name of the Twig extension
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'LinkHelpers';
	}

	/**
	 * Get a list of the Twig filters this extension is providing
	 *
	 * @return array
	 */
	public function getFilters()
	{
		return array(
			'auto_link_urls' => new Twig_Filter_Method($this, 'justUrls'),
			'auto_link_emails' => new Twig_Filter_Method($this, 'justEmails'),
			// 'auto_link' => new Twig_Filter_Method($this, 'urlsAndEmails'),
		);
	}

	/**
	 * Get a list of the Twig functions this extension is providing
	 * 
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			'linkEmail' => new Twig_Function_Method($this, 'linkEmail'),
			'linkUrl' => new Twig_Function_Method($this, 'linkUrl'),
			'linkText' => new Twig_Function_Method($this, 'linkText'),
		);
	}

	/**
	 * Create an HTML link from an email address
	 * 
	 * @param  string      $email_address
	 * @return Twig_Markup
	 */
	public function linkEmail($email_address)
	{
		// @todo: Verify?

		$return = "<a href=\"mailto:$email_address\">$email_address</a>";

		return $this->returnString($return);
	}

	/**
	 * Create an HTML link from a URL
	 * 
	 * @param  string      $url
	 * @return Twig_Markup
	 */
	public function linkUrl($url)
	{
		// @todo: Verify?

		$return = "<a href=\"$url\">$url</a>";

		return $this->returnString($return);
	}

	/**
	 * Enclose text with anchor tag if URL present
	 * 
	 * @param  string      $url
	 * @return Twig_Markup
	 */
	public function linkText($text, $url = null)
	{
		if (!empty($url)) {
			$return = "<a href=\"$url\">$text</a>";
		} else {
			$return = $text;
		}

		return $this->returnString($return);
	}

	/**
	 * Auto-link the Urls in a string
	 *
	 * @param string $str
	 * @return Twig_Markup
	 */
	public function justUrls($str)
	{
		$str = $this->pregUrls($str);

		return $this->returnString($str);
	}

	/**
	 * Auto-link the email addresses in a string
	 *
	 * @param string $str
	 * @return Twig_Markup
	 */
	public function justEmails($str)
	{
		$str = $this->pregEmails($str);

		return $this->returnString($str);
	}

	/**
	 * Auto-link all Urls and Email addresses in a string 
	 *
	 * @param string $str
	 * @return Twig_Markup
	 */
	public function urlsAndEmails($str)
	{
		throw new Exception('Haven\'t figured this one out yet.');

		$str = $this->pregEmails($this->pregUrls($str));

		return $this->returnString($str);
	}

	/**
	 * Regex to find URLs and replace them with full HTML links
	 *
	 * @source http://stackoverflow.com/a/10769415/1052406
	 * @param string $string
	 * @return string 
	 *
	 */ 
	protected function pregUrls($string)
	{
		$regexp				= '#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#i';
		$anchorMarkup = "<a href=\"%s\" target=\"_blank\" >%s</a>";

		preg_match_all($regexp, $string, $matches, \PREG_SET_ORDER);

		foreach ($matches as $match)
		{
			$m = $match[0];
			$url = (preg_match("/^\w+:/", $m)) ? $m : "http://$m";
			$replace = sprintf($anchorMarkup, $url, $m);
			$string = str_replace($match[0], $replace, $string);
		}

		return $string;
	}

	/**
	 * Regex to find email addresses and replace them with full HTML links
	 *
	 * @source http://stackoverflow.com/a/626760/1052406
	 * @param string $string
	 * @return string 
	 *
	 */ 
	protected function pregEmails($string)
	{
		$regex = '/(\S+@\S+\.\S+)/';
		$replace = '<a href="mailto:$1">$1</a>';

		return preg_replace($regex, $replace, $string);
	}

	/**
	 * Return string, safed
	 * 
	 * @param  string $string
	 * @return Twig_Markup
	 */
	protected function returnString($string)
	{
		return new Twig_Markup($string, craft()->templates->getTwig()->getCharset());
	}
}
