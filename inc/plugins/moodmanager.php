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
if(THIS_SCRIPT == 'showthread.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_mood,global_mood';
}

if(THIS_SCRIPT == 'private.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_mood,global_mood';
}

if(THIS_SCRIPT == 'announcements.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_mood,global_mood';
}

if(THIS_SCRIPT == 'newthread.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_mood,global_mood';
}

if(THIS_SCRIPT == 'newreply.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_mood,global_mood';
}

if(THIS_SCRIPT == 'editpost.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'postbit_mood,global_mood';
}

if(THIS_SCRIPT == 'member.php')
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'global_mood';
}

// Tell MyBB when to run the hooks
$plugins->add_hook("global_start", "moodmanager_link_cache");
$plugins->add_hook("global_intermediate", "moodmanager_link");
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
		"version"			=> "1.2",
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

	switch($db->type)
	{
		case "pgsql":
			$db->write_query("CREATE TABLE ".TABLE_PREFIX."moods (
				mid serial,
				name varchar(120) NOT NULL default '',
				path varchar(220) NOT NULL default '',
				PRIMARY KEY (mid)
			);");
			break;
		case "sqlite":
			$db->write_query("CREATE TABLE ".TABLE_PREFIX."moods (
				mid INTEGER PRIMARY KEY,
				name varchar(120) NOT NULL default '',
				path varchar(220) NOT NULL default ''
			);");
			break;
		default:
			$db->write_query("CREATE TABLE ".TABLE_PREFIX."moods (
				mid int unsigned NOT NULL auto_increment,
				name varchar(120) NOT NULL default '',
				path varchar(220) NOT NULL default '',
				PRIMARY KEY (mid)
			) ENGINE=MyISAM{$collation};");
			break;
	}

	switch($db->type)
	{
		case "pgsql":
			$db->add_column("users", "mood", "int NOT NULL default '0'");
			break;
		case "sqlite":
			$db->add_column("users", "mood", "int NOT NULL default '0'");
			break;
		default:
			$db->add_column("users", "mood", "int unsigned NOT NULL default '0'");
			break;
	}

	$db->write_query("INSERT INTO ".TABLE_PREFIX."moods (mid, name, path) VALUES
(1, '<lang:mood_addicted>', 'images/mood/{lang}/addicted.png'),
(2, '<lang:mood_adored>', 'images/mood/{lang}/adored.gif'),
(3, '<lang:mood_aggressive>', 'images/mood/{lang}/aggressive.png'),
(4, '<lang:mood_airheaded>', 'images/mood/{lang}/airheaded.png'),
(5, '<lang:mood_allshookup>', 'images/mood/{lang}/allshookup.png'),
(6, '<lang:mood_alone>', 'images/mood/{lang}/alone.png'),
(7, '<lang:mood_amazed>', 'images/mood/{lang}/amazed.png'),
(8, '<lang:mood_amused>', 'images/mood/{lang}/amused.png'),
(9, '<lang:mood_angelic>', 'images/mood/{lang}/angelic.png'),
(10, '<lang:mood_angry>', 'images/mood/{lang}/angry.png'),
(11, '<lang:mood_annoyed>', 'images/mood/{lang}/annoyed.png'),
(12, '<lang:mood_apathetic>', 'images/mood/{lang}/apathetic.png'),
(13, '<lang:mood_apprehensive>', 'images/mood/{lang}/apprehensive.png'),
(14, '<lang:mood_approved>', 'images/mood/{lang}/approved.png'),
(15, '<lang:mood_artistic>', 'images/mood/{lang}/artistic.png'),
(16, '<lang:mood_ashamed>', 'images/mood/{lang}/ashamed.png'),
(17, '<lang:mood_asleep>', 'images/mood/{lang}/asleep.png'),
(18, '<lang:mood_bahahaha>', 'images/mood/{lang}/bahahaha.gif'),
(19, '<lang:mood_bashful>', 'images/mood/{lang}/bashful.png'),
(20, '<lang:mood_bawling>', 'images/mood/{lang}/bawling.gif'),
(21, '<lang:mood_believing>', 'images/mood/{lang}/believing.png'),
(22, '<lang:mood_bewildered>', 'images/mood/{lang}/bewildered.png'),
(23, '<lang:mood_bitchy>', 'images/mood/{lang}/bitchy.gif'),
(24, '<lang:mood_blah>', 'images/mood/{lang}/blah.png'),
(25, '<lang:mood_blessed>', 'images/mood/{lang}/blessed.png'),
(26, '<lang:mood_bookworm>', 'images/mood/{lang}/bookworm.png'),
(27, '<lang:mood_bored>', 'images/mood/{lang}/bored.png'),
(28, '<lang:mood_brave>', 'images/mood/{lang}/brave.png'),
(29, '<lang:mood_breezy>', 'images/mood/{lang}/breezy.png'),
(30, '<lang:mood_broken>', 'images/mood/{lang}/broken.png'),
(31, '<lang:mood_brooding>', 'images/mood/{lang}/brooding.png'),
(32, '<lang:mood_bumbly>', 'images/mood/{lang}/bumbly.png'),
(33, '<lang:mood_bummed>', 'images/mood/{lang}/bummed.png'),
(34, '<lang:mood_busy>', 'images/mood/{lang}/busy.png'),
(35, '<lang:mood_buzzed>', 'images/mood/{lang}/buzzed.png'),
(36, '<lang:mood_champion>', 'images/mood/{lang}/champion.png'),
(37, '<lang:mood_chatty>', 'images/mood/{lang}/chatty.png'),
(38, '<lang:mood_cheeky>', 'images/mood/{lang}/cheeky.png'),
(39, '<lang:mood_cheerful>', 'images/mood/{lang}/cheerful.png'),
(40, '<lang:mood_cloud_9>', 'images/mood/{lang}/cloud9.png'),
(41, '<lang:mood_clumsy>', 'images/mood/{lang}/clumsy.png'),
(42, '<lang:mood_coffee>', 'images/mood/{lang}/coffee.png'),
(43, '<lang:mood_cold>', 'images/mood/{lang}/cold.png'),
(44, '<lang:mood_confused>', 'images/mood/{lang}/confused.png'),
(45, '<lang:mood_cool>', 'images/mood/{lang}/cool.png'),
(46, '<lang:mood_crackhead>', 'images/mood/{lang}/crackhead.png'),
(47, '<lang:mood_crappy>', 'images/mood/{lang}/crappy.png'),
(48, '<lang:mood_crazy>', 'images/mood/{lang}/crazy.png'),
(49, '<lang:mood_creative>', 'images/mood/{lang}/creative.png'),
(50, '<lang:mood_curmudgeon>', 'images/mood/{lang}/curmudgeon.png'),
(51, '<lang:mood_cynical>', 'images/mood/{lang}/cynical.png'),
(52, '<lang:mood_daring>', 'images/mood/{lang}/daring.png'),
(53, '<lang:mood_dead>', 'images/mood/{lang}/dead.png'),
(54, '<lang:mood_depressed>', 'images/mood/{lang}/depressed.png'),
(55, '<lang:mood_devilish>', 'images/mood/{lang}/devilish.png'),
(56, '<lang:mood_devious>', 'images/mood/{lang}/devious.gif'),
(57, '<lang:mood_disgusted>', 'images/mood/{lang}/disgusted.png'),
(58, '<lang:mood_disapprove>', 'images/mood/{lang}/disapprove.png'),
(59, '<lang:mood_doh>', 'images/mood/{lang}/doh.gif'),
(60, '<lang:mood_doubtful>', 'images/mood/{lang}/doubtful.png'),
(61, '<lang:mood_doulay>', 'images/mood/{lang}/doulay.png'),
(62, '<lang:mood_dreamy>', 'images/mood/{lang}/dreamy.png'),
(63, '<lang:mood_drunk>', 'images/mood/{lang}/drunk.png'),
(64, '<lang:mood_dunce>', 'images/mood/{lang}/dunce.png'),
(65, '<lang:mood_dunno>', 'images/mood/{lang}/dunno.gif'),
(66, '<lang:mood_embarrassed>', 'images/mood/{lang}/embarrassed.png'),
(67, '<lang:mood_empty>', 'images/mood/{lang}/empty.png'),
(68, '<lang:mood_epic>', 'images/mood/{lang}/epic.png'),
(69, '<lang:mood_erotic>', 'images/mood/{lang}/erotic.gif'),
(70, '<lang:mood_errrrr>', 'images/mood/{lang}/errrrr.gif'),
(71, '<lang:mood_evil>', 'images/mood/{lang}/evil.png'),
(72, '<lang:mood_excited>', 'images/mood/{lang}/excited.png'),
(73, '<lang:mood_exhausted>', 'images/mood/{lang}/exhausted.png'),
(74, '<lang:mood_fast>', 'images/mood/{lang}/fast.png'),
(75, '<lang:mood_festive>', 'images/mood/{lang}/festive.png'),
(76, '<lang:mood_fine>', 'images/mood/{lang}/fine.png'),
(77, '<lang:mood_flirty>', 'images/mood/{lang}/flirty.gif'),
(78, '<lang:mood_flowery>', 'images/mood/{lang}/flowery.gif'),
(79, '<lang:mood_footmouth>', 'images/mood/{lang}/footmouth.png'),
(80, '<lang:mood_forgetful>', 'images/mood/{lang}/forgetful.gif'),
(81, '<lang:mood_friendly>', 'images/mood/{lang}/friendly.png'),
(82, '<lang:mood_gay>', 'images/mood/{lang}/gay.png'),
(83, '<lang:mood_geeky>', 'images/mood/{lang}/geeky.gif'),
(84, '<lang:mood_gleeful>', 'images/mood/{lang}/gleeful.png'),
(85, '<lang:mood_gloomy>', 'images/mood/{lang}/gloomy.gif'),
(86, '<lang:mood_goofy>', 'images/mood/{lang}/goofy.png'),
(87, '<lang:mood_grateful>', 'images/mood/{lang}/grateful.png'),
(88, '<lang:mood_greedy>', 'images/mood/{lang}/greedy.png'),
(89, '<lang:mood_grumpy>', 'images/mood/{lang}/grumpy.png'),
(90, '<lang:mood_hacker>', 'images/mood/{lang}/hacker.gif'),
(91, '<lang:mood_haha>', 'images/mood/{lang}/haha.png'),
(92, '<lang:mood_happy>', 'images/mood/{lang}/happy.png'),
(93, '<lang:mood_heartbroken>', 'images/mood/{lang}/heartbroken.png'),
(94, '<lang:mood_helpful>', 'images/mood/{lang}/helpful.png'),
(95, '<lang:mood_hmm>', 'images/mood/{lang}/hmm.png'),
(96, '<lang:mood_homesick>', 'images/mood/{lang}/homesick.gif'),
(97, '<lang:mood_hopeful>', 'images/mood/{lang}/hopeful.gif'),
(98, '<lang:mood_hoppy>', 'images/mood/{lang}/hoppy.png'),
(99, '<lang:mood_hormonal>', 'images/mood/{lang}/hormonal.png'),
(100, '<lang:mood_horny>', 'images/mood/{lang}/horny.png'),
(101, '<lang:mood_hot>', 'images/mood/{lang}/hot.png'),
(102, '<lang:mood_hotflash>', 'images/mood/{lang}/hotflash.png'),
(103, '<lang:mood_huggable>', 'images/mood/{lang}/huggable.png'),
(104, '<lang:mood_hungover>', 'images/mood/{lang}/hungover.png'),
(105, '<lang:mood_hurt>', 'images/mood/{lang}/hurt.png'),
(106, '<lang:mood_hyper>', 'images/mood/{lang}/hyper.png'),
(107, '<lang:mood_infatuated>', 'images/mood/{lang}/infatuated.gif'),
(108, '<lang:mood_injured>', 'images/mood/{lang}/injured.png'),
(109, '<lang:mood_inlove>', 'images/mood/{lang}/inlove.png'),
(110, '<lang:mood_innocent>', 'images/mood/{lang}/innocent.png'),
(111, '<lang:mood_inpain>', 'images/mood/{lang}/inpain.gif'),
(112, '<lang:mood_inspired>', 'images/mood/{lang}/inspired.png'),
(113, '<lang:mood_invisible>', 'images/mood/{lang}/invisible.gif'),
(114, '<lang:mood_irritated>', 'images/mood/{lang}/irritated.png'),
(115, '<lang:mood_joyful>', 'images/mood/{lang}/joyful.png'),
(116, '<lang:mood_jubilant>', 'images/mood/{lang}/jubilant.gif'),
(117, '<lang:mood_lazy>', 'images/mood/{lang}/lazy.png'),
(118, '<lang:mood_lonely>', 'images/mood/{lang}/lonely.png'),
(119, '<lang:mood_loopified>', 'images/mood/{lang}/loopified.png'),
(120, '<lang:mood_lurking>', 'images/mood/{lang}/lurking.png'),
(121, '<lang:mood_mad>', 'images/mood/{lang}/mad.png'),
(122, '<lang:mood_mellow>', 'images/mood/{lang}/mellow.png'),
(123, '<lang:mood_melodious>', 'images/mood/{lang}/melodious.png'),
(124, '<lang:mood_musical>', 'images/mood/{lang}/musical.png'),
(125, '<lang:mood_nerdy>', 'images/mood/{lang}/nerdy.gif'),
(126, '<lang:mood_optimistic>', 'images/mood/{lang}/optimistic.png'),
(127, '<lang:mood_ornery>', 'images/mood/{lang}/ornery.png'),
(128, '<lang:mood_paranoid>', 'images/mood/{lang}/paranoid.png'),
(129, '<lang:mood_patriotic>', 'images/mood/{lang}/patriotic.gif'),
(130, '<lang:mood_peaceful>', 'images/mood/{lang}/peaceful.png'),
(131, '<lang:mood_peachy>', 'images/mood/{lang}/peachy.png'),
(132, '<lang:mood_pensive>', 'images/mood/{lang}/pensive.png'),
(133, '<lang:mood_persnickety>', 'images/mood/{lang}/persnickety.png'),
(134, '<lang:mood_pessimistic>', 'images/mood/{lang}/pessimistic.png'),
(135, '<lang:mood_pimpin>', 'images/mood/{lang}/pimpin.png'),
(136, '<lang:mood_pissedoff>', 'images/mood/{lang}/pissedoff.png'),
(137, '<lang:mood_pity>', 'images/mood/{lang}/pity.png'),
(138, '<lang:mood_praising>', 'images/mood/{lang}/praising.gif'),
(139, '<lang:mood_pregnant>', 'images/mood/{lang}/pregnant.png'),
(140, '<lang:mood_prepared>', 'images/mood/{lang}/prepared.png'),
(141, '<lang:mood_pretty>', 'images/mood/{lang}/pretty.png'),
(142, '<lang:mood_productive>', 'images/mood/{lang}/productive.gif'),
(143, '<lang:mood_proud>', 'images/mood/{lang}/proud.png'),
(144, '<lang:mood_psychadelic>', 'images/mood/{lang}/psychadelic.png'),
(145, '<lang:mood_question>', 'images/mood/{lang}/question.png'),
(146, '<lang:mood_rainy>', 'images/mood/{lang}/rainy.png'),
(147, '<lang:mood_rejected>', 'images/mood/{lang}/rejected.png'),
(148, '<lang:mood_relaxed>', 'images/mood/{lang}/relaxed.png'),
(149, '<lang:mood_roflol>', 'images/mood/{lang}/roflol.gif'),
(150, '<lang:mood_sad>', 'images/mood/{lang}/sad.png'),
(151, '<lang:mood_sarcastic>', 'images/mood/{lang}/sarcastic.png'),
(152, '<lang:mood_sassy>', 'images/mood/{lang}/sassy.png'),
(153, '<lang:mood_satisfied>', 'images/mood/{lang}/satisfied.png'),
(154, '<lang:mood_saywhat>', 'images/mood/{lang}/saywhat.gif'),
(155, '<lang:mood_scared>', 'images/mood/{lang}/scared.png'),
(156, '<lang:mood_scooted>', 'images/mood/{lang}/scooted.png'),
(157, '<lang:mood_shh>', 'images/mood/{lang}/shh.png'),
(158, '<lang:mood_shocked>', 'images/mood/{lang}/shocked.png'),
(159, '<lang:mood_shredding>', 'images/mood/{lang}/shredding.png'),
(160, '<lang:mood_sick>', 'images/mood/{lang}/sick.png'),
(161, '<lang:mood_silly>', 'images/mood/{lang}/silly.gif'),
(162, '<lang:mood_slapfight>', 'images/mood/{lang}/slapfight.png'),
(163, '<lang:mood_sleepy>', 'images/mood/{lang}/sleepy.png'),
(164, '<lang:mood_slow>', 'images/mood/{lang}/slow.png'),
(165, '<lang:mood_smokin>', 'images/mood/{lang}/smokin.png'),
(166, '<lang:mood_snappy>', 'images/mood/{lang}/snappy.png'),
(167, '<lang:mood_sneaky>', 'images/mood/{lang}/sneaky.png'),
(168, '<lang:mood_snobbery>', 'images/mood/{lang}/snobbery.gif'),
(169, '<lang:mood_spammy>', 'images/mood/{lang}/spammy.png'),
(170, '<lang:mood_speechless>', 'images/mood/{lang}/speechless.png'),
(171, '<lang:mood_spoiled>', 'images/mood/{lang}/spoiled.png'),
(172, '<lang:mood_starving>', 'images/mood/{lang}/starving.png'),
(173, '<lang:mood_stoned>', 'images/mood/{lang}/stoned.png'),
(174, '<lang:mood_stressed>', 'images/mood/{lang}/stressed.png'),
(175, '<lang:mood_stuck>', 'images/mood/{lang}/stuck.gif'),
(176, '<lang:mood_studly>', 'images/mood/{lang}/studly.png'),
(177, '<lang:mood_stunned>', 'images/mood/{lang}/stunned.png'),
(178, '<lang:mood_sunshine>', 'images/mood/{lang}/sunshine.png'),
(179, '<lang:mood_suspicious>', 'images/mood/{lang}/suspicious.png'),
(180, '<lang:mood_sweet>', 'images/mood/{lang}/sweet.png'),
(181, '<lang:mood_talkative>', 'images/mood/{lang}/talkative.gif'),
(182, '<lang:mood_tell>', 'images/mood/{lang}/tell.png'),
(183, '<lang:mood_thinking>', 'images/mood/{lang}/thinking.gif'),
(184, '<lang:mood_tired>', 'images/mood/{lang}/tired.png'),
(185, '<lang:mood_tolerant>', 'images/mood/{lang}/tolerant.png'),
(186, '<lang:mood_tonguetied>', 'images/mood/{lang}/tonguetied.png'),
(187, '<lang:mood_torn>', 'images/mood/{lang}/torn.png'),
(188, '<lang:mood_twisted>', 'images/mood/{lang}/twisted.png'),
(189, '<lang:mood_ugly>', 'images/mood/{lang}/ugly.png'),
(190, '<lang:mood_vilified>', 'images/mood/{lang}/vilified.png'),
(191, '<lang:mood_volatile>', 'images/mood/{lang}/volatile.png'),
(192, '<lang:mood_wasted>', 'images/mood/{lang}/wasted.png'),
(193, '<lang:mood_what>', 'images/mood/{lang}/what.gif'),
(194, '<lang:mood_where>', 'images/mood/{lang}/where.png'),
(195, '<lang:mood_wicked>', 'images/mood/{lang}/wicked.png'),
(196, '<lang:mood_wild>', 'images/mood/{lang}/wild.png'),
(197, '<lang:mood_woot>', 'images/mood/{lang}/woot.png'),
(198, '<lang:mood_worried>', 'images/mood/{lang}/worried.png'),
(199, '<lang:mood_yeehaw>', 'images/mood/{lang}/yeehaw.png'),
(200, '<lang:mood_zombie>', 'images/mood/{lang}/zombie.png')");

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
	global $db, $cache;

	if($db->table_exists("moods"))
	{
		$db->drop_table("moods");
	}

	if($db->field_exists("mood", "users"))
	{
		$db->drop_column("users", "mood");
	}

	$cache->delete('moods');
}

// This function runs when the plugin is activated.
function moodmanager_activate()
{
	global $db;
	update_moods();

	$insert_array = array(
		'title'		=> 'postbit_mood',
		'template'	=> $db->escape_string('<br />{$lang->mood}: {$mood}'),
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
		'title'		=> 'mood_option',
		'template'	=> $db->escape_string('<option value="{$mood[\'mid\']}"{$selected}>{$mood[\'name\']}</option>'),
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

	$insert_array = array(
		'title'		=> 'header_moodlink',
		'template'	=> $db->escape_string('<li><strong><a href="javascript:void(0)" onclick="MyBB.popupWindow(\'/mood.php\'); return false;">{$lang->change_mood}</a></strong></li>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	$insert_array = array(
		'title'		=> 'global_mood',
		'template'	=> $db->escape_string('<img src="{$currentmood[\'path\']}" alt="{$currentmood[\'name\']}" title="{$currentmood[\'name\']}" />'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'usermood\']}');
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'user_details\']}')."#i", '{$post[\'user_details\']}{$post[\'usermood\']}');
	find_replace_templatesets("member_profile", "#".preg_quote('{$online_status}')."#i", '{$online_status}<br /><strong>{$lang->mood}:</strong> {$mood}');
	find_replace_templatesets("header_welcomeblock_member", "#".preg_quote('<ul class="menu user_links">')."#i", '<ul class="menu user_links">{$moodlink}');
	find_replace_templatesets("headerinclude", "#".preg_quote('{$stylesheets}')."#i", '<script type="text/javascript" src="{$mybb->asset_url}/jscripts/mood.js?ver=1800"></script>{$stylesheets}');
}

// This function runs when the plugin is deactivated.
function moodmanager_deactivate()
{
	global $db;
	$db->delete_query("templates", "title IN('postbit_mood','mood','mood_option','mood_updated','header_moodlink','global_mood')");

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("postbit", "#".preg_quote('{$post[\'usermood\']}')."#i", '', 0);
	find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'usermood\']}')."#i", '', 0);
	find_replace_templatesets("member_profile", "#".preg_quote('<br /><strong>{$lang->mood}:</strong> {$mood}')."#i", '', 0);
	find_replace_templatesets("header_welcomeblock_member", "#".preg_quote('{$moodlink}')."#i", '', 0);
	find_replace_templatesets("headerinclude", "#".preg_quote('<script type="text/javascript" src="{$mybb->asset_url}/jscripts/mood.js?ver=1800"></script>')."#i", '', 0);
}

// Cache the header mood template
function moodmanager_link_cache()
{
	global $templatelist;
	if(isset($templatelist))
	{
		$templatelist .= ',';
	}
	$templatelist .= 'header_moodlink';
}

// Link to Mood pop-up
function moodmanager_link()
{
	global $mybb, $lang, $templates, $moodlink;
	$lang->load('mood');

	if($mybb->user['uid'])
	{
		eval("\$moodlink = \"".$templates->get("header_moodlink")."\";");
	}
}

// Display Mood on Postbit
function moodmanager_postbit($post)
{
	global $mybb, $lang, $templates, $cache;
	$lang->load("mood");
	$mood_cache = $cache->read("moods");

	if(!empty($post['mood']))
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

		eval("\$mood = \"".$templates->get("global_mood")."\";");
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
	global $mybb, $lang, $templates, $memprofile, $mood, $cache;
	$lang->load("mood");
	$mood_cache = $cache->read("moods");

	if(!empty($memprofile['mood']))
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

		eval("\$mood = \"".$templates->get("global_mood")."\";");
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
	global $lang;
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

	$query = $db->simple_select("moods", "mid, name, path", "", array('order_by' => 'name', 'order_dir' => 'asc'));
	while($mood = $db->fetch_array($query))
	{
		$moods[$mood['mid']] = $mood;
	}

	$cache->update("moods", $moods);
}
