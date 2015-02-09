<?php
/*
Plugin Name: Weaver II to Weaver Xtreme
Plugin URI: http://weavertheme.com
Description: Weaver II to Weaver Xtreme will convert various Weaver II and Weaver II Pro settings to Weaver Xtreme. Plugin options found on the Tools menu.
Author: Bruce Wampler
Author URI: http://weavertheme.com/about
Version: 1.0
License: GPL

Weaver II to Weaver Xtreme
Copyright (C) 2015, Bruce E. Wampler - weaver@weavertheme.com

GPL License: http://www.opensource.org/licenses/gpl-license.php

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

define ('WII2WX_VERSION','1.0');
define ('WII2WX_MINIFY', '');

function wii2wx_installed() {
    return true;
}

require_once(dirname( __FILE__ ) . '/wii2wx_core.php'); // NOW - load the plugin

?>
