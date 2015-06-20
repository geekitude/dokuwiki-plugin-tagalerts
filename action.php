<?php
/**
 * Tag Alerts plugin main file
 * 
 * @author: Simon Delage <simon.geekitude@gmail.com>
 * @license: CC Attribution-Share Alike 3.0 Unported <http://creativecommons.org/licenses/by-sa/3.0/>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');

require_once (DOKU_PLUGIN . 'action.php');

class action_plugin_tagalerts extends DokuWiki_Action_Plugin{

    function register(&$controller) {
        $controller->register_hook('TPL_METAHEADER_OUTPUT', 'AFTER', $this, 'init', array());
        $controller->register_hook('TPL_TOC_RENDER', 'AFTER', $this, 'alert', array());
        $controller->register_hook('PLUGIN_TAG_LINK', 'AFTER', $this, 'link', array());
        $controller->register_hook('CONFMANAGER_CONFIGFILES_REGISTER', 'BEFORE',  $this, 'addConfigFile', array());
    }

    function init(&$event, $param) {
        global $ID;
        global $conf;
 
        $tagplugin = plugin_load('helper', 'tag');
        if(is_null($tagplugin)) {
            msg($this->getLang('tag_required'), -1);
            return false;
        }
        // Fetch tags for the page; stop proceeding when no tags specified
        $tags = p_get_metadata($ID, 'subject', METADATA_DONT_RENDER);
        if(is_null($tags)) true;
 
        foreach($event->data['meta'] as &$meta) {
            if($meta['name'] == 'keywords') {
                // Get an array of page's tags
                $this->pagetags = explode(',', $meta['content']);
            }
        }
        // Load special messages from ...tagalerts/conf/tagalerts.conf to global conf
//        $specAlertsFile = dirname(__FILE__).'/conf/tagalerts.conf';
        $specAlertsFile = DOKU_CONF.'tagalerts.conf';
        if (@file_exists($specAlertsFile)) {
            $conf['plugin']['tagalerts']['specAlerts'] = confToHash($specAlertsFile);
        }
    }

    function alert(&$event, $param) {
        global $conf;
        global $ACT;

        if (($this->getConf('action') == "messages") & ($ACT == "show")) {
            // Get an array of notification triggers from 'notify' option (make sure the list is well formated: no blanks between triggers and no '_' in triggers)
            $errorTriggers = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('error'))));
            $infoTriggers = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('info'))));
            $successTriggers = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('success'))));
            $notifyTriggers = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('notify'))));
            // Get matches between page tags and triggers (don't preserve keys)
            $tagalerts = array();
            $tagalerts['error'] = array_values((array_intersect($this->pagetags, $errorTriggers)));
            $tagalerts['info'] = array_values((array_intersect($this->pagetags, $infoTriggers)));
            $tagalerts['success'] = array_values((array_intersect($this->pagetags, $successTriggers)));
            $tagalerts['notify'] = array_values((array_intersect($this->pagetags, $notifyTriggers)));
            foreach($tagalerts as $type=>$tag) {
                if (isset($tag[0])) {
                    // Alert from conf file
                    if (isset($conf['plugin']['tagalerts']['specAlerts'][$tag[0]])) {
                        $msg = $conf['plugin']['tagalerts']['specAlerts'][$tag[0]];
                    // Or from localized $conf
                    } else {
                        $msg = $this->getLang('tagalerts').$tag[0].".";
                    }
                    echo '<div class="tag'.$type.'">'.hsc($msg).'</div>';
                }
            }
        }
    }

    function link(&$event) {
        global $conf;
        global $ACT;

        if (($this->getConf('action') == "inline") & ($ACT == "show")) {
            $href = $event->data['href'];
            $class = $event->data['class'];
            $tooltip = $event->data['tooltip'];
            $title = $event->data['title'];
            // CLASS
            // Get an array of notification triggers from 'notify' option (make sure the list is well formated: no blanks between triggers and no '_' in triggers)
            $triggers = array();
            $triggers['error'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('error'))));
            $triggers['info'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('info'))));
            $triggers['success'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('success'))));
            $triggers['notify'] = explode(',',str_replace('_', ' ', str_replace(', ', ',', $this->getConf('notify'))));
            foreach($triggers as $type=>$val) {
                if (in_array($title, $val)) {
                    $class = $class.' tag'.$type;
                }
            }
            // TOOLTIP
            if (isset($conf['plugin']['tagalerts']['specAlerts'][$title])) {
                $tooltip = $conf['plugin']['tagalerts']['specAlerts'][$title]." (".$tooltip.")";
            }
            // RESULT
            $event->data = array(
                'href' => $href,
                'class' => $class,
                'tooltip' => $tooltip,
                'title' => $title
            );
        }
    }

    // Register the plugin conf file in ConfManager Plugin
    public function addConfigFile(Doku_Event $event, $params) {
        if (class_exists('ConfigManagerTwoLine')) {
//            $config = new ConfigManagerTwoLine('Tag Alerts', $this->getLang('confdescription'), DOKU_PLUGIN . 'tagalerts/conf/tagalerts.conf');
            $config = new ConfigManagerTwoLine('Tag Alerts', $this->getLang('confdescription'), DOKU_CONF . 'tagalerts.conf');
            $event->data[] = $config;
        }
    }
}
