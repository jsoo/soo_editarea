<?php

$plugin['name'] = 'soo_editarea';
$plugin['version'] = '0.1.3';
$plugin['author'] = 'Jeff Soo';
$plugin['author_uri'] = 'http://ipsedixit.net/txp/';
$plugin['description'] = 'Integrate the EditArea admin-side code editor';
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
	register_callback('soo_editarea_manage_prefs', 'plugin_prefs.soo_editarea');
	register_callback('soo_editarea_manage_prefs', 'plugin_lifecycle.soo_editarea');

	// Only activate plugin when needed
	if ( in_array(gps('event'), array('page', 'form', 'css')) )
		register_callback('soo_editarea', 'admin_side', 'head_end');
}

function soo_editarea_manage_prefs( $event, $step )
{
	if ( function_exists('soo_plugin_pref') )
		return soo_plugin_pref($event, $step, soo_editarea_pref_spec());
	
		// message to install soo_plugin_pref
	if ( substr($event, 0, 12) == 'plugin_prefs' ) {
		$plugin = substr($event, 13);
		$message = '<p><br /><strong>' . gTxt('edit') . " $plugin " .
			gTxt('edit_preferences') . ':</strong><br />' . gTxt('install_plugin') . 
			' <a href="http://ipsedixit.net/txp/92/soo_plugin_pref">' . 
			'soo_plugin_pref</a></p>';
		pagetop(gTxt('edit_preferences') . " &#8250; $plugin", $message);
	}
}

function soo_editarea_pref_spec( )
{
	return array(
		'page_form_lang' => array(
			'val'	=> 'html',
			'html'	=> 'text_input',
			'text'	=> 'Page Template and Form syntax',
		),
		'editarea_dir'	=>	array(
			'val'	=>	'edit_area',
			'html'	=>	'text_input',
			'text'	=>	'EditArea directory',
		),
		'language'	=>	array(
			'val'	=>	'en',
			'html'	=>	'text_input',
			'text'	=>	'Language',
		),
		'font_size'	=>	array(
			'val'	=>	10,
			'html'	=>	'text_input',
			'text'	=>	'Font size',
		),
		'font_family'	=>	array(
			'val'	=>	'monospace',
			'html'	=>	'text_input',
			'text'	=>	'Font family',
		),
		'replace_tab_by_spaces'	=>	array(
			'val'	=>	0,
			'html'	=>	'text_input',
			'text'	=>	'Convert tab to spaces',
		),
	);
}

function soo_editarea_prefs( )
{
	static $prefs;
	if ( ! $prefs )
	{
		foreach ( soo_editarea_pref_spec() as $name => $spec )
			$prefs[$name] = $spec['val'];
		if ( function_exists('soo_plugin_pref_vals') )
			$prefs = array_merge($prefs, soo_plugin_pref_vals('soo_editarea'));
	}
	return $prefs;
}

function soo_editarea( $event, $step )
{
	$soo_editarea = soo_editarea_prefs();
	if ( $soo_editarea['replace_tab_by_spaces'] == 0 )
		unset($soo_editarea['replace_tab_by_spaces']);
	extract($soo_editarea);
	
	$vars = array(
		'page' => array(
			'id' => 'html',
			'syntax' => $page_form_lang,
		),
		'form' => array(
			'id' => 'form',
			'syntax' => $page_form_lang,
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
EOT;
	array_shift($soo_editarea);
	array_shift($soo_editarea);
	foreach ( array_keys($soo_editarea) as $k )
		$init .= n . t . ",$k: " . ( is_numeric($$k) ? $$k : '"' . $$k . '"' );
	$init .= n . '});';
	
	echo 
		'<script type="application/javascript" src="',
		$editarea_dir,
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
* "Configuration options":#configuration
* "Txp tag highlighting":#txp_highlighting
* "History":#history

 </div>

h1. soo_editarea

h2(#overview). Overview

"EditArea":http://www.cdolivet.com/index.php?page=editArea is a JavaScript-based code editor for browser textareas. Features include:

* Code highlighting
* Browser tab-key override for proper tabbing in the textarea
* Full-screen mode
* Find & replace w/ regex support
* Multiple undo/redo

*soo_editarea* provides easy integration of EditArea into Textpattern. (Well, pretty easy: there are a few steps involved.)

_Suggested by the (apparently defunct) "atb_editarea":http://forum.textpattern.com/viewtopic.php?id=33915 plugin, and "discussion":http://forum.textpattern.com/viewtopic.php?id=21370 on the Txp forum._

h3. Features:

You can set plugin preferences for:

* Syntax language for Page & Form editing, allowing custom syntax file 
* Source path, making it easier to share one EditArea installation across multiple sites
* Various EditArea options (tooltip language, font size & family, &c.)

h2(#installation). Installation

"Install and activate the plugin":http://textbook.textpattern.net/wiki/index.php?title=Plugins#Downloading_.26_installing_plugins in the usual way.

"Download EditArea":http://sourceforge.net/projects/editarea/files/ and place the @edit_area@ directory in a server-accessible location of your choice. (The default is @/textpattern/edit_area@, but you can change this in the plugin's Options settings.)

Optionally, download the "txp.js syntax file":http://ipsedixit.net/file_download/16/txp.js (or "create your own":http://www.cdolivet.com/editarea/editarea/docs/customization_syntax.html) and place it in @edit_area/reg_syntax@.

h2(#configuration). Configuration options

The first two steps in Installation, above, are all you need to get EditArea working with standard HTML syntax highlighting for Pages and Forms, and CSS highlighting for Styles.

To activate the Txp syntax file (from step 3, above), or to use a different location for the EditArea files, install and activate the "soo_plugin_pref":http://ipsedixit.net/txp/92/soo_plugin_pref plugin (Txp 4.2.0 or greater %(required)required%). Then, in the "main plugin panel":http://textbook.textpattern.net/wiki/index.php?title=Plugins, click the Options link for *soo_editarea* (look in the *Manage* column at right). 

To use the Txp syntax file, change the *Page Template and Form syntax* setting to "txp".

The *EditArea directory* setting is the URL (relative to @/textpattern/index.php@) of the EditArea files. (Hint: for sharing one set of EditArea files across multiple sites, put the files in any server-accessible location you choose, then add a symbolic link to each site's @/textpattern@ directory.)

h3. More options:

* *Language:* for EditArea tooltips. Use the two-letter code corresponding to the file in @edit_area/langs@.
* *Font size:* default font size for the editor
* *Font family:* comma-separated list of font names (%(default)default% "monospace").
* *Convert tab to spaces:* convert tabs to this many spaces (leave at 0 for standard tabs)


h2(#txp_highlighting). Txp tag highlighting

By default the plugin uses EditArea's HTML highlighting for Page Template and Form editing, giving Txp tags the same highlight color as HTML tags. To have Txp tags appear in a different color, follow the installation/configuration instructions above for adding the txp.js file. (If you later upgrade the EditArea files you will have to remember to preserve this file.)

The txp.js file linked above highlights Txp tags in a lovely orange color. To change it (or any of the other colors), edit txp.js to suit (look toward the bottom of the file). If you'd prefer a soothing green for your Txp tags, uncomment the line near the bottom labeled "green", and comment the line above it labeled "orange" (i.e., remove the two slashes at the start of the "green" line, and add two slashes to the start of the "orange" line).

h2(#history). Version History

h3. 0.1.3 (unreleased)

h3. 0.1.2 (2010/12/20)

* Added preference settings for several EditArea options

h3. 0.1.1 (2010/12/20)

* Documentation update _[thanks to Marc C. for the suggestions]_

h3. 0.1.0 (2010/12/20)

* Initial release
* EditArea integration for Txp's Page Template, Form, and CSS editors

</div>
# --- END PLUGIN HELP ---
-->
<?php
}

?>
