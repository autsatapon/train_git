/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

// CKFinder.setupCKEditor( null, '../../../../../assets/images/ckfinder/' );

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    /*
    config.toolbar = [
        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
        { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
        { name: 'links', items: [ 'Link', 'Unlink' ] },
        '/',
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat' ] },
        { name: 'styles', items: [ 'Format', 'FontSize' ] },
        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'document', items: [ 'Source' ] },
        { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
    ];
    */

    config.filebrowserBrowseUrl = '../../../../../assets/images/ckfinder/ckfinder.html';
    config.filebrowserUploadUrl = '../../../../../assets/images/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';
    config.filebrowserImageBrowseUrl = '../../../../../assets/images/ckfinder/ckfinder.html?type=Images';
    config.filebrowserImageUploadUrl = '../../../../../assets/images/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';
    config.filebrowserWindowWidth  = 1000;
    config.filebrowserWindowHeight = 700;

    config.toolbar = [
        { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat' ] },
        { name: 'styles', items: [ 'Format', 'FontSize' ] },
        { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
        { name: 'paragraph', items: [ 'NumberedList', 'BulletedList' ] },
        { name: 'insert', items: [ 'Table', 'HorizontalRule' ] },
        { name: 'links', items: [ 'Link', 'Unlink','Image' ] },
        { name: 'document', items: [ 'Source' ] }
    ];
};
