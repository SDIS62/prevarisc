<?php

class View_Helper_MinifyInlineScript extends Zend_View_Helper_InlineScript
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
//	protected $_regKey = 'RC_View_Helper_MinifyHeadScript';

    
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
	 * Return headScript object
	 *
	 * Returns headScript helper object; optionally, allows specifying a script
	 * or script file to include.
	 *
	 * @param  string $mode 			Script or file
	 * @param  string $spec 			Script/url
	 * @param  string $placement	Append, prepend, or set
	 * @param  array  $attrs 			Array of script attributes
	 * @param  string $type 			Script type and/or array of script attributes
	 * @return Zend_View_Helper_HeadScript
	 */
    public function minifyHeadScript($mode = Zend_View_Helper_HeadScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = array(), $type = 'text/javascript')
    {
        return parent::headScript($mode, $spec, $placement, $attrs, $type);
    }

    /**
	 *
	 * Gets a string representation of the headscripts suitable for inserting
	 * in the html head section. All included javascript files will be minified
	 * and any script sections will remain as is.
	 *
	 * It is important to note that the minified javascript files will be minified
	 * in reverse order of being added to this object, and ALL files will be rendered
	 * prior to inline scripts being rendered.
	 *
	 * @see Zend_View_Helper_HeadScript->toString()
	 * @param  string|int $indent
	 * @return string
	 */
    public function toString($indent = null)
    {
        // An array of Script Items to be rendered
        $items = array();

        // An array of Javascript Items
        $scripts = array();

        // Any indentation we should use.
        $indent = (null !== $indent) ? $this->getWhitespace($indent) : $this->getIndent();

        // Determining the appropriate way to handle inline scripts
        if ($this->view) {
            $useCdata = $this->view->doctype()->isXhtml() ? true : false;
        } else {
            $useCdata = $this->useCdata ? true : false;
        }

        $escapeStart = ($useCdata) ? '//<![CDATA[' : '//<!--';
        $escapeEnd = ($useCdata) ? '//]]>' : '//-->';

        $this->getContainer()->ksort();
        $groupIndex = 0;
        foreach ($this as $i => $item) {
            if ($this->_isNeedToMinify($item)) {
                if (!empty($item->attributes['minify_split_before']) || !empty($item->attributes['minify_split'])) {
                    $items[] = $this->_generateMinifyItem($scripts);
                    $scripts = array();
                }
                $scripts[] = $item->attributes['src'];
                if (!empty($item->attributes['minify_split_after']) || !empty($item->attributes['minify_split'])) {
                    $items[] = $this->_generateMinifyItem($scripts);
                    $scripts = array();
                }
            } else {
                if ($scripts) {
                    $items[] = $this->_generateMinifyItem($scripts);
                    $scripts = array();
                }
                $items[] = $this->itemToString($item, $indent, $escapeStart, $escapeEnd);
            }
        }
        if ($scripts) {
            $items[] = $this->_generateMinifyItem($scripts);
        }

        return $indent . implode($this->_escape($this->getSeparator()) . $indent, $items);
    }

    protected function _isNeedToMinify($item)
    {
        return isset($item->attributes ['src'])
                && !empty($item->attributes ['src'])
                && preg_match('/^https?:\/\//', $item->attributes['src']) == false
                && !isset($item->attributes['minify_disabled']);
    }

    protected function _generateMinifyItem(array $scripts)
    {
        $baseUrl = $this->getBaseUrl();
        if (substr($baseUrl, 0, 1) == '/') {
            $baseUrl = substr($baseUrl, 1);
        }
        $minScript = new stdClass();
        $minScript->type = 'text/javascript';
        if (is_null($baseUrl) || $baseUrl == '') {
            $minScript->attributes['src'] = $this->getMinUrl() . '?f=' . implode(',', $scripts);
        } else {
            $minScript->attributes['src'] = $this->getMinUrl() . '?b=' . $baseUrl . '&f=' . implode(',', $scripts);
        }
        
        if ($this->_version) $minScript->attributes['src'] .= '&v=' . $this->_version;

        return $this->itemToString($minScript, '', '', '');
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
