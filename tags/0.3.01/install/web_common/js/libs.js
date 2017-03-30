/**
 * Created by nas on 24/08/15.
 */
requirejs.config({
    //By default load any module IDs from jscss/js/web1
    baseUrl: '/jscss/js',
    //except, if the module ID starts with "app",
    //load it from the js/app directory. paths
    //config is relative to the baseUrl, and
    //never includes a ".js" extension since
    //the paths config could be for a directory.
    paths: {
        "domReady": '/jscss/common/js/libs/domReady',
        "system_manager": '/jscss/common/js/system_manager',
        "search_manager": '/jscss/common/js/search_manager',
        "manager_loader": '/jscss/common/js/manager_loader',
        "dropdown_manager": '/jscss/common/js/dropdown_manager',
        "login" :'/jscss/js/web1/login',
        "modal_dialog": '/jscss/common/js/modal_dialog',


        "jquery": '/jscss/common/js/libs/external/jquery/jquery',
        "jquery-ui": '/jscss/common/js/libs/external/jquery-ui/jquery-ui.min',
        // jquery blockUI include
        "block-ui": '//cdn.trunknetworks.com/jquery-blockui/2.70.0-2014.11.23/jquery.blockUI',
        // jquery serialize hash includes
        "serialize_hash": '//cdn.trunknetworks.com/jquery-serialize-hash/2015.06.04/jquery.serialize-hash',
        /*
        * Bootstrap Includes - not required if using jQuery UI as core framework
        */
        "bootstrap": '/jscss/common/js/libs/external/bootstrap/js/bootstrap.min',
        // Bootstrap typeahead (autocomplete) bundle
        //"typeahead": '//cdn.trunknetworks.com/bootstrap-typeahead/0.11.1/dist/typeahead.bundle.min',
        "typeahead": '//cdn.trunknetworks.com/bootstrap-typeahead/0.11.1/dist/typeahead.jquery',
        "bloodhound": '//cdn.trunknetworks.com/bootstrap-typeahead/0.11.1/dist/bloodhound',
        // Bootstrap growl functionality
        "growl": '//cdn.trunknetworks.com/bootstrap-growl/1.1.0/jquery.bootstrap-growl.min',
        // Bootstrap sweet alert functionality
        "sweet_alert": '//cdn.trunknetworks.com/bootstrap-sweetalert/0.4.5/lib/sweet-alert.min',
        // Bootstrap date picker
        "datepicker": '/jscss/common/js/libs/external/bootstrap-datepicker/js/bootstrap-datepicker',
        // Bootstrap jquery validator
        "validator": '//cdn.trunknetworks.com/bootstrap-validator/0.9.0/js/validator',
    },

    shim: {
        'jquery': {
            exports: 'jQuery'
        },
        'jquery-ui': {
            deps: ['jquery'],
            exports: 'jQuery.ui'
        },

        'block-ui': {
            deps: ['jquery'],
            exports: 'jQuery.fn.block'
        },
        'serialize_hash': {
            deps: ['jquery'],
            exports: 'jQuery.fn.serializeHash'
        },

        'bootstrap': {
            deps: ['jquery']
            //exports: 'jQuery.fn.'
        },

        /*'typeahead': {
            deps: ['jquery'],
            exports: 'jQuery.fn.typeahead'
        },*/
        'growl': {
            deps: ['jquery'],
            exports: 'jQuery.bootstrapGrowl'
        },
        'sweet_alert': {
            deps: ['jquery'],
            exports: 'jQuery.fn.alert'
        },
        'datepicker': {
            deps: ['jquery'],
            exports: 'jQuery.fn.datepicker'
        },
        'validator': {
            deps: ['jquery'],
            exports: 'jQuery.fn.validator'
        },
        'plupload':{
            deps: ['jquery','jquery-ui','moxie'],
            exports: 'plupload'
        },
        'plupload-ui': {
            deps: ['plupload'],
            exports: 'jQuery.fn.plupload'
        },
    },
    // Add timestamp to prevent caching
    //urlArgs: "bust=" + (new Date()).getTime()
});

require(['jquery', 'jquery-ui' ,'bootstrap' ], function ( $ ) {});

// Start the main app logic. Load all th required libraries before loading page specific modules
/*require(['jquery','domReady','jquery-ui', 'block-ui', 'serialize_hash', 'custommenu', 'bootstrap', 'typeahead', 'growl', 'sweet_alert', 'datepicker', 'validator','bloodhound', 'system_manager', 'init', 'plupload' ],
    function ( $ ) {
        require([ 'search_manager', 'domReady!' ],  function ( search_manager ) { search_manager.init(); });
});*/
// Search manager
require([ 'search_manager', 'domReady!' ],  function ( search_manager ) { search_manager.init(); });
// Module dynamic loading manager
require([ 'manager_loader' ],  function ( manager_loader ) { manager_loader.init(); });
// File upload manager
//require([ 'file_uploader', 'domReady!' ],  function ( upload_manager ) { upload_manager.init(); });
