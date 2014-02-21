<?php

namespace ride\library\mvc\view;

/**
 * Interface for a HTML view
 */
interface HtmlView extends View {

    /**
     * Adds a javascript file to this view
     * @param string $file Reference to a javascript file. This can be a
     * absolute URL or relative URL to the base URL
     * @return null
     */
    public function addJavascript($file);

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
     * @return null
     */
    public function addInlineJavascript($script);

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
     * @return null
     */
    public function addStyle($file);

    /**
     * Gets all the stylesheets which are added to this view
     * @return array
     */
    public function getStyles();

    /**
     * Removes a stylesheet file from this view
     * @param string $file Reference to the css file
     * @return null
     * @see addStyle
     */
    public function removeStyle($file);

}