<?php
/*
 * Copyright (c) 2016 Delicious Brains. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 */

defined( 'WPINC' ) or die;

require_once GP_LIBRARY_DIRECTORY . 'batch-processing/wp-async-request.php';
require_once GP_LIBRARY_DIRECTORY . 'batch-processing/wp-background-process.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-ipq-process.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-image-processing-queue.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/ipq-template-functions.php';

Image_Processing_Queue::instance();
