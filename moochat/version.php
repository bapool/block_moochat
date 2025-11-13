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

$plugin->component = 'block_moochat';
$plugin->requires = 2022041900; // Moodle 4.0
$plugin->version = 2025111300;  // YYYYMMDDXX - Fixed language strings and compiled JavaScript
$plugin->maturity = MATURITY_BETA;
$plugin->release = 'v1.3';
