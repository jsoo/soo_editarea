<?php

$plugin['name'] = 'soo_editarea';
$plugin['version'] = '0.1.0';
$plugin['author'] = 'Jeff Soo';
$plugin['author_uri'] = 'http://ipsedixit.net/txp/';
$plugin['description'] = 'Implement EditArea for admin-side code highlighting';
$plugin['type'] = 3; // admin-side only

defined('PLUGIN_HAS_PREFS') or define('PLUGIN_HAS_PREFS', 0x0001); 
defined('PLUGIN_LIFECYCLE_NOTIFY') or define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); 
$plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;

defined('txpinterface') or @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---

if ( @txpinterface == 'admin' ) 
{
	@require_plugin('soo_plugin_pref');		// optional

	add_privs('plugin_prefs.soo_editarea','1,2');
	add_privs('plugin_lifecycle.soo_editarea','1,2');
	register_callback('soo_editarea_prefs', 'plugin_prefs.soo_editarea');
	register_callback('soo_editarea_prefs', 'plugin_lifecycle.soo_editarea');

	// Only activate plugin when needed
	if ( in_array(gps('event'), array('page', 'form', 'css')) )
	{
		register_callback('soo_editarea', 'admin_side', 'head_end');
		global $soo_editarea;
		$soo_editarea = function_exists('soo_plugin_pref_vals') ? 
			array_merge(soo_editarea_defaults(true), soo_plugin_pref_vals('soo_editarea')) 
			: soo_editarea_defaults(true);
	}
}


function soo_editarea_prefs( $event, $step )
{
	if ( function_exists('soo_plugin_pref') )
		return soo_plugin_pref($event, $step, soo_editarea_defaults());
	if ( substr($event, 0, 12) == 'plugin_prefs' ) {
		$plugin = substr($event, 13);
		$message = '<p><br /><strong>' . gTxt('edit') . " $plugin " .
			gTxt('edit_preferences') . ':</strong><br />' . gTxt('install_plugin') . 
			' <a href="http://ipsedixit.net/txp/92/soo_plugin_pref">' . 
			'soo_plugin_pref</a></p>';
		pagetop(gTxt('edit_preferences') . " &#8250; $plugin", $message);
	}
}

function soo_editarea_defaults( $vals_only = false )
{
	$defaults = array(
		'page_form_lang'	=>	array(
			'val'	=>	'html',
			'html'	=>	'text_input',
			'text'	=>	'Page Template and Form syntax',
		),
		'editarea_dir'	=>	array(
			'val'	=>	'edit_area',
			'html'	=>	'text_input',
			'text'	=>	'EditArea directory',
		),
	);
	if ( $vals_only )
		foreach ( $defaults as $name => $arr )
			$defaults[$name] = $arr['val'];
	return $defaults;
}

function soo_editarea( $event, $step )
{
	global $soo_editarea;
	$vars = array(
		'page' => array(
			'id' => 'html',
			'syntax' => $soo_editarea['page_form_lang'],
		),
		'form' => array(
			'id' => 'form',
			'syntax' => $soo_editarea['page_form_lang'],
		),
		'css' => array(
			'id' => 'css',
			'syntax' => 'css',
		),
	);
	extract($vars[gps('event')]);	// validated in plugin init
	$init = <<<EOT
editAreaLoader.init({
	id : "$id"
	,syntax: "$syntax"
	,start_highlight: true
});

EOT;
	
	echo 
		'<script type="application/javascript" src="',
		$soo_editarea['editarea_dir'],
		'/edit_area_full.js"></script>',
		n,
		script_js($init),
		n
	;
	
}

# --- END PLUGIN CODE ---

if (0) {
?>
<!-- CSS SECTION
# --- BEGIN PLUGIN CSS ---
<style type="text/css">
div#sed_help pre {padding: 0.5em 1em; background: #eee; border: 1px dashed #ccc;}
div#sed_help h1, div#sed_help h2, div#sed_help h3, div#sed_help h3 code {font-family: sans-serif; font-weight: bold;}
div#sed_help h1, div#sed_help h2, div#sed_help h3 {margin-left: -1em;}
div#sed_help h2, div#sed_help h3 {margin-top: 2em;}
div#sed_help h1 {font-size: 2.4em;}
div#sed_help h2 {font-size: 1.8em;}
div#sed_help h3 {font-size: 1.4em;}
div#sed_help h4 {font-size: 1.2em;}
div#sed_help h5 {font-size: 1em;margin-left:1em;font-style:oblique;}
div#sed_help h6 {font-size: 1em;margin-left:2em;font-style:oblique;}
div#sed_help li {list-style-type: disc;}
div#sed_help li li {list-style-type: circle;}
div#sed_help li li li {list-style-type: square;}
div#sed_help li a code {font-weight: normal;}
div#sed_help li code:first-child {background: #ddd;padding:0 .3em;margin-left:-.3em;}
div#sed_help li li code:first-child {background:none;padding:0;margin-left:0;}
div#sed_help dfn {font-weight:bold;font-style:oblique;}
div#sed_help .required, div#sed_help .warning {color:red;}
div#sed_help .default {color:green;}
div#sed_help kbd {
	font-family: Verdana, Arial, sans-serif;
	font-size: 11px;
	color: #000;
	line-height: 11px;
	height: 17px;
	background: #eee;
	border: solid #aaa;
	border-width: 1px 0 0 1px;
	padding: -1px 1px;	
}
</style>
# --- END PLUGIN CSS ---
-->
<!-- HELP SECTION
# --- BEGIN PLUGIN HELP ---
<div id="sed_help">

 <div id="toc">

h2. Contents

* "Overview":#overview
* "Installation":#installation
* "Txp tag highlighting":#txp_highlighting
* "History":#history

 </div>

h1. soo_editarea

h2(#overview). Overview

Add "EditArea":http://www.cdolivet.com/index.php?page=editArea code highlighting to Txp admin textareas. Inspired by the atb_editarea plugin.

h2(#installation). Installation

"Install the plugin":http://textbook.textpattern.net/wiki/index.php?title=Plugins#Downloading_.26_installing_plugins in the usual way. 

The plugin does not contain the actual EditArea files; you must install those separately. "Download EditArea":http://sourceforge.net/projects/editarea/files/ and place the @edit_area@ directory in a server-accessible location of your choice. 

If you put it somewhere other than at the root level of your @/textpattern@ directory, you will need to tell the plugin where to find it. The recommended way is to install "soo_plugin_pref":http://ipsedixit.net/txp/92/soo_plugin_pref ( %(required)requires% Txp version 4.2.0 or greater), then set this value in the "plugin options":http://textbook.textpattern.net/wiki/index.php?title=Plugins#Panel_layout_.26_controls. (Alternatively, you could edit the plugin code directly.)

h2(#txp_highlighting). Txp tag highlighting

By default the plugin uses EditArea's HTML highlighting for Page Template and Form editing, giving Txp tags the same highlight color as HTML tags. To have Txp tags appear in a different color, add a new syntax file at @edit_area/reg_syntax/@. (Whenever you upgrade the EditArea files you will have to remember to preserve this file.) Then change the *soo_editarea* option for Page Template & Form syntax (the installation instructions tell you how to set plugin options).

You can "download a txp.js syntax file":http://ipsedixit.net/file_download/16/txp.js from the "author's website":http://ipsedixit.net/txp.

h2(#history). History

h3. Version 0.1.0 (2010/12/20)

* Initial release.

</div>
# --- END PLUGIN HELP ---
-->
<?php
}

?>
