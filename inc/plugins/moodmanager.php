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

$plugins->add_hook("admin_config_menu", "moodmanager_admin_menu");
$plugins->add_hook("admin_config_action_handler", "moodmanager_admin_action_handler");
$plugins->add_hook("admin_config_permissions", "moodmanager_admin_permissions");
$plugins->add_hook("admin_tools_get_admin_log_action", "moodmanager_admin_adminlog");

// The information that shows up on the plugin manager
function moodmanager_info()
{
	global $lang;
	$lang->load("config_moods");

	return array(
		"name"				=> $lang->moodmanager_info_name,
		"description"		=> $lang->moodmanager_info_desc,
		"website"			=> "http://galaxiesrealm.com/index.php",
		"author"			=> "Starpaul20",
		"authorsite"		=> "http://galaxiesrealm.com/index.php",
		"version"			=> "1.0",
		"codename"			=> "moodmanager",
		"compatibility"		=> "18*"
	);
}

// This function runs when the plugin is installed.
function moodmanager_install()
{
	global $db;
	moodmanager_uninstall();
	$collation = $db->build_create_table_collation();

	$db->write_query("CREATE TABLE ".TABLE_PREFIX."moods (
				mid int(10) unsigned NOT NULL auto_increment,
				name varchar(120) NOT NULL default '',
				path varchar(220) NOT NULL default '',
				PRIMARY KEY(mid)
			) ENGINE=MyISAM{$collation}");

	$db->add_column("users", "mood", "int(3) unsigned NOT NULL default '0'");

	$db->write_query("INSERT INTO ".TABLE_PREFIX."moods (mid, name, path) VALUES
(1, '<lang:mood_addicted>', 'images/mood/{lang}/Addicted.gif'),
(2, '<lang:mood_adored>', 'images/mood/{lang}/Adored.gif'),
(3, '<lang:mood_aggressive>', 'images/mood/{lang}/Aggressive.gif'),
(4, '<lang:mood_airheaded>', 'images/mood/{lang}/Airheaded.gif'),
(5, '<lang:mood_allshookup>', 'images/mood/{lang}/AllShookUp.gif'),
(6, '<lang:mood_alone>', 'images/mood/{lang}/Alone.gif'),
(7, '<lang:mood_amazed>', 'images/mood/{lang}/Amazed.gif'),
(8, '<lang:mood_amused>', 'images/mood/{lang}/Amused.gif'),
(9, '<lang:mood_angelic>', 'images/mood/{lang}/Angelic.gif'),
(10, '<lang:mood_angry>', 'images/mood/{lang}/Angry.gif'),
(11, '<lang:mood_annoyed>', 'images/mood/{lang}/Annoyed.gif'),
(12, '<lang:mood_apathetic>', 'images/mood/{lang}/Apathetic.gif'),
(13, '<lang:mood_apprehensive>', 'images/mood/{lang}/Apprehensive.gif'),
(14, '<lang:mood_approved>', 'images/mood/{lang}/Approved.gif'),
(15, '<lang:mood_artistic>', 'images/mood/{lang}/Artistic.gif'),
(16, '<lang:mood_ashamed>', 'images/mood/{lang}/Ashamed.gif'),
(17, '<lang:mood_asleep>', 'images/mood/{lang}/Asleep.gif'),
(18, '<lang:mood_bahahaha>', 'images/mood/{lang}/Bahahaha.gif'),
(19, '<lang:mood_bashful>', 'images/mood/{lang}/Bashful.gif'),
(20, '<lang:mood_bawling>', 'images/mood/{lang}/Bawling.gif'),
(21, '<lang:mood_believing>', 'images/mood/{lang}/Believing.gif'),
(22, '<lang:mood_bewildered>', 'images/mood/{lang}/Bewildered.gif'),
(23, '<lang:mood_bitchy>', 'images/mood/{lang}/Bitchy.gif'),
(24, '<lang:mood_blah>', 'images/mood/{lang}/Blah.gif'),
(25, '<lang:mood_blessed>', 'images/mood/{lang}/Blessed.gif'),
(26, '<lang:mood_bookworm>', 'images/mood/{lang}/Bookworm.gif'),
(27, '<lang:mood_bored>', 'images/mood/{lang}/Bored.gif'),
(28, '<lang:mood_brave>', 'images/mood/{lang}/Brave.gif'),
(29, '<lang:mood_breezy>', 'images/mood/{lang}/Breezy.gif'),
(30, '<lang:mood_broken>', 'images/mood/{lang}/Broken.gif'),
(31, '<lang:mood_brooding>', 'images/mood/{lang}/Brooding.gif'),
(32, '<lang:mood_bumbly>', 'images/mood/{lang}/Bumbly.gif'),
(33, '<lang:mood_bummed>', 'images/mood/{lang}/Bummed.gif'),
(34, '<lang:mood_busy>', 'images/mood/{lang}/Busy.gif'),
(35, '<lang:mood_buzzed>', 'images/mood/{lang}/Buzzed.gif'),
(36, '<lang:mood_champion>', 'images/mood/{lang}/Champion.gif'),
(37, '<lang:mood_chatty>', 'images/mood/{lang}/Chatty.gif'),
(38, '<lang:mood_cheeky>', 'images/mood/{lang}/Cheeky.gif'),
(39, '<lang:mood_cheerful>', 'images/mood/{lang}/Cheerful.gif'),
(40, '<lang:mood_cloud_9>', 'images/mood/{lang}/Cloud_9.gif'),
(41, '<lang:mood_clumsy>', 'images/mood/{lang}/Clumsy.gif'),
(42, '<lang:mood_coffee>', 'images/mood/{lang}/Coffee.gif'),
(43, '<lang:mood_cold>', 'images/mood/{lang}/Cold.gif'),
(44, '<lang:mood_confused>', 'images/mood/{lang}/Confused.gif'),
(45, '<lang:mood_cool>', 'images/mood/{lang}/Cool.gif'),
(46, '<lang:mood_crackhead>', 'images/mood/{lang}/Crackhead.gif'),
(47, '<lang:mood_crappy>', 'images/mood/{lang}/Crappy.gif'),
(48, '<lang:mood_crazy>', 'images/mood/{lang}/Crazy.gif'),
(49, '<lang:mood_creative>', 'images/mood/{lang}/Creative.gif'),
(50, '<lang:mood_curmudgeon>', 'images/mood/{lang}/Curmudgeon.gif'),
(51, '<lang:mood_cynical>', 'images/mood/{lang}/Cynical.gif'),
(52, '<lang:mood_daring>', 'images/mood/{lang}/Daring.gif'),
(53, '<lang:mood_dead>', 'images/mood/{lang}/Dead.gif'),
(54, '<lang:mood_depressed>', 'images/mood/{lang}/Depressed.gif'),
(55, '<lang:mood_devilish>', 'images/mood/{lang}/Devilish.gif'),
(56, '<lang:mood_devious>', 'images/mood/{lang}/Devious.gif'),
(57, '<lang:mood_disgusted>', 'images/mood/{lang}/Disgusted.gif'),
(58, '<lang:mood_disapprove>', 'images/mood/{lang}/Disapprove.gif'),
(59, '<lang:mood_doh>', 'images/mood/{lang}/Doh.gif'),
(60, '<lang:mood_doubtful>', 'images/mood/{lang}/Doubtful.gif'),
(61, '<lang:mood_doulay>', 'images/mood/{lang}/Doulay.gif'),
(62, '<lang:mood_dreamy>', 'images/mood/{lang}/Dreamy.gif'),
(63, '<lang:mood_drunk>', 'images/mood/{lang}/Drunk.gif'),
(64, '<lang:mood_dunce>', 'images/mood/{lang}/Dunce.gif'),
(65, '<lang:mood_dunno>', 'images/mood/{lang}/Dunno.gif'),
(66, '<lang:mood_embarrassed>', 'images/mood/{lang}/Embarrassed.gif'),
(67, '<lang:mood_empty>', 'images/mood/{lang}/Empty.gif'),
(68, '<lang:mood_epic>', 'images/mood/{lang}/Epic.gif'),
(69, '<lang:mood_erotic>', 'images/mood/{lang}/Erotic.gif'),
(70, '<lang:mood_errrrr>', 'images/mood/{lang}/Errrrr.gif'),
(71, '<lang:mood_evil>', 'images/mood/{lang}/Evil.gif'),
(72, '<lang:mood_excited>', 'images/mood/{lang}/Excited.gif'),
(73, '<lang:mood_exhausted>', 'images/mood/{lang}/Exhausted.gif'),
(74, '<lang:mood_fast>', 'images/mood/{lang}/Fast.gif'),
(75, '<lang:mood_festive>', 'images/mood/{lang}/Festive.gif'),
(76, '<lang:mood_fine>', 'images/mood/{lang}/Fine.gif'),
(77, '<lang:mood_flirty>', 'images/mood/{lang}/Flirty.gif'),
(78, '<lang:mood_flowery>', 'images/mood/{lang}/Flowery.gif'),
(79, '<lang:mood_footmouth>', 'images/mood/{lang}/FootMouth.gif'),
(80, '<lang:mood_forgetful>', 'images/mood/{lang}/Forgetful.gif'),
(81, '<lang:mood_friendly>', 'images/mood/{lang}/Friendly.gif'),
(82, '<lang:mood_gay>', 'images/mood/{lang}/Gay.gif'),
(83, '<lang:mood_geeky>', 'images/mood/{lang}/Geeky.gif'),
(84, '<lang:mood_gleeful>', 'images/mood/{lang}/Gleeful.gif'),
(85, '<lang:mood_gloomy>', 'images/mood/{lang}/Gloomy.gif'),
(86, '<lang:mood_goofy>', 'images/mood/{lang}/Goofy.gif'),
(87, '<lang:mood_grateful>', 'images/mood/{lang}/Grateful.gif'),
(88, '<lang:mood_greedy>', 'images/mood/{lang}/Greedy.gif'),
(89, '<lang:mood_grumpy>', 'images/mood/{lang}/Grumpy.gif'),
(90, '<lang:mood_hacker>', 'images/mood/{lang}/Hacker.gif'),
(91, '<lang:mood_haha>', 'images/mood/{lang}/HaHa.gif'),
(92, '<lang:mood_happy>', 'images/mood/{lang}/Happy.gif'),
(93, '<lang:mood_heartbroken>', 'images/mood/{lang}/Heartbroken.gif'),
(94, '<lang:mood_helpful>', 'images/mood/{lang}/Helpful.gif'),
(95, '<lang:mood_hmm>', 'images/mood/{lang}/Hmm.gif'),
(96, '<lang:mood_homesick>', 'images/mood/{lang}/Homesick.gif'),
(97, '<lang:mood_hopeful>', 'images/mood/{lang}/Hopeful.gif'),
(98, '<lang:mood_hoppy>', 'images/mood/{lang}/Hoppy.gif'),
(99, '<lang:mood_hormonal>', 'images/mood/{lang}/Hormonal.gif'),
(100, '<lang:mood_horny>', 'images/mood/{lang}/Horny.gif'),
(101, '<lang:mood_hot>', 'images/mood/{lang}/Hot.gif'),
(102, '<lang:mood_hotflash>', 'images/mood/{lang}/Hotflash.gif'),
(103, '<lang:mood_huggable>', 'images/mood/{lang}/Huggable.gif'),
(104, '<lang:mood_hungover>', 'images/mood/{lang}/Hungover.gif'),
(105, '<lang:mood_hurt>', 'images/mood/{lang}/Hurt.gif'),
(106, '<lang:mood_hyper>', 'images/mood/{lang}/Hyper.gif'),
(107, '<lang:mood_infatuated>', 'images/mood/{lang}/Infatuated.gif'),
(108, '<lang:mood_injured>', 'images/mood/{lang}/Injured.gif'),
(109, '<lang:mood_inlove>', 'images/mood/{lang}/InLove.gif'),
(110, '<lang:mood_innocent>', 'images/mood/{lang}/Innocent.gif'),
(111, '<lang:mood_inpain>', 'images/mood/{lang}/InPain.gif'),
(112, '<lang:mood_inspired>', 'images/mood/{lang}/Inspired.gif'),
(113, '<lang:mood_invisible>', 'images/mood/{lang}/Invisible.gif'),
(114, '<lang:mood_irritated>', 'images/mood/{lang}/Irritated.gif'),
(115, '<lang:mood_joyful>', 'images/mood/{lang}/Joyful.gif'),
(116, '<lang:mood_jubilant>', 'images/mood/{lang}/Jubilant.gif'),
(117, '<lang:mood_lazy>', 'images/mood/{lang}/Lazy.gif'),
(118, '<lang:mood_lonely>', 'images/mood/{lang}/Lonely.gif'),
(119, '<lang:mood_loopified>', 'images/mood/{lang}/Loopified.gif'),
(120, '<lang:mood_lurking>', 'images/mood/{lang}/Lurking.gif'),
(121, '<lang:mood_mad>', 'images/mood/{lang}/Mad.gif'),
(122, '<lang:mood_mellow>', 'images/mood/{lang}/Mellow.gif'),
(123, '<lang:mood_melodious>', 'images/mood/{lang}/Melodious.gif'),
(124, '<lang:mood_musical>', 'images/mood/{lang}/Musical.gif'),
(125, '<lang:mood_nerdy>', 'images/mood/{lang}/Nerdy.gif'),
(126, '<lang:mood_optimistic>', 'images/mood/{lang}/Optimistic.gif'),
(127, '<lang:mood_ornery>', 'images/mood/{lang}/Ornery.gif'),
(128, '<lang:mood_paranoid>', 'images/mood/{lang}/Paranoid.gif'),
(129, '<lang:mood_patriotic>', 'images/mood/{lang}/Patriotic.gif'),
(130, '<lang:mood_peaceful>', 'images/mood/{lang}/Peaceful.gif'),
(131, '<lang:mood_peachy>', 'images/mood/{lang}/Peachy.gif'),
(132, '<lang:mood_pensive>', 'images/mood/{lang}/Pensive.gif'),
(133, '<lang:mood_persnickety>', 'images/mood/{lang}/Persnickety.gif'),
(134, '<lang:mood_pessimistic>', 'images/mood/{lang}/Pessimistic.gif'),
(135, '<lang:mood_pimpin>', 'images/mood/{lang}/Pimpin.gif'),
(136, '<lang:mood_pissedoff>', 'images/mood/{lang}/PissedOff.gif'),
(137, '<lang:mood_pity>', 'images/mood/{lang}/Pity.gif'),
(138, '<lang:mood_praising>', 'images/mood/{lang}/Praising.gif'),
(139, '<lang:mood_pregnant>', 'images/mood/{lang}/Pregnant.gif'),
(140, '<lang:mood_prepared>', 'images/mood/{lang}/Prepared.gif'),
(141, '<lang:mood_pretty>', 'images/mood/{lang}/Pretty.gif'),
(142, '<lang:mood_productive>', 'images/mood/{lang}/Productive.gif'),
(143, '<lang:mood_proud>', 'images/mood/{lang}/Proud.gif'),
(144, '<lang:mood_psychadelic>', 'images/mood/{lang}/Psychadelic.gif'),
(145, '<lang:mood_question>', 'images/mood/{lang}/Question.gif'),
(146, '<lang:mood_rainy>', 'images/mood/{lang}/Rainy.gif'),
(147, '<lang:mood_rejected>', 'images/mood/{lang}/Rejected.gif'),
(148, '<lang:mood_relaxed>', 'images/mood/{lang}/Relaxed.gif'),
(149, '<lang:mood_roflol>', 'images/mood/{lang}/Roflol.gif'),
(150, '<lang:mood_sad>', 'images/mood/{lang}/Sad.gif'),
(151, '<lang:mood_sarcastic>', 'images/mood/{lang}/Sarcastic.gif'),
(152, '<lang:mood_sassy>', 'images/mood/{lang}/Sassy.gif'),
(153, '<lang:mood_satisfied>', 'images/mood/{lang}/Satisfied.gif'),
(154, '<lang:mood_saywhat>', 'images/mood/{lang}/SayWhat.gif'),
(155, '<lang:mood_scared>', 'images/mood/{lang}/Scared.gif'),
(156, '<lang:mood_scooted>', 'images/mood/{lang}/Scooted.gif'),
(157, '<lang:mood_shh>', 'images/mood/{lang}/Shh.gif'),
(158, '<lang:mood_shocked>', 'images/mood/{lang}/Shocked.gif'),
(159, '<lang:mood_shredding>', 'images/mood/{lang}/Shredding.gif'),
(160, '<lang:mood_sick>', 'images/mood/{lang}/Sick.gif'),
(161, '<lang:mood_silly>', 'images/mood/{lang}/Silly.gif'),
(162, '<lang:mood_slapfight>', 'images/mood/{lang}/Slapfight.gif'),
(163, '<lang:mood_sleepy>', 'images/mood/{lang}/Sleepy.gif'),
(164, '<lang:mood_slow>', 'images/mood/{lang}/Slow.gif'),
(165, '<lang:mood_smokin>', 'images/mood/{lang}/Smokin.gif'),
(166, '<lang:mood_snappy>', 'images/mood/{lang}/Snappy.gif'),
(167, '<lang:mood_sneaky>', 'images/mood/{lang}/Sneaky.gif'),
(168, '<lang:mood_snobbery>', 'images/mood/{lang}/Snobbery.gif'),
(169, '<lang:mood_spammy>', 'images/mood/{lang}/Spammy.gif'),
(170, '<lang:mood_speechless>', 'images/mood/{lang}/Speechless.gif'),
(171, '<lang:mood_spoiled>', 'images/mood/{lang}/Spoiled.gif'),
(172, '<lang:mood_starving>', 'images/mood/{lang}/Starving.gif'),
(173, '<lang:mood_stoned>', 'images/mood/{lang}/Stoned.gif'),
(174, '<lang:mood_stressed>', 'images/mood/{lang}/Stressed.gif'),
(175, '<lang:mood_stuck>', 'images/mood/{lang}/Stuck.gif'),
(176, '<lang:mood_studly>', 'images/mood/{lang}/Studly.gif'),
(177, '<lang:mood_stunned>', 'images/mood/{lang}/Stunned.gif'),
(178, '<lang:mood_sunshine>', 'images/mood/{lang}/Sunshine.gif'),
(179, '<lang:mood_suspicious>', 'images/mood/{lang}/Suspicious.gif'),
(180, '<lang:mood_sweet>', 'images/mood/{lang}/Sweet.gif'),
(181, '<lang:mood_talkative>', 'images/mood/{lang}/Talkative.gif'),
(182, '<lang:mood_tell>', 'images/mood/{lang}/Tell.gif'),
(183, '<lang:mood_thinking>', 'images/mood/{lang}/Thinking.gif'),
(184, '<lang:mood_tired>', 'images/mood/{lang}/Tired.gif'),
(185, '<lang:mood_tolerant>', 'images/mood/{lang}/Tolerant.gif'),
(186, '<lang:mood_tonguetied>', 'images/mood/{lang}/TongueTied.gif'),
(187, '<lang:mood_torn>', 'images/mood/{lang}/Torn.gif'),
(188, '<lang:mood_twisted>', 'images/mood/{lang}/Twisted.gif'),
(189, '<lang:mood_ugly>', 'images/mood/{lang}/Ugly.gif'),
(190, '<lang:mood_vilified>', 'images/mood/{lang}/Vilified.gif'),
(191, '<lang:mood_volatile>', 'images/mood/{lang}/Volatile.gif'),
(192, '<lang:mood_wasted>', 'images/mood/{lang}/Wasted.gif'),
(193, '<lang:mood_what>', 'images/mood/{lang}/What.gif'),
(194, '<lang:mood_where>', 'images/mood/{lang}/Where.gif'),
(195, '<lang:mood_wicked>', 'images/mood/{lang}/Wicked.gif'),
(196, '<lang:mood_wild>', 'images/mood/{lang}/Wild.gif'),
(197, '<lang:mood_woot>', 'images/mood/{lang}/Woot.gif'),
(198, '<lang:mood_worried>', 'images/mood/{lang}/Worried.gif'),
(199, '<lang:mood_yeehaw>', 'images/mood/{lang}/Yeehaw.gif'),
(200, '<lang:mood_zombie>', 'images/mood/{lang}/Zombie.gif')");

	update_moods();
}

// Checks to make sure plugin is installed.
function moodmanager_is_installed()
{
	global $db;
	if($db->field_exists("mood", "users"))
	{
		return true;
	}
	return false;
}

// This function runs when the plugin is uninstalled.
function moodmanager_uninstall()
{
	global $db;

	if($db->table_exists("moods"))
	{
		$db->drop_table("moods");
	}

	if($db->field_exists("mood", "users"))
	{
		$db->drop_column("users", "mood");
	}

	$db->delete_query("datacache", "title IN('moods')");
}

// This function runs when the plugin is activated.
function moodmanager_activate()
{
	global $db;
	update_moods();

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
		'template'	=> $db->escape_string('<div class="modal">
<div style="overflow-y: auto; max-height: 400px;" class="modal_{$mybb->user[\'uid\']}">
<form action="mood.php" method="post" class="moodclass_{$mybb->user[\'uid\']}" onsubmit="javascript: return Mood.submitMood({$mybb->user[\'uid\']});">
<input type="hidden" name="my_post_key" value="{$mybb->post_code}" />
<input type="hidden" name="action" value="do_change" />
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
<input type="submit" class="button" value="{$lang->change_mood}" />
</td>
</tr>
</table>
</form>
</div>
</div>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'mood_updated',
		'template'	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="trow1" style="padding: 20px">
<strong>{$lang->mood_updated}</strong><br /><br />
<blockquote>{$lang->mood_updated_message}</blockquote>
</td>
</tr>
</table>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}<br />{$post[\'usermood\']}');
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}<br />{$post[\'usermood\']}');
	find_replace_templatesets("member_profile", "#".preg_quote('{$online_status}')."#i", '{$online_status}<br /><strong>{$lang->mood}:</strong> {$mood}');
	find_replace_templatesets("header_welcomeblock_member", "#".preg_quote('<ul class="menu user_links">')."#i", '<ul class="menu user_links">{$moodlink}');
	find_replace_templatesets("headerinclude", "#".preg_quote('{$stylesheets}')."#i", '<script type="text/javascript" src="{$mybb->asset_url}/jscripts/mood.js?ver=1800"></script>{$stylesheets}');
}

// This function runs when the plugin is deactivated.
function moodmanager_deactivate()
{
	global $db;
	$db->delete_query("templates", "title IN('postbit_mood','mood','mood_updated')");

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('<br />{$post[\'usermood\']}')."#i", '', 0);
	find_replace_templatesets("postbit_classic", "#".preg_quote('<br />{$post[\'usermood\']}')."#i", '', 0);
	find_replace_templatesets("member_profile", "#".preg_quote('<br /><strong>{$lang->mood}:</strong> {$mood}')."#i", '', 0);
	find_replace_templatesets("header_welcomeblock_member", "#".preg_quote('{$moodlink}')."#i", '', 0);
	find_replace_templatesets("headerinclude", "#".preg_quote('<script type="text/javascript" src="{$mybb->asset_url}/jscripts/mood.js?ver=1800"></script>')."#i", '', 0);
}

// Link to Mood pop-up
function moodmanager_link()
{
	global $mybb, $lang, $templates, $moodlink;
	$lang->load('mood');

	if($mybb->user['uid'])
	{
		$moodlink = "<li><strong><a href=\"javascript:;\" onclick=\"MyBB.popupWindow('/mood.php'); return false;\">{$lang->change_mood}</a></strong></li>";
	}
}

// Display Mood on Postbit
function moodmanager_postbit($post)
{
	global $db, $mybb, $lang, $templates, $cache;
	$lang->load("mood");
	$mood_cache = $cache->read("moods");

	if($post['mood'])
	{
		$post['mood'] = (int)$post['mood'];
		$currentmood = $mood_cache[$post['mood']];

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
			$mood = "<img src=\"{$englishpath}\" alt=\"{$currentmood['name']}\" title=\"{$currentmood['name']}\" />";
		}
		else
		{
			$mood = "<img src=\"{$path}\" alt=\"{$currentmood['name']}\" title=\"{$currentmood['name']}\" />";
		}
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
	global $db, $mybb, $lang, $memprofile, $mood, $cache;
	$lang->load("mood");
	$mood_cache = $cache->read("moods");

	if($memprofile['mood'])
	{
		$memprofile['mood'] = (int)$memprofile['mood'];
		$currentmood = $mood_cache[$memprofile['mood']];

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
			$mood = "<img src=\"{$englishpath}\" alt=\"{$currentmood['name']}\" title=\"{$currentmood['name']}\" />";
		}
		else
		{
			$mood = "<img src=\"{$path}\" alt=\"{$currentmood['name']}\" title=\"{$currentmood['name']}\" />";
		}
	}
	else
	{
		$mood = $lang->not_specified;
	}
}

// Add mood manage section in Admin CP
function moodmanager_admin_menu($sub_menu)
{
	global $lang;
	$lang->load("config_moods");

	$sub_menu['220'] = array('id' => 'moods', 'title' => $lang->moods, 'link' => 'index.php?module=config-moods');

	return $sub_menu;
}

function moodmanager_admin_action_handler($actions)
{
	$actions['moods'] = array('active' => 'moods', 'file' => 'moods.php');

	return $actions;
}

function moodmanager_admin_permissions($admin_permissions)
{
	global $db, $mybb, $lang;
	$lang->load("config_moods");

	$admin_permissions['moods'] = $lang->can_manage_moods;

	return $admin_permissions;
}

// Admin Log display
function moodmanager_admin_adminlog($plugin_array)
{
	global $lang;
	$lang->load("config_moods");

	return $plugin_array;
}

/**
 * Update the mood cache.
 *
 */
function update_moods()
{
	global $db, $cache;

	$moods = array();

	$query = $db->simple_select("moods", "mid, name, path");
	while($mood = $db->fetch_array($query))
	{
		$moods[$mood['mid']] = $mood;
	}

	$cache->update("moods", $moods);
}

?>