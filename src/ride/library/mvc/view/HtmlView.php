<?php

namespace ride\library\mvc\view;

/**
 * Interface for a HTML view
 */
interface HtmlView extends View {

    /**
     * Merges the javascripts and styles of the provided view into this view
     * @param HtmlView $view
     * @return null
     */
    public function mergeResources(HtmlView $view);

    /**
     * Adds a javascript file to this view
     * @param string $file Reference to a javascript file. This can be a
     * absolute URL or relative URL to the base URL
     * @param boolean $prepend Set to true to prepend the script
     * @return null
     */
    public function addJavascript($file, $prepend = false);

    /**
     * Gets all the javascript files which are added to this view
     * @return array
     */
    public function getJavascripts();

    /**
     * Removes a javascript file from this view
     * @param string $file Reference to the javascript file
     * @return null
     * @see addJavascript
     */
    public function removeJavascript($file);

    /**
     * Adds a inline javascript to this view
     * @param string $script Javascript code to add
     * @param boolean $prepend Set to true to prepend the script
     * @return null
     */
    public function addInlineJavascript($script, $prepend = false);

    /**
     * Gets all the inline javascripts
     * @return array
    */
    public function getInlineJavascripts();

    /**
     * Removes a inline javascript from this view
     * @param string $script Javascript code to remove
     * @return boolean True if the script is found and removed, false otherwise
     * @see addInlineJavascript
    */
    public function removeInlineJavascript($script);

    /**
     * Adds a stylesheet file to this view
     * @param string $file Reference to a CSS file. This can be a absolute URL
     * or a relative URL to the base URL
     * @param boolean $prepend Set to true to prepend the style
     * @return null
     */
    public function addStyle($file, $prepend = false);

    /**
     * Gets all the stylesheets which are added to this view
     * @return array
     */
    public function getStyles();

    /**
     * Removes a stylesheet file from this view
     * @param string $file Reference to the css file
     * @return boolean True if the style is found and removed, false otherwise
     * @see addStyle
     */
    public function removeStyle($file);

}
