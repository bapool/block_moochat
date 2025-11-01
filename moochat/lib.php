<?php
// This file is part of Moodle - http://moodle.org/

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
