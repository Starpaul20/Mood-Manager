<?php
/**
 * Mood Manager
 * Copyright 2012 Starpaul20
 */

define("IN_MYBB", 1);
define('THIS_SCRIPT', 'mood.php');

$templatelist = "mood,mood_updated,mood_option,global_mood";

require_once "./global.php";

// Load global language phrases
$lang->load("mood");

if($mybb->user['uid'] == 0)
{
	error_no_permission();
}

$mood_cache = $cache->read("moods");
$mybb->input['action'] = $mybb->get_input('action');

if($mybb->input['action'] == "do_change" && $mybb->request_method == "post")
{
	// Verify incoming POST request
	verify_post_check($mybb->get_input('my_post_key'));

	$update_mood = array(
		"mood" => $mybb->get_input('mood', MyBB::INPUT_INT)
	);
	$db->update_query("users", $update_mood, "uid='".(int)$mybb->user['uid']."'");

	eval("\$updated = \"".$templates->get("mood_updated", 1, 0)."\";");
	echo $updated;
	exit;
}

if(!$mybb->input['action'])
{
	$mybb->user['mood'] = (int)$mybb->user['mood'];
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

		$icon_path = str_replace("{lang}", $language, $currentmood['path']);
		$currentmood['name'] = $lang->parse($currentmood['name']);

		if(!is_file($icon_path))
		{
			$currentmood['path'] = str_replace("{lang}", "english", $currentmood['path']);
		}
		else
		{
			$currentmood['path'] = str_replace("{lang}", $language, $currentmood['path']);
		}

		eval("\$current_mood = \"".$templates->get("global_mood")."\";");
	}

	foreach($mood_cache as $mid => $mood)
	{
		$mood['name'] = $lang->parse($mood['name']);

		$selected = '';
		if($mybb->user['mood'] == $mood['mid'])
		{
			$selected = "selected=\"selected\"";
		}

		eval("\$moodoptions .= \"".$templates->get("mood_option")."\";");
	}

	eval("\$changemood = \"".$templates->get("mood", 1, 0)."\";");
	echo $changemood;
	exit;
}
