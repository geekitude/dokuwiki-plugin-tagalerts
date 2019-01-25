<?php
/**
 * Configuration defaults file for Tag Alert plugin
 * 
 * @author   Simon DELAGE <sdelage@gmail.com>
 * @license: CC Attribution-Share Alike 3.0 Unported <http://creativecommons.org/licenses/by-sa/3.0/>
 */

$conf['action']                 = 'inline';
$conf['error']                  = '';       //comma separated list of tags for wich a "tag error" should be thrown
$conf['info']                   = '';       //comma separated list of tags for wich a "tag info" should be thrown
$conf['success']                = '';       //comma separated list of tags for wich a "tag success" should be thrown
$conf['notify']                 = '';       //comma separated list of tags for wich a "tag notification" should be thrown
$conf['forcemsg']               = '';       //comma separated list of tags for wich messages will be forced, even with `inline` setting
