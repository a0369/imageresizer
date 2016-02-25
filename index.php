<?php
$file = false;

if (isset($_FILES['dUpload']) && $_FILES['dUpload']['size'] > 0 && $_FILES['dUpload']['name'] !="") {
    $file = $_FILES['dUpload'];
} else if (isset($_FILES['iUpload']) && $_FILES['iUpload']['size'] > 0 && $_FILES['iUpload']['name'] !="") {
    $file = $_FILES['iUpload'];
}

if ($file !== false) {
    //if ($file['size'] > 0 && $file["name"] != "") {
        //check type
        $elem = getimagesize($file["tmp_name"]);
        $type = image_type_to_extension($elem[2], false);

        switch ($type) {
            case 'png':
                $new_ext = "png";
                $im = imagecreatefrompng($file["tmp_name"]);
            break;
            case 'jpeg':
                $new_ext = "jpg";
                $im = imagecreatefromjpeg($file["tmp_name"]);
            break;
            case 'gif':
                $new_ext = "gif";
                $im = imagecreatefromgif($file["tmp_name"]);
            break;
            default: 
                echo json_encode(array(
                    'error' => true,
                    'msg' => 'Invalid File Type'
                ));

                exit;
        }

        $randomHash = sha1(time() . 'randomsalt', false);

        imagepng($im, 'uploads/' . $randomHash . '.simg');
        imagedestroy($im);

        echo json_encode(array(
            'error' => false,
            'msg' => '',
            'width' => $elem["0"],
            'height' => $elem["1"],
            'type' => $new_ext,
            'hid' => $randomHash
        ));
    /*} else {
        echo json_encode(array(
            'error' => true,
            'msg' => 'Invalid File'
        ));
    }*/

    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Image Resizer</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery2.2.0.js"></script>
    <script src="js/global.js"></script>
</head>
<body>
    <div id="step1" class="optionContainer">
        <div class="attn" id="uploadMsg"></div>
        <div id="hiddenUploadForm">
            <form id="imgUpload" enctype="multipart/form-data">
                <input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="20000000" />
                <input type="file" name="iUpload" id="imgUploadFile">
                <input type="file" name="dUpload">
            </form>
        </div>
        <div id="uploadArea">
            <img src="images/upload.png" alt=""><br>
            <span id="uploadImageTxt">Drag Image Here</span><br>
            <button id="uploadBtn" class="sButton">Upload Image</button>
        </div>
        <div id="uploadingProgress">
            Uploading File<br>
            <progress id="progressBar"></progress>
        </div>
    </div>
    <div id="step2" class="optionContainer">
        <div id="sizeContainer">
            <div>
                Original Size: <span id="imgOriWidth"></span><span class="sm">px</span> x <span id="imgOriHeight"></span><span class="sm">px</span>
            </div>
            <div>
                New Size: <input type="number" value="" id="newWidthTxt"> x <input type="number" value="" id="newHeightTxt">
            </div>
            <button class="sButton" id="convertImgBtn">Convert</button>
        </div>
    </div>
    <div id="step3" class="optionContainer">
        <div id="loadingConvert">

        </div>
        <div id="newImageContainer">
            <img src="" alt="Converted Image" id="convertedImg">
        </div>
        <div id="newImageBottom">
            <button class="sButton" id="newImageBtn">New Image</button>
        </div>
    </div>
    <footer>
        &copy; <a href="http://github.com/a0369" target="_new">a0369</a>
    </footer>
</body>
</html>
