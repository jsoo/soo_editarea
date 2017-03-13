<?php

$plugin['version'] = '0.1.5';
$plugin['author'] = 'Jeff Soo';
$plugin['author_uri'] = 'http://ipsedixit.net/txp/';
$plugin['description'] = 'Integrate the EditArea admin-side code editor';
$plugin['type'] = 3; // admin-side only
$plugin['allow_html_help'] = 1;

defined('PLUGIN_HAS_PREFS') or define('PLUGIN_HAS_PREFS', 0x0001); 
defined('PLUGIN_LIFECYCLE_NOTIFY') or define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); 
$plugin['flags'] = PLUGIN_HAS_PREFS | PLUGIN_LIFECYCLE_NOTIFY;

if (! defined('txpinterface')) {
    global $compiler_cfg;
    @include_once('config.php');
    @include_once($compiler_cfg['path']);
}

# --- BEGIN PLUGIN CODE ---

if (@txpinterface == 'admin') {
    @require_plugin('soo_plugin_pref');     // optional

    add_privs('plugin_prefs.soo_editarea','1,2');
    add_privs('plugin_lifecycle.soo_editarea','1,2');
    register_callback('soo_editarea_manage_prefs', 'plugin_prefs.soo_editarea');
    register_callback('soo_editarea_manage_prefs', 'plugin_lifecycle.soo_editarea');

    // Only activate plugin when needed
    if (in_array(gps('event'), array('page', 'form', 'css'))) {
        register_callback('soo_editarea', 'admin_side', 'head_end');
    }
}

function soo_editarea_manage_prefs($event, $step)
{
    if (function_exists('soo_plugin_pref')) {
        return soo_plugin_pref($event, $step, soo_editarea_pref_spec());
    }
    
        // message to install soo_plugin_pref
    if (substr($event, 0, 12) == 'plugin_prefs') {
        $plugin = substr($event, 13);
        $message = '<p><br /><strong>' . gTxt('edit') . " $plugin " .
            gTxt('edit_preferences') . ':</strong><br />' . gTxt('install_plugin') . 
            ' <a href="http://ipsedixit.net/txp/92/soo_plugin_pref">' . 
            'soo_plugin_pref</a></p>';
        pagetop(gTxt('edit_preferences') . " &#8250; $plugin", $message);
    }
}

function soo_editarea_pref_spec()
{
    return array(
        'page_form_lang' => array(
            'val'   => 'html',
            'html'  => 'text_input',
            'text'  => 'Page Template and Form syntax',
        ),
        'editarea_dir' => array(
            'val'   => 'edit_area',
            'html'  => 'text_input',
            'text'  => 'EditArea directory',
        ),
        'language' => array(
            'val'   => 'en',
            'html'  => 'text_input',
            'text'  => 'Language',
        ),
        'min_height' => array(
            'val'   => 500,
            'html'  => 'text_input',
            'text'  => 'Editor height',
        ),
        'min_width' => array(
            'val'   => 400,
            'html'  => 'text_input',
            'text'  => 'Editor width',
        ),
        'font_size' => array(
            'val'   => 10,
            'html'  => 'text_input',
            'text'  => 'Font size',
        ),
        'font_family' => array(
            'val'   => 'monospace',
            'html'  => 'text_input',
            'text'  => 'Font family',
        ),
        'replace_tab_by_spaces' => array(
            'val'   => 0,
            'html'  => 'text_input',
            'text'  => 'Convert tab to spaces',
        ),
        'plugins' => array(
            'val'   => '',
            'html'  => 'text_input',
            'text'  => 'EditArea plugins (comma-separated)',
        ),
    );
}

function soo_editarea_prefs()
{
    static $prefs;
    if (! $prefs) {
        foreach (soo_editarea_pref_spec() as $name => $spec) {
            $prefs[$name] = $spec['val'];
        }
        if (function_exists('soo_plugin_pref_vals')) {
            $prefs = array_merge($prefs, soo_plugin_pref_vals('soo_editarea'));
        }
    }
    return $prefs;
}

function soo_editarea()
{
    $prefs = soo_editarea_prefs();
    extract($prefs);
    $exclude_from_init = array_flip(array('page_form_lang', 'editarea_dir'));
    $init = array_diff_key($prefs, $exclude_from_init);
    $textarea = array(
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
    $init = array_merge($textarea[gps('event')], $init);    
//  if ( substr(gps('name'), -3) == '.js' )
//      $init['syntax'] = 'js';

    $func = 'editAreaLoader.init({'.n.t.'start_highlight: true';
    foreach ($init as $k => $v) if ($v) {
        $func .= n.t.",$k: ".( is_numeric($v) ? $v : "\"$v\"" );
    }
    $func .= n.'});';
    
    echo 
        '<script type="text/javascript" src="',
        $editarea_dir,
        '/edit_area_full.js"></script>', 
        n, script_js($func), n
    ;
}

# --- END PLUGIN CODE ---

?>
