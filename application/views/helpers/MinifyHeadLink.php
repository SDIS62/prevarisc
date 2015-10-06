<?php

class View_Helper_MinifyHeadLink extends Zend_View_Helper_HeadLink
{
    /**
	 *
	 * The folder to be appended to the base url to find minify on your server.
	 * The default assumes you installed minify in your documentroot\min directory
	 * if you modified the directory name at all, you need to let the helper know
	 * here.
	 * @var string
	 */
    protected $_minifyLocation = '/min/';

    /**
	 * Registry key for placeholder
	 * @var string
	 */
    protected $_regKey = 'RC_View_Helper_MinifyHeadLink';

    /**
	 *
	 * Known Valid CSS Extension Types
	 * @var array
	 */
    protected $_cssExtensions = array(".css", ".css1", ".css2", ".css3");
    
    
    /**
     *
     * @var type The application version
     * @var string
     */
    protected $_version = null;
    
    /**
     * Overrides default constructor to inject version
     */
    public function __construct($version = null)
    {
        parent::__construct();
        $this->_version = $version;
    }
    
    
    /**
	 * Returns current object instance. Optionally, allows passing array of
	 * values to build link.
	 *
	 *
	 * @param array $attributes
	 * @param string $placement
	 * @return Zend_View_Helper_HeadLink
	 */
    public function minifyHeadLink(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        return parent::headLink($attributes, $placement);
    }

    /**
	 *
	 * Gets a string representation of the headLinks suitable for inserting
	 * in the html head section.
	 *
	 * It is important to note that the minified files will be minified
	 * in reverse order of being added to this object, and ALL files will be rendered
	 * prior to inline being rendered.
	 *
	 * @see Zend_View_Helper_HeadScript->toString()
	 * @param  string|int $indent
	 * @return string
	 */
    public function toString($indent = null)
    {
        $indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();
        $trimmedBaseUrl = trim($this->getBaseUrl(), '/');

        $items = array();
        $stylesheets = array();
        $this->getContainer()->ksort();
        foreach ($this as $item) {
            if ($item->type == 'text/css' && $item->conditionalStylesheet === false && strpos($item->href, 'http://') === false && $this->isValidStyleSheetExtension($item->href)) {
                $stylesheets [$item->media] [] = str_replace($this->getBaseUrl(), '', $item->href);
            } else {
                // first get all the stylsheets up to this point, and get them into
                // the items array
                $seen = array();
                foreach ($stylesheets as $media => $styles) {
                    $minStyles = new stdClass();
                    $minStyles->rel = 'stylesheet';
                    $minStyles->type = 'text/css';
                    $minStyles->href = $this->getMinUrl() . '?f=' . implode(',', $styles);
                    if ($trimmedBaseUrl) $minStyles->href .= '&b=' . $trimmedBaseUrl;
                    if ($this->_version) $minStyles->href .= '&v=' . $this->_version;
                    $minStyles->media = $media;
                    $minStyles->conditionalStylesheet = false;
                    if (in_array($this->itemToString($minStyles), $seen)) {
                        continue;
                    }
                    $items [] = $this->itemToString($minStyles); // add the minified item
                    $seen [] = $this->itemToString($minStyles); // remember we saw it
                }
                $stylesheets = array(); // Empty our stylesheets array
                $items [] = $this->itemToString($item); // Add the item
            }
        }

        // Make sure we pick up the final minified item if it exists.
        $seen = array();
        foreach ($stylesheets as $media => $styles) {
            $minStyles = new stdClass();
            $minStyles->rel = 'stylesheet';
            $minStyles->type = 'text/css';
            $minStyles->href = $this->getMinUrl() . '?f=' . implode(',', $styles);
            if ($trimmedBaseUrl) $minStyles->href .= '&b=' . $trimmedBaseUrl;
            $minStyles->media = $media;
            $minStyles->conditionalStylesheet = false;
            if (in_array($this->itemToString($minStyles), $seen)) {
                continue;
            }
            $items [] = $this->itemToString($minStyles);
            $seen [] = $this->itemToString($minStyles);
        }

        return $indent . implode($this->_escape($this->getSeparator()) . $indent, $items);

    }

    /**
	 *
	 * Loops through the defined valid static css extensions we use.
	 * @param string $string
	 */
    public function isValidStyleSheetExtension($string)
    {
        foreach ($this->_cssExtensions as $ext) {
            if (substr_compare($string, $ext, -strlen($ext), strlen($ext)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
	 * Retrieve the minify url
	 *
	 * @return string
	 */
    public function getMinUrl()
    {
        return $this->getBaseUrl() . $this->_minifyLocation;
    }

    /**
	 * Retrieve the currently set base URL
	 *
	 * @return string
	 */
    public function getBaseUrl()
    {
        return Zend_Controller_Front::getInstance()->getBaseUrl();
    }
}
