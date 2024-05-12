<?php
/**
 * Mood Manager
 * Copyright 2012 Starpaul20
 */

$l['moodmanager_info_name'] = "Mood Manager";
$l['moodmanager_info_desc'] = "Allows users to set a mood for themselves to display on postbit/profile.";

$l['moods'] = "Moods";
$l['can_manage_moods'] = "Can manage moods?";

$l['add_mood'] = "Add New Mood";
$l['add_mood_desc'] = "Here you can add a single new mood icon.";
$l['add_multiple_moods'] = "Add Multiple Moods";
$l['add_multiple_moods_desc'] = "Here you can add multiple new moods.";
$l['edit_mood'] = "Edit Mood";
$l['edit_mood_desc'] = "Here you can edit a mood icon.";
$l['manage_moods'] = "Manage Moods";
$l['manage_moods_desc'] = "This section allows you to edit, delete, and manage your mood icons.";

$l['name_desc'] = "This is the name for the mood icon. Use <lang:language_string> to allow mood name to be translated (language string must be added to mood.lang.php file).";
$l['image_path'] = "Image Path";
$l['image_path_desc'] = "This is the path to the mood icon image. Use {lang} to represent the user's chosen language if translated mood icons are available.";
$l['save_mood'] = "Save Mood";

$l['name'] = "Name";
$l['image'] = "Image";
$l['controls'] = "Controls";
$l['edit'] = "Edit";
$l['delete'] = "Delete";

$l['path_to_moods'] = "Path to Moods";
$l['path_to_moods_desc'] = "This is the path to the folder that the mood images are in. <strong>Please note that any folder with \"english\" in its path will be automatically changed to {lang} upon being added.</strong>";
$l['show_moods'] = "Show Moods";
$l['add'] = "Add?";
$l['save_moods'] = "Save Moods";

$l['no_moods'] = "There are no moods on your forum at this time.";

$l['error_missing_name'] = "You did not enter a name for this post icon";
$l['error_missing_path'] = "You did not enter a path to this post icon";
$l['error_invalid_mood'] = "The specified mood does not exist.";
$l['error_missing_path_multiple'] = "You did not enter a path";
$l['error_invalid_path'] = "You did not enter a valid path";
$l['error_no_mood_images'] = "There are no mood images in the specified directory, or all mood images in the directory have already been added.";
$l['error_none_included_moods'] = "You did not select any moods to include.";

$l['success_mood_added'] = "The mood has been added successfully.";
$l['success_moods_added'] = "The selected moods have been added successfully.";
$l['success_mood_updated'] = "The mood has been updated successfully.";
$l['success_mood_deleted'] = "The selected mood has been deleted successfully.";

$l['confirm_mood_deletion'] = "Are you sure you wish to delete this mood?";

// Admin Log
$l['admin_log_config_moods_add'] = "Added mood #{1} ({2})";
$l['admin_log_config_moods_add_multiple'] = "Added multiple moods";
$l['admin_log_config_moods_edit'] = "Edited mood #{1} ({2})";
$l['admin_log_config_moods_delete'] = "Deleted mood #{1} ({2})";
