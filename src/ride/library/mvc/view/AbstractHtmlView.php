<?php

namespace ride\library\mvc\view;

/**
 * Cacheable view for a widget
 */
abstract class AbstractHtmlView implements HtmlView {

    /**
     * Javascripts added to the view
     * @var array
     */
    protected $javascripts = array();

    /**
     * Inline javascripts added to the view
     * @var array
     */
    protected $inlineJavascripts = array();

    /**
     * Styles added to the view
     * @var array
     */
    protected $styles = array();

    /**
     * Merges the javascripts and styles of the provided view into this view
     * @param HtmlView $view
     * @return null
     */
    public function mergeResources(HtmlView $view) {
        $javascripts = $view->getJavascripts();
        foreach ($javascripts as $javascript) {
            $this->addJavascript($javascript);
        }

        $inlineJavascripts = $view->getInlineJavascripts();
        foreach ($inlineJavascripts as $inlineJavascript) {
            $this->addInlineJavascript($inlineJavascript);
        }

        $styles = $view->getStyles();
        foreach ($styles as $style) {
            $this->addStyle($style);
        }
    }

    /**
     * Adds a javascript file to this view
     * @param string $file Reference to a javascript file. This can be a
     * absolute URL or relative URL to the base URL
     * @param boolean $prepend Set to true to prepend the script
     * @return null
     */
    public function addJavascript($file, $prepend = false) {
        if ($prepend) {
            $this->javascripts = array($file => true) + $this->javascripts;
        } else {
            $this->javascripts[$file] = true;
        }
    }

    /**
     * Gets all the javascript files which are added to this view
     * @return array
    */
    public function getJavascripts() {
        return array_keys($this->javascripts);
    }

    /**
     * Removes a javascript file from this view
     * @param string $file Reference to the javascript file
     * @return boolean True when the javascript has been removed, false
     * otherwise
     * @see addJavascript
     */
    public function removeJavascript($file) {
        if (!isset($this->javascripts[$file])) {
            return false;
        }

        unset($this->javascripts[$file]);

        return true;
    }

    /**
     * Adds a inline javascript to this view
     * @param string $script Javascript code to add
     * @param boolean $prepend Set to true to prepend the script
     * @return null
     */
    public function addInlineJavascript($script, $prepend = false) {
        if ($prepend) {
            array_unshift($this->inlineJavascripts, $script);
        } else {
            array_push($this->inlineJavascripts, $script);
        }
    }

    /**
     * Gets all the inline javascripts
     * @return array
    */
    public function getInlineJavascripts() {
        return $this->inlineJavascripts;
    }

    /**
     * Removes a inline javascript from this view
     * @param string $script Javascript code to remove
     * @return boolean True if the script is found and removed, false otherwise
     * @see addInlineJavascript
    */
    public function removeInlineJavascript($script) {
        foreach ($this->inlineJavascripts as $index => $inlineJavascript) {
            if ($inlineJavascript == $script) {
                unset($this->inlineJavascripts[$index]);

                return true;
            }
        }

        return false;
    }

    /**
     * Adds a stylesheet file to this view
     * @param string $file Reference to a CSS file. This can be a absolute URL
     * or a relative URL to the base URL
     * @param boolean $prepend Set to true to prepend the style
     * @return null
     */
    public function addStyle($file, $prepend = false) {
        if ($prepend) {
            $this->styles = array($file => true) + $this->styles;
        } else {
            $this->styles[$file] = true;
        }
    }

    /**
     * Gets all the stylesheets which are added to this view
     * @return array
     */
    public function getStyles() {
        return array_keys($this->styles);
    }

    /**
     * Removes a stylesheet file from this view
     * @param string $file Reference to the css file
     * @return boolean True if the style is found and removed, false otherwise
     * @see addStyle
     */
    public function removeStyle($file) {
        if (!isset($this->styles[$file])) {
            return false;
        }

        unset($this->styles[$file]);

        return true;
    }

}
