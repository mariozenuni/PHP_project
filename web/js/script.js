$(document).ready(function () {

    initSummerNote();
    $('.float').mask("#.##9,99", {reverse: true});

    initSwitch();
})

function initSummerNote(){

    $('.summernote').each(function(){
        var text =$(this);

        $(this).summernote({
            height: 450, // set editor height
            minHeight: null, // set minimum height of editor
            maxHeight: null, // set maximum height of editor
            focus: false, // set focus to editable area after initializing summernote
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['misc',['undo','redo']],
                ['view', ['fullscreen', 'codeview'],
                ['help', ['help']]
              ],
            ],




        });


     })};


function initSwitch() {
    $('.js-switch').each(function () {
        if ($(this).data("switchery") == null) {
            new Switchery($(this)[0], $(this).data());
        }
    });
}



