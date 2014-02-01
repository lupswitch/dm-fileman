$(document).ready(function(){
    var createDirectoryForm, uploadFileForm, deleteFileForm, fileThumbs, deleteFileBtn, selectFileBtn, selectedFile = null, isTinyMce;

    createDirectoryForm = $('#create-directory-form');
    uploadFileForm      = $('#upload-file-form');
    deleteFileForm      = $('#delete-file-form');
    fileThumbs          = $('#file-thumbs');
    deleteFileBtn       = $('#delete-file-btn');
    selectFileBtn       = $('#select-file-btn');

    isTinyMce = !(typeof window.parent.tinymce === 'undefined');

    $('#create-directory-btn').click(function(){
        createDirectoryForm.toggleClass('hidden');
        uploadFileForm.addClass('hidden');
    });

    $('#upload-file-btn').click(function(){
        uploadFileForm.toggleClass('hidden');
        createDirectoryForm.addClass('hidden');
    });

    fileThumbs.on("dblclick", "a", function() {
        location.href = $(this).attr('href');
    });

    fileThumbs.on("click", "a", function(event) {
        var parent;

        event.preventDefault();

        parent = $(this).parent();

        if (selectedFile) {
            selectedFile.removeClass('selected');
        }

        if (selectedFile===null || selectedFile[0] !== parent[0]) {
            selectedFile = parent;
            parent.addClass('selected');
            deleteFileBtn.attr('disabled', false);

            if ($(this).css('background-image').indexOf('thumb') > 0) {
                selectFileBtn.attr('disabled', false);
            }
        } else {
            parent.removeClass('selected');
            deleteFileBtn.attr('disabled', true);
            selectFileBtn.attr('disabled', true);
            selectedFile = null;
        }
    });

    deleteFileBtn.click(function(){
        var name;

        if (selectedFile !== null) {
            name = $('a', selectedFile).text();

            $('input[name=name]', deleteFileForm).val(name);

            $('input[type=submit]', deleteFileForm).click();
        }
    });

    if (isTinyMce) {
        selectFileBtn.click(function(){
            var href, tag;

            if (selectedFile !== null) {
                href = $('a', selectedFile).attr('href');

                tag = '<img src="' + fixHref(href) + '" alt="" />';

                window.parent.tinymce.activeEditor.insertContent(tag);
                window.parent.tinymce.activeEditor.windowManager.close();
            }
        });
    } else {
        selectFileBtn.addClass('hidden');
    }

    function fixHref(href) {
        var params;

        params = top.tinymce.activeEditor.windowManager.getParams();

        if (params.file_path_base) {
            href = href.replace(params.file_path_base, params.file_url_base);
        }

        if (params.server_url) {
            href = params.server_url + href;
        }

        return href;
    }
});