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

$mood_cache = $cache->read("moods");

if($mybb->input['action'] == "do_change" && $mybb->request_method == "post")
{
	// Verify incoming POST request
	verify_post_check($mybb->input['my_post_key']);

	$update_mood = array(
		"mood" => intval($mybb->input['mood'])
	);
	$db->update_query("users", $update_mood, "uid='".intval($mybb->user['uid'])."'");

	eval("\$updated = \"".$templates->get("mood_updated")."\";");
	output_page($updated);
}

if(!$mybb->input['action'])
{
	$mybb->user['mood'] = intval($mybb->user['mood']);
	if(!$mybb->user['mood'])
	{
		$current_mood = $lang->no_mood;
	}
	else
	{
		$currentmood = $mood_cache[$mybb->user['mood']];
		if($mybb->user['language'] != "english" && $mybb->user['language'] != "")
		{
			$language = $mybb->user['language'];
		}
		elseif($mybb->settings['bblanguage'] != "english")
		{
			$language = $mybb->settings['bblanguage'];
		}
		else
		{
			$language = "english";
		}

		$path = str_replace("{lang}", $language, $currentmood['path']);
		$currentmood['name'] = $lang->parse($currentmood['name']);

		if(!is_file($path))
		{
			$englishpath = str_replace("{lang}", "english", $currentmood['path']);
			$current_mood = "<img src=\"{$englishpath}\" alt=\"{$currentmood['name']}\" title=\"{$currentmood['name']}\" />";
		}
		else
		{
			$current_mood = "<img src=\"{$path}\" alt=\"{$currentmood['name']}\" title=\"{$currentmood['name']}\" />";
		}
	}

	$query = $db->simple_select("moods", "*", "", array('order_by' => 'name', 'order_dir' => 'asc'));
	while($mood = $db->fetch_array($query))
	{
		$moodname = $lang->parse($mood['name']);

		$selected = "";
		if($mybb->user['mood'] == $mood['mid'])
		{
			$selected = "selected=\"selected\"";
		}
		$moodoptions .= "<option value=\"{$mood['mid']}\"{$selected}>{$moodname}</option>\n";
	}

	eval("\$mood = \"".$templates->get("mood")."\";");
	output_page($mood);
}

?>