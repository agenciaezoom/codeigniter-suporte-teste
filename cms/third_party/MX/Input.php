<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Adapted from the CodeIgniter Core Classes
 * @link    http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Input class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Input.php
 *
 * @copyright   Copyright (c) 2015 Ezoom
 * @version     1.0
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MX_Input extends CI_Input
{
    function get_merge($merge = array()) {
        return $_GET = array_replace($_GET, $merge);
    }

    function get_replace($replace = array()) {
        return $_GET = $replace;
    }

    function get_unset($key = null) {
        if (isset($_GET[$key])) {
            unset($_GET[$key]);
            return true;
        }
        return false;
    }

    function post_merge($merge = array()) {
        return $_POST = array_replace($_POST, $merge);
    }

    function post_replace($replace = array()) {
        return $_POST = $replace;
    }

    function post_unset($key = null) {
        if (isset($_POST[$key])) {
            unset($_POST[$key]);
            return true;
        }
        return false;
    }
}
