import "jquery/dist/jquery";

import "bootstrap/dist/js/bootstrap";

import "pnotify/dist/pnotify.buttons.js";
import "pnotify/dist/pnotify.css";
import "pnotify/dist/pnotify.buttons.css";

import "select2/dist/js/select2";
import "select2/dist/css/select2.css";

import "blueimp-file-upload/js/vendor/jquery.ui.widget";
import "blueimp-file-upload/js/jquery.iframe-transport";
import "blueimp-file-upload/js/jquery.fileupload";

import "bootstrap-datepicker/dist/js/bootstrap-datepicker";
import "twitter-bootstrap-wizard/jquery.bootstrap.wizard";

import "./js/core.init";

import "./less/yawik.less";

import "tinymce/tinymce";
import "tinymce/themes/modern/theme";
import "tinymce/plugins/autolink/plugin";
import "tinymce/plugins/lists/plugin";
import "tinymce/plugins/advlist/plugin";
import "tinymce/plugins/visualblocks/plugin";
import "tinymce/plugins/code/plugin";
import "tinymce/plugins/fullscreen/plugin";
import "tinymce/plugins/contextmenu/plugin";
import "tinymce/plugins/paste/plugin";
import "tinymce/plugins/link/plugin";
import "tinymce/skins/lightgray/skin.min.css";

global.jQuery = window.$ = window.jQuery = require('jquery');
window.PNotify = require('pnotify/dist/pnotify');