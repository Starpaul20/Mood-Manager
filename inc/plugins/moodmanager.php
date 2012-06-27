<?php
/**
 * Mood Manager
 * Copyright 2012 Starpaul20
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Neat trick for caching our custom template(s)
if(my_strpos($_SERVER['PHP_SELF'], 'showthread.php'))
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_mood';
}

// Tell MyBB when to run the hooks
$plugins->add_hook("global_start", "moodmanager_link");
$plugins->add_hook("postbit", "moodmanager_postbit");
$plugins->add_hook("postbit_pm", "moodmanager_postbit");
$plugins->add_hook("postbit_announcement", "moodmanager_postbit");
$plugins->add_hook("postbit_prev", "moodmanager_postbit");
$plugins->add_hook("member_profile_end", "moodmanager_profile");

// The information that shows up on the plugin manager
function moodmanager_info()
{
	return array(
		"name"				=> "Mood Manager",
		"description"		=> "Allows users to set a mood for themselves to display on postbit/profile.",
		"website"			=> "http://galaxiesrealm.com/index.php",
		"author"			=> "Starpaul20",
		"authorsite"		=> "http://galaxiesrealm.com/index.php",
		"version"			=> "1.0",
		"guid"				=> "4815b6d6799907a85c547c902057dec2",
		"compatibility"		=> "16*"
	);
}

// This function runs when the plugin is activated.
function moodmanager_activate()
{
	global $db;

	if(!$db->field_exists("mood", "users"))
	{
		$db->add_column("users", "mood", "varchar(20) NOT NULL default ''");
	}

	$insert_array = array(
		'title'		=> 'postbit_mood',
		'template'	=> $db->escape_string('{$lang->mood}: {$mood}<br />'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'mood',
		'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->change_mood}</title>
{$headerinclude}
</head>
<body>
<br />
<form action="mood.php" method="post">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="trow1" style="padding: 20px">
<strong>{$lang->change_your_mood}</strong><br /><br />
{$lang->current_mood}: {$current_mood}
<br /><br />
<select id="mood" name="mood">
<option value="">{$lang->no_mood}</option>
{$moodoptions}
</select>
<input type="hidden" name="action" value="do_change" />
<input type="submit" class="button" name="submit" value="{$lang->change_mood}" />
</td>
</tr>
</table>
</form>
</body>
</html>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'mood_updated',
		'template'	=> $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->mood_updated}</title>
{$headerinclude}
</head>
<body onunload="window.opener.location.reload();">
<br />
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="trow1" style="padding: 20px">
<strong>{$lang->mood_updated}</strong><br /><br />
<blockquote>{$lang->mood_updated_message}</blockquote>
<br /><br />
<div style="text-align: center;">
<script type="text/javascript">
<!--
document.write(\'[<a href="javascript:window.close();">{$lang->close_window}</a>]\');
// -->
</script>
</div>
</td>
</tr>
</table>
</body>
</html>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}<br />{$post[\'usermood\']}');
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}<br />{$post[\'usermood\']}');
	find_replace_templatesets("member_profile", "#".preg_quote('{$online_status}')."#i", '{$online_status}<br /><strong>{$lang->mood}:</strong> {$mood}');
	find_replace_templatesets("header_welcomeblock_member", "#".preg_quote('{$lang->welcome_open_buddy_list}</a>')."#i", '{$lang->welcome_open_buddy_list}</a> | {$moodlink}');
}

// This function runs when the plugin is deactivated.
function moodmanager_deactivate()
{
	global $db;

	if($db->field_exists("mood", "users"))
	{
		$db->drop_column("users", "mood");
	}

	$db->delete_query("templates", "title IN('postbit_mood','mood','mood_updated')");

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('<br />{$post[\'usermood\']}')."#i", '', 0);
	find_replace_templatesets("postbit_classic", "#".preg_quote('<br />{$post[\'usermood\']}')."#i", '', 0);
	find_replace_templatesets("member_profile", "#".preg_quote('<br /><strong>{$lang->mood}:</strong> {$mood}')."#i", '', 0);
	find_replace_templatesets("header_welcomeblock_member", "#".preg_quote(' | {$moodlink}')."#i", '', 0);
}

// Link to Mood pop-up
function moodmanager_link()
{
	global $mybb, $lang, $templates, $moodlink;
	$lang->load('mood');

	if($mybb->user['uid'])
	{
		$moodlink = "<strong><a href=\"javascript:MyBB.popupWindow('mood.php', 'mood', '400', '300')\">{$lang->change_mood}</a></strong>";
	}
}

// Display Mood on Postbit
function moodmanager_postbit($post)
{
	global $db, $mybb, $lang, $templates;
	$lang->load("mood");

	if($post['mood'])
	{
		$post_mood = htmlspecialchars_uni($post['mood']);
		$mood = "<img src=\"images/mood/{$post_mood}.gif\" alt=\"{$post_mood}\" />";
	}
	else
	{
		$mood = $lang->no_mood;
	}

	eval("\$post['usermood'] = \"".$templates->get("postbit_mood")."\";");

	return $post;
}

// Display Mood in Profiles
function moodmanager_profile()
{
	global $db, $mybb, $lang, $memprofile, $mood;
	$lang->load("mood");

	if($memprofile['mood'])
	{
		$post_mood = htmlspecialchars_uni($memprofile['mood']);
		$mood = "<img src=\"images/mood/{$post_mood}.gif\" alt=\"{$post_mood}\" />";
	}
	else
	{
		$mood = $lang->not_specified;
	}
}

?>