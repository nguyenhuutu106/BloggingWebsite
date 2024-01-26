<?php 
require 'inc/header.php';

$addPostMessage = '';

if(isset($_POST['submit']) && $_POST['randcheck']==$_SESSION['rand']){
   $postName = $_POST['postName'];
   $postDes = $_POST['postDes'];
   @$postCategory = $_POST['postCategory'];
   $postContent = $_POST['postContent'];
   $postThumbnailValue = $_POST['postImageValue'];

    if (strlen($postName) == 0){
      $addPostMessage = 'Vui lòng nhập tên bài viết!';
    }else if (strlen($postDes) == 0){
    $addPostMessage = 'Vui lòng nhập giới thiệu bài viết!';
    } else if(@count($postCategory) == 0){
      $addPostMessage = 'Vui lòng chọn thể loại bài viết!';
    } else if (strlen($postContent) == 0){
        $addPostMessage = 'Vui lòng nhập nội dung bài viết!';
    } else if (!$postThumbnailValue){
        $addPostMessage = 'Vui lòng chọn ảnh bìa cho bài viết!';
    }
   if($addPostMessage == '') {	
      $user->userId = $account['UserId'];
      $user->postTitle = $_POST['postName'];
      $user->postDes = $_POST['postDes'];
      $user->postThumbnail = $_POST['postImageValue'];
      $user->postContent = $_POST['postContent'];
      $user->postCategory = $_POST['postCategory'];

      if($user->createPost()){
        $_SESSION['success_message'] = 'Bạn đã thực hiện thành công việc thêm bài viết!';
        $postName = "";
        $postDes = "";
        $postCategory = "";
        $postContent = "";
        $postThumbnailValue = "";
      } else if ($user->createPost() == "nodata"){
        $addPostMessage = "Có lỗi xảy ra trong việc thực hiện thêm bài viết!";
      }
   }
}
?>
        <div class="user-sidebar">
            <div class="user-side-content">
                <div class="user-side-menu">
                    <ul>
                      <li>
                        <a href="./account_management.php">
                            <i class="fa-solid fa-user"></i>
                            <small>Tài Khoản</small>
                        </a>
                        <li>
                           <a href="./management_post.php" class="active">
                                <i class="fa-solid fa-pen"></i>
                                <small>Quản Lý Bài Viết</small>
                            </a>
                        </li>
                        </li>
                        <li>
                            <a href="./change_password.php">
                                <i class="fa-solid fa-lock"></i>
                                 <small>Đổi Mật Khẩu</small>
                             </a>
                         </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END SIDEBAR -->
        <div class="admin-management-form" style="margin-top: 72px">
        <a href="management_post.php" style="all:initial;"><i class="fa-solid fa-arrow-left" style="font-size: 20px; cursor: pointer; margin-top: 2rem;"></i></a>
            <h2 style="color: black;">Thêm Bài Viết</h2>
            <?php if ($addPostMessage != '') { ?>
                <div class="alert__message error" style="width: 80%">
                    <?= $addPostMessage ?>
                </div>
           <?php } else if (isset($_SESSION['success_message'])) { ?>
                <div class="alert__message success" style="width: 80%">
                        <?= $_SESSION['success_message'] ?>
                    </div>
            <?php  unset($_SESSION['success_message']); }?> 
            <form action="<?php echo $_SERVER['PHP_SELF']?>"  method="POST" enctype="multipart/form-data">
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
                    $fetchCategory = $user->getAllCategory();
                    if(!empty($postCategory)){
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
                    }
                    else { 
                        foreach($fetchCategory as $row) { ?>
                            <option value="<?=$row['CategoryId'] ?>"><?=$row['CategoryName']?></option>
                        <?php } 
                    }?>

                </select>
                </div>
                <div class="form__control" style="width: 95%">
                    <label for="postContent">Nội dung bài viết</label>
                    <textarea name="postContent" placeholder="Nhập thông tin bài viết" id="postContent"><?php echo isset($postContent)? $postContent: "" ?></textarea>
                </div>
                <button type="submit" class="btn btn_admin" name="submit">Thêm bài viết</button>
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