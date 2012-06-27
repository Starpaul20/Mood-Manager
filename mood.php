<?php
/**
 * Mood Manager
 * Copyright 2012 Starpaul20
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'mood.php');

$templatelist = 'mood,mood_updated';
require_once "./global.php";

// Load global language phrases
$lang->load("mood");

if($mybb->user['uid'] == 0)
{
	error_no_permission();
}

if($mybb->input['action'] == "do_change" && $mybb->request_method == "post")
{
	// Verify incoming POST request
	verify_post_check($mybb->input['my_post_key']);

	$update_mood = array(
		"mood" => $db->escape_string($mybb->input['mood'])
	);
	$db->update_query("users", $update_mood, "uid='".intval($mybb->user['uid'])."'");

	eval("\$updated = \"".$templates->get("mood_updated")."\";");
	output_page($updated);
}

if(!$mybb->input['action'])
{
	$mybb->user['mood'] = htmlspecialchars_uni($mybb->user['mood']);
	if(!$mybb->user['mood'])
	{
		$current_mood = $lang->no_mood;
	}
	else
	{
		$current_mood = "<img src=images/mood/{$mybb->user['mood']}.gif alt=\"{$mybb->user['mood']}\">";
	}

	$mood_list = array();
	$mood_files = scandir(MYBB_ROOT."images/mood/");
	foreach($mood_files as $mood_file)
	{
		if(is_file(MYBB_ROOT."images/mood/{$mood_file}") && get_extension($mood_file) == "gif")
		{
			$mood_file_id = preg_replace("#\.".get_extension($mood_file)."$#i", "$1", $mood_file);
			$mood_list[$mood_file_id] = $mood_file_id;
		}
	}

	$moodoptions = '';
	foreach($mood_list as $value => $option)
	{
		$selected = "";
		if($mybb->user['mood'] == $value)
		{
			$selected = "selected=\"selected\"";
		}
		$moodoptions .= "<option value=\"{$value}\"{$selected}>{$option}</option>\n";
	}

	eval("\$mood = \"".$templates->get("mood")."\";");
	output_page($mood);
}

?>