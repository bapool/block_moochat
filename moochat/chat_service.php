<?php
// This file is part of Moodle - http://moodle.org/

define('AJAX_SCRIPT', true);

require_once('../../config.php');
$PAGE->set_context(context_system::instance());

require_login();

$instanceid = required_param('instanceid', PARAM_INT);
$message = required_param('message', PARAM_TEXT);
$conversationhistory = optional_param('history', '', PARAM_RAW);

// Get block instance
$blockinstance = $DB->get_record('block_instances', array('id' => $instanceid), '*', MUST_EXIST);
$block = block_instance('moochat', $blockinstance);

if (!$block) {
    echo json_encode(array('error' => 'Block not found'));
    die();
}

// Get context
$context = context_block::instance($instanceid);

// Get block configuration
$config = $block->config;
$systemprompt = isset($config->systemprompt) ? $config->systemprompt : get_string('defaultprompt', 'block_moochat');
$maxmessages = isset($config->maxmessages) ? intval($config->maxmessages) : 20;
// Automatic cleanup: Delete records older than 7 days
$cleanuptime = time() - (7 * 86400); // 7 days ago
$DB->delete_records_select('block_moochat_usage', 'lastmessage < ?', array($cleanuptime));

// Check rate limiting
$ratelimit_enabled = isset($config->ratelimit_enable) ? $config->ratelimit_enable : 0;
if ($ratelimit_enabled) {
    $ratelimit_period = isset($config->ratelimit_period) ? $config->ratelimit_period : 'day';
    $ratelimit_count = isset($config->ratelimit_count) ? intval($config->ratelimit_count) : 10;
    
    // Get or create usage record
    $usage = $DB->get_record('block_moochat_usage', 
        array('instanceid' => $instanceid, 'userid' => $USER->id));
    
    $now = time();
    $period_seconds = ($ratelimit_period === 'hour') ? 3600 : 86400;
    
    if ($usage) {
        // Check if we need to reset the counter
        if (($now - $usage->firstmessage) >= $period_seconds) {
            // Period has expired, reset counter
            $usage->messagecount = 0;
            $usage->firstmessage = $now;
            $usage->lastmessage = $now;
            $DB->update_record('block_moochat_usage', $usage);
        } else {
            // Check if limit reached
            if ($usage->messagecount >= $ratelimit_count) {
                $period_string = get_string('ratelimitreached_' . $ratelimit_period, 'block_moochat');
                echo json_encode(array(
                    'error' => get_string('ratelimitreached', 'block_moochat', 
                        array('limit' => $ratelimit_count, 'period' => $period_string)),
                    'remaining' => 0
                ));
                die();
            }
        }
    } else {
        // Create new usage record
        $usage = new stdClass();
        $usage->instanceid = $instanceid;
        $usage->userid = $USER->id;
        $usage->messagecount = 0;
        $usage->firstmessage = $now;
        $usage->lastmessage = $now;
        $usage->id = $DB->insert_record('block_moochat_usage', $usage);
    }
}

// Parse conversation history
$history = array();
if (!empty($conversationhistory)) {
    $history = json_decode($conversationhistory, true);
    if (!is_array($history)) {
        $history = array();
    }
}

// Check message limit (old system, kept for compatibility)
if ($maxmessages > 0 && count($history) >= ($maxmessages * 2)) {
    echo json_encode(array('error' => get_string('maxmessagesreached', 'block_moochat')));
    die();
}

// Build full prompt with system instructions and conversation history
$fullprompt = $systemprompt . "\n\n";

// Add conversation history
foreach ($history as $msg) {
    if ($msg['role'] === 'user') {
        $fullprompt .= "User: " . $msg['content'] . "\n";
    } else if ($msg['role'] === 'assistant') {
        $fullprompt .= "Assistant: " . $msg['content'] . "\n";
    }
}

// Add current message
$fullprompt .= "User: " . $message . "\nAssistant:";

try {
    // Create AI action using Moodle's core AI system
    $action = new \core_ai\aiactions\generate_text(
        contextid: $context->id,
        userid: $USER->id,
        prompttext: $fullprompt
    );
    
    // Get AI manager and process the action
    $manager = \core\di::get(\core_ai\manager::class);
    $response = $manager->process_action($action);
    
    if ($response->get_success()) {
        $reply = $response->get_response_data()['generatedcontent'] ?? '';
        
        // Update usage counter if rate limiting is enabled
        if ($ratelimit_enabled && isset($usage)) {
            $usage->messagecount++;
            $usage->lastmessage = time();
            $DB->update_record('block_moochat_usage', $usage);
            
            $remaining = $ratelimit_count - $usage->messagecount;
        } else {
            $remaining = -1; // Unlimited
        }
        
        // Return success response
        echo json_encode(array(
            'success' => true,
            'reply' => trim($reply),
            'remaining' => $remaining
        ));
    } else {
        // Return error from AI system
        echo json_encode(array(
            'error' => $response->get_errormessage() ?: 'AI generation failed'
        ));
    }
    
} catch (Exception $e) {
    echo json_encode(array(
        'error' => 'Error: ' . $e->getMessage()
    ));
}
