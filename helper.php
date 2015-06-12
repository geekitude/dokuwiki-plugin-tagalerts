<?php

/**
 * Tag Alerts plugin helper for tag plugin
 * 
 * @author: Simon Delage <simon.geekitude@gmail.com>
 * @license: CC Attribution-Share Alike 3.0 Unported <http://creativecommons.org/licenses/by-sa/3.0/>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");

/**
 * Helper part of the tag plugin, allows to query and print tags
 */
class helper_plugin_tagalerts extends DokuWiki_Plugin {

    /**
     * Returns the links for given tags
     *
     * @param array $tags an array of tags
     * @return string HTML link tags
     */
    function extraClass($tag, $class) {
        global $ID;
        global $conf;

        if ($this->getConf('inline')) {
            // Get an array of notification triggers from 'notify' option (make sure the list is well formated: no blanks between triggers and no '_' in triggers)
            $triggers = array();
            $triggers['error'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('error'))));
            $triggers['info'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('info'))));
            $triggers['success'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('success'))));
            $triggers['notify'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('notify'))));
            foreach($triggers as $type=>$val) {
                if (in_array($tag, $val)) {
                    $class = $class.' tag'.$type;
                }
            }
            return $class;
        }
    }

    function tooltip($tag, $tooltip) {
        global $ID;
        global $conf;

        if (isset($conf['plugin']['tagalerts']['specAlerts'][$tag])) {
            $tooltip = $conf['plugin']['tagalerts']['specAlerts'][$tag]." (".$tooltip.")";
        }
        return $tooltip;
    }

}
// vim:ts=4:sw=4:et:  
