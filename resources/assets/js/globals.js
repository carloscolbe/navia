/*
 * Global browser bindings.
 *
 * This module must be imported before any other module in app.js: Blade
 * views and the rest of the bundle rely on these window.* globals, and
 * several jQuery plugins grab window.jQuery when their module executes.
 */
import Vue from 'vue';
import jQuery from 'jquery';
import Cropper from 'cropperjs';
import toastr from 'toastr';
import DataTable from 'datatables';
import EasyMDE from 'easymde';
import tinymce from 'tinymce';

window.Vue = Vue;
window.jQuery = jQuery;
window.$ = jQuery;
window.Cropper = Cropper;
window.toastr = toastr;
window.DataTable = DataTable;
window.EasyMDE = EasyMDE;
window.TinyMCE = window.tinymce = tinymce;
