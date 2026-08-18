<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Store only a bcrypt hash. The plaintext activation code is intentionally not
// present in PHP, HTML, JavaScript, or public configuration.
$config['local_activation_code_hash'] = '$2y$12$7UzyvXbzCxgdXdeNU90aY.Ppm.jlr4lCRWzVY4gs5My5gDq7w6k7O';
