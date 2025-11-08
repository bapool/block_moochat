<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
/**
 * Privacy Subsystem implementation for mod_mooproof
 *
 * @package    block_moochat
 * @copyright  2025 Brian A. Pool
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Serve the files from the moochat file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool false if file not found, does not return if found - just send the file
 */
function block_moochat_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    
    if ($context->contextlevel != CONTEXT_BLOCK) {
        return false;
    }
    
    if ($filearea !== 'avatar') {
        return false;
    }
    
    require_login($course);
    
    $fs = get_file_storage();
    $filename = array_pop($args);
    $filepath = '/';
    
    $file = $fs->get_file($context->id, 'block_moochat', $filearea, 0, $filepath, $filename);
    
    if (!$file || $file->is_directory()) {
        return false;
    }
    
    // Send the file
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
