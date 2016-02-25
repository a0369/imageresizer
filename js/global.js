var imageHID;

function progressHandlingFunction(e) {
    if(e.lengthComputable) {
        $("#progressBar").attr({
            value : e.loaded,
            max : e.total
        });
    }
}

function uploadImg(dropFiles) {
    $("#uploadArea").hide();
    $("#uploadingProgress").show();

    var formData = new FormData($('#imgUpload')[0]);
    if (dropFiles && dropFiles.length > 0) {
        formData.append("dUpload", dropFiles[0]);
    }

    $.ajax({
        url: "index.php",
        type: "POST",
        xhr: function() {  // Custom XMLHttpRequest
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) { // Check if upload property exists
                myXhr.upload.addEventListener('progress', 
                    progressHandlingFunction, false); // For handling the progress of the upload
            }
            return myXhr;
        },
        //Ajax events
        //beforeSend: beforeSendHandler,
        success: function (data) {
            // Convert to JSON format
            var data = $.parseJSON(data);

            if (data.error == true) {
                $("#uploadMsg").text(data.msg).show();
                $("#uploadingProgress").hide();
                $("#uploadArea").show();

                return;
            }

            $("#imgOriWidth").text(data.width);
            $("#imgOriHeight").text(data.height);
            $("#newWidthTxt").val(data.width);
            $("#newHeightTxt").val(data.height);
            imageHID = data.hid;

            $("#step1").hide();
            $("#uploadingProgress").hide();
            $("#uploadArea").show();
            $("#step2").show();

            $('#imgUpload')[0].reset();
        },
        error: function () {
            $("#uploadMsg").text("An upload error has occured!").show();
            $("#uploadingProgress").hide();
            $("#uploadArea").show();
            $('#imgUpload')[0].reset();
        },
        data: formData,
        cache: false,
        contentType: false,
        processData: false
    });
}

$(function () {
    // File Upload Functions
    $("#imgUploadFile").change(function () {
        uploadImg();
    });

    $("#uploadBtn").click(function () {
        $("#imgUploadFile").click();
    });

    if (window.File && window.FileList && window.FileReader) {
        var xhr = new XMLHttpRequest();
        if (xhr.upload) {
            $("#uploadImageTxt").show();
            // Upload Area Functions
            $("#uploadArea").on("drop", function (e) {
                e.stopPropagation();
                e.preventDefault();
                
                var files = (e.originalEvent.target.files || e.originalEvent.dataTransfer.files);
                uploadImg(files);
            });

            $("#uploadArea").on("dragover", function (e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).addClass("dragActive");
            });

            $("#uploadArea").on("dragleave", function () {
                $(this).removeClass("dragActive");
            });
        }
    }

    // Converting Functions
    $("#convertImgBtn").click(function () {
        if (isNaN($("#newWidthTxt").val()) || isNaN($("#newHeightTxt").val())) {
            alert("Width and Height must be a number!");
            return;
        }

        var newWidth = parseInt($("#newWidthTxt").val());
        var newHeight = parseInt($("#newHeightTxt").val());

        if (newWidth < 1 || newHeight < 1) {
            alert("Width and Height must be over 0px!");
            return;
        }

        $("#convertedImg").attr("src", "getpic.php?hid=" + imageHID +
            "&w=" + newWidth + "&h=" + newHeight);

        $("#step2").hide();
        $("#step3").show();
    });

    $("#newImageBtn").click(function () {
        $("#uploadArea").removeClass("dragActive");
        $.ajax("getpic.php?del=true&hid=" + imageHID);

        $("#step3,#show2").hide();
        $("#step1").show();
    });

    // Show Step 1 after load
    $("#step1").show();
});
