<?php 
$page = 'manage_posts';
require 'inc/sidebar.php';

$editPostMessage = '';

$id = $_GET['id'];
$fetchPostId = $admin->getPostId($id);
while ($result = $fetchPostId->fetch_assoc()) { 
   $postName = $result['PostTitle'];
   $postDes = $result['PostDescription'];
   $postContent = $result['PostContent'];
   $postThumbnailValue = $result['PostThumbnail'];
}

$fetchCategoryId = $admin->getPostCategory($id);
$postCategory = array();
while ($result = $fetchCategoryId->fetch_assoc()){
    array_push($postCategory, $result['CategoryId']);
}

if(isset($_POST['submit']) && $_POST['randcheck']==$_SESSION['rand']){
   $postName = $_POST['postName'];
   $postDes = $_POST['postDes'];
   @$postCategory = $_POST['postCategory'];
   $postContent = $_POST['postContent'];
   $postThumbnailValue = $_POST['postImageValue'];
   
    if (strlen($postName) == 0){
        $editPostMessage = 'Vui lòng nhập tên bài viết!';
    }else if (strlen($postDes) == 0){
        $editPostMessage = 'Vui lòng nhập Giới thiệu bài viết!';
    } else if(@count($postCategory) == 0){
        $editPostMessage = 'Vui lòng chọn thể loại bài viết!';
    } else if (strlen($postContent) == 0){
            $editPostMessage = 'Vui lòng nhập nội dung bài viết!';
    } else if (!$postThumbnailValue){
            $editPostMessage = 'Vui lòng chọn ảnh bìa cho bài viết!';
    }
   if($editPostMessage == '') {	
      $admin->postTitle = $_POST['postName'];
      $admin->postDes = $_POST['postDes'];
      $admin->postCategory = $_POST['postCategory'];
      $admin->postContent = $_POST['postContent'];
      $admin->postThumbnail = $_POST['postImageValue'];

      if($admin->editPost($id)){
        $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc sửa bài viết!';
      } else if ($admin->editPost($id) == "nodata"){
        $editPostMessage = "Có lỗi xảy ra trong việc thực hiện sửa bài viết!";
      }
   }
}
?>
        <div class="admin-management-form">
            <a href="manage_posts.php" style="all:initial;"><i class="fa-solid fa-arrow-left" style="font-size: 20px; cursor: pointer; margin-top: 2rem;"></i></a>
            <h2 style="color: black;">Sửa Bài Viết</h2>
            <?php if ($editPostMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $editPostMessage ?>
                </div>
           <?php } else if (isset($_SESSION['success_message'])) { ?>
                <div class="alert__message success" style="width: 80%">
                        <?= $_SESSION['success_message'] ?>
                    </div>
            <?php  unset($_SESSION['success_message']); }?> 
            <form action="<?php echo $_SERVER['PHP_SELF']."?id=".$id; ?>"  method="POST" enctype="multipart/form-data">
                <!-- Stop resubmission when refreshing page -->
                <?php
                $rand=rand();
                $_SESSION['rand']=$rand;
                ?>
                <div class="form__control">
                    <input type="hidden" name="randcheck" value="<?=$rand; ?>" >
                    <label for="postName">Tên bài viết</label>
                    <input type="text" placeholder="Tên bài viết" name="postName" value="<?php echo isset($postName)? $postName: "" ?>" >
                </div>
                <div class="form__control">
                    <label for="postDes">Giới thiệu bài viết</label>
                    <input type="text" placeholder="Giới thiệu bài viết" name="postDes" value="<?php echo isset($postDes)? $postDes: "" ?>" >
                </div>
                <div class="form__control">
                    <label for="thumbnail">Ảnh bìa bài viết</label>
                    <div class="btn up_image">
                        <i class="fa-solid fa-plus"></i>
                        <span>Đăng tải ảnh bìa</span>
                        <input type="file" accept="image/*" name="postImage" id="thumbnail"  onchange="loadFile(event); getFileName(event); ">
                    </div>
                    <input type="hidden" id="imageValue" name="postImageValue" <?php if(isset($postThumbnailValue)) echo "value =".$postThumbnailValue."" ?>>
                    <p><img id="output" style="width:50vh; vertical-align: middle;" <?php if(!empty($postThumbnailValue)) echo "src='".ROOT_URL."assets/images/thumbnail/".$postThumbnailValue."'";?>/></p>
                </div>
                <div class="form__control">
                <label for="postCategory">Thể loại bài viết</label>
                <select class="postCategory" name="postCategory[]" multiple="multiple">
                    <?php 
                    $fetchCategory = $admin->getAllCategory();
                    if(count($postCategory) > 0){
                        foreach($fetchCategory as $row) { 
                            $i = 0;
                            foreach($postCategory as $check){
                                if($row['CategoryId'] == $check){ ?>
                                    <option value="<?=$row['CategoryId'] ?> "selected><?=$row['CategoryName']?></option>
                                <?php break;}
                                else {
                                    $i++;
                                    if(count($postCategory) == $i){ ?>
                                    <option value="<?=$row['CategoryId'] ?> "><?=$row['CategoryName']?></option>
                                    <?php break;
                                    }   
                                }
                            }
                        }
                    }else { 
                        foreach($fetchCategory as $row) { ?>
                            <option value="<?=$row['CategoryId'] ?>"><?=$row['CategoryName']?></option>
                        <?php } 
                    }?>
                </select>
                </div>
                <div class="form__control">
                    <label for="postContent">Nội dung bài viết</label>
                    <textarea name="postContent" placeholder="Nhập thông tin bài viết" id="postContent"><?php echo isset($postContent)? $postContent: "" ?></textarea>
                </div>
                <button type="submit" class="btn btn_admin" name="submit">Sửa bài viết</button>
            </form>
        </div>

        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
                $('.postCategory').select2();
        </script>
    <!-- CKeditor -->
    <script src="./js/main.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/super-build/ckeditor.js"></script>
    <script>
        CKEDITOR.ClassicEditor.create(document.getElementById("postContent"), {
            // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
            toolbar: {
                items: [
                    'exportPDF','exportWord', '|',
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    '-',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                    'alignment', '|',
                    'link', 'uploadImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                    'textPartLanguage', '|',
                    'sourceEditing'
                ],
                shouldNotGroupWhenFull: true
            },
            // Changing the language of the interface requires loading the language file using the <script> tag.
            // language: 'es',
            list: {
                properties: {
                    styles: true,
                    startIndex: true,
                    reversed: true
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
            placeholder: 'Nhập nội dung bài viết',
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
            fontSize: {
                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                supportAllValues: true
            },
            // Be careful with the setting below. It instructs CKEditor to accept ALL HTML markup.
            // https://ckeditor.com/docs/ckeditor5/latest/features/general-html-support.html#enabling-all-html-features
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true
                    }
                ]
            },
            // Be careful with enabling previews
            // https://ckeditor.com/docs/ckeditor5/latest/features/html-embed.html#content-previews
            htmlEmbed: {
                showPreviews: true
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/link.html#custom-link-attributes-decorators
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            // https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html#configuration
            mention: {
                feeds: [
                    {
                        marker: '@',
                        feed: [
                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                            '@sugar', '@sweet', '@topping', '@wafer'
                        ],
                        minimumCharacters: 1
                    }
                ]
            },
            // The "super-build" contains more premium features that require additional configuration, disable them below.
            // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
            removePlugins: [
                // These two are commercial, but you can try them out without registering to a trial.
                // 'ExportPdf',
                // 'ExportWord',
                'AIAssistant',
                'CKBox',
                'CKFinder',
                'EasyImage',
                // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
                // Storing images as Base64 is usually a very bad idea.
                // Replace it on production website with other solutions:
                // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
                // 'Base64UploadAdapter',
                'RealTimeCollaborativeComments',
                'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory',
                'PresenceList',
                'Comments',
                'TrackChanges',
                'TrackChangesData',
                'RevisionHistory',
                'Pagination',
                'WProofreader',
                // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
                // from a local file system (file://) - load this site via HTTP server if you enable MathType.
                'MathType',
                // The following features are part of the Productivity Pack and require additional license.
                'SlashCommand',
                'Template',
                'DocumentOutline',
                'FormatPainter',
                'TableOfContents',
                'PasteFromOfficeEnhanced'
            ]
        });
    </script>
</body>
</html>