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

$page->add_breadcrumb_item($lang->moods, "index.php?module=config-moods");

$lang->load("mood", true);

if($mybb->input['action'] == "add")
{
	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}

		if(!trim($mybb->input['path']))
		{
			$errors[] = $lang->error_missing_path;
		}

		if(!$errors)
		{
			$new_mood = array(
				'name' => $db->escape_string($mybb->input['name']),
				'path' => $db->escape_string($mybb->input['path'])
			);

			$mid = $db->insert_query("moods", $new_mood);

			update_moods();

			// Log admin action
			$name = $lang->parse($mybb->input['name']);
			log_admin_action($mid, $name);

			flash_message($lang->success_mood_added, 'success');
			admin_redirect('index.php?module=config-moods');
		}
	}

	$page->add_breadcrumb_item($lang->add_mood);
	$page->output_header($lang->moods." - ".$lang->add_mood);

	$sub_tabs['manage_moods'] = array(
		'title'	=> $lang->manage_moods,
		'link' => "index.php?module=config-moods"
	);

	$sub_tabs['add_mood'] = array(
		'title'	=> $lang->add_mood,
		'link' => "index.php?module=config-moods&amp;action=add",
		'description' => $lang->add_mood_desc
	);

	$page->output_nav_tabs($sub_tabs, 'add_mood');

	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input['path'] = 'images/mood/{lang}';
	}

	$form = new Form("index.php?module=config-moods&amp;action=add", "post", "add");
	$form_container = new FormContainer($lang->add_mood);
	$form_container->output_row($lang->name." <em>*</em>", $lang->name_desc, $form->generate_text_box('name', $mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->image_path." <em>*</em>", $lang->image_path_desc, $form->generate_text_box('path', $mybb->input['path'], array('id' => 'path')), 'path');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->save_mood);

	$form->output_submit_wrapper($buttons);

	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "edit")
{
	$query = $db->simple_select("moods", "*", "mid='".(int)$mybb->input['mid']."'");
	$mood = $db->fetch_array($query);

	if(!$mood['mid'])
	{
		flash_message($lang->error_invalid_mood, 'error');
		admin_redirect("index.php?module=config-moods");
	}

	if($mybb->request_method == "post")
	{
		if(!trim($mybb->input['name']))
		{
			$errors[] = $lang->error_missing_name;
		}

		if(!trim($mybb->input['path']))
		{
			$errors[] = $lang->error_missing_path;
		}

		if(!$errors)
		{
			$mood = array(
				'name'	=> $db->escape_string($mybb->input['name']),
				'path'	=> $db->escape_string($mybb->input['path'])
			);

			$db->update_query("moods", $mood, "mid='".(int)$mybb->input['mid']."'");

			update_moods();

			// Log admin action
			$name = $lang->parse($mybb->input['name']);
			log_admin_action($mybb->input['mid'], $name);

			flash_message($lang->success_mood_updated, 'success');
			admin_redirect('index.php?module=config-moods');
		}
	}

	$page->add_breadcrumb_item($lang->edit_mood);
	$page->output_header($lang->moods." - ".$lang->edit_mood);

	$sub_tabs['edit_mood'] = array(
		'title'	=> $lang->edit_mood,
		'link'	=> "index.php?module=config-moods",
		'description'	=> $lang->edit_mood_desc
	);

	$page->output_nav_tabs($sub_tabs, 'edit_mood');

	$form = new Form("index.php?module=config-moods&amp;action=edit", "post", "edit");
	echo $form->generate_hidden_field("mid", $mood['mid']);

	if($errors)
	{
		$page->output_inline_error($errors);
	}
	else
	{
		$mybb->input = $mood;
	}

	$form_container = new FormContainer($lang->edit_mood);
	$form_container->output_row($lang->name." <em>*</em>", $lang->name_desc, $form->generate_text_box('name', $mybb->input['name'], array('id' => 'name')), 'name');
	$form_container->output_row($lang->image_path." <em>*</em>", $lang->image_path_desc, $form->generate_text_box('path', $mybb->input['path'], array('id' => 'path')), 'path');
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->save_mood);

	$form->output_submit_wrapper($buttons);
	$form->end();

	$page->output_footer();
}

if($mybb->input['action'] == "delete")
{
	$query = $db->simple_select("moods", "*", "mid='".(int)$mybb->input['mid']."'");
	$mood = $db->fetch_array($query);

	if(!$mood['mid'])
	{
		flash_message($lang->error_invalid_post_mood, 'error');
		admin_redirect("index.php?module=config-moods");
	}

	// User clicked no
	if($mybb->input['no'])
	{
		admin_redirect("index.php?module=config-moods");
	}

	if($mybb->request_method == "post")
	{
		$updated_user = array(
			"mood" => 0
		);

		$db->update_query("users", $updated_user, "mood='{$mood['mid']}'");
		$db->delete_query("moods", "mid='{$mood['mid']}'");

		update_moods();

		// Log admin action
		$name = $lang->parse($mood['name']);
		log_admin_action($mybb->input['mid'], $name);

		flash_message($lang->success_mood_deleted, 'success');
		admin_redirect("index.php?module=config-moods");
	}
	else
	{
		$page->output_confirm_action("index.php?module=config-moods&amp;action=delete&amp;mid={$mood['mid']}", $lang->confirm_mood_deletion);
	}
}

if(!$mybb->input['action'])
{
	$page->output_header($lang->moods);

	$sub_tabs['manage_moods'] = array(
		'title'	=> $lang->manage_moods,
		'link' => "index.php?module=config-moods",
		'description' => $lang->manage_moods_desc
	);

	$sub_tabs['add_mood'] = array(
		'title'	=> $lang->add_mood,
		'link' => "index.php?module=config-moods&amp;action=add"
	);

	$page->output_nav_tabs($sub_tabs, 'manage_moods');

	$pagenum = $mybb->get_input('page', 1);
	if($pagenum)
	{
		$start = ($pagenum - 1) * 20;
	}
	else
	{
		$start = 0;
		$pagenum = 1;
	}

	$table = new Table;
	$table->construct_header($lang->image, array('class' => "align_center", 'width' => 1));
	$table->construct_header($lang->name, array('width' => "70%"));
	$table->construct_header($lang->controls, array('class' => "align_center", 'colspan' => 2));

	$query = $db->simple_select("moods", "*", "", array('limit_start' => $start, 'limit' => 20, 'order_by' => 'name'));
	while($mood = $db->fetch_array($query))
	{
		if(my_strpos($mood['path'], "p://") || substr($mood['path'], 0, 1) == "/")
		{
			$image = $mood['path'];
		}
		else
		{
			$mood['path'] = str_replace("{lang}", "english", $mood['path']);
			$image = "../".$mood['path'];
		}

		$mood['name'] = $lang->parse($mood['name']);

		$table->construct_cell("<img src=\"{$image}\" alt=\"\" />", array("class" => "align_center"));
		$table->construct_cell(htmlspecialchars_uni($mood['name']));

		$table->construct_cell("<a href=\"index.php?module=config-moods&amp;action=edit&amp;mid={$mood['mid']}\">{$lang->edit}</a>", array("class" => "align_center"));
		$table->construct_cell("<a href=\"index.php?module=config-moods&amp;action=delete&amp;mid={$mood['mid']}&amp;my_post_key={$mybb->post_code}\" onclick=\"return AdminCP.deleteConfirmation(this, '{$lang->confirm_mood_deletion}')\">{$lang->delete}</a>", array("class" => "align_center"));
		$table->construct_row();
	}

	if($table->num_rows() == 0)
	{
		$table->construct_cell($lang->no_moods, array('colspan' => 4));
		$table->construct_row();
	}

	$table->output($lang->manage_moods);

	$query = $db->simple_select("moods", "COUNT(mid) AS moods");
	$total_rows = $db->fetch_field($query, "moods");

	echo "<br />".draw_admin_pagination($pagenum, "20", $total_rows, "index.php?module=config-moods&amp;page={page}");

	$page->output_footer();
}
?>