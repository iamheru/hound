<?php
session_start();

include '../config.php';
include '../libs/hound.php';
include 'libs/houndAdmin.php';

include 'includes/functions.php';

$temppass = $_SESSION['temppass'];
$page = $_GET['page'];

$houndAdmin = new houndAdmin('', '');
$param = $houndAdmin->read_param('../site/config.txt');

if($temppass == $password) {
    include 'includes/header.php';
    include 'includes/sidebar.php'; ?>

    <div class="content">
        <div class="content main">
            <?php
            if($_POST['op'] == 'mod' && $_POST['title'] != 'admin') {
                $slug = $_POST['slug'];
                $title = $_POST['title'];
                $content = $_POST['content'];
                $content = str_replace("\n","",$content);
                $content = str_replace("\\","",$content);

                $slug = $houndAdmin->makeUrlFriendly($slug);

                $metatitle=$_POST['metatitle'];
                $metadescription=$_POST['metadescription'];
                $template=$_POST['template'];
                $featuredimage=$_POST['featuredimage'];

                $file="../site/pages/page-$page.txt";
                $arrayvalue = array(
                    'Slug' => $slug,
                    'Title' => $title,
                    'Content' => $content,
                    'Meta.title' => $metatitle,
                    'Meta.description' => $metadescription,
                    'Template'=> $template,
                    'Featuredimage' => $featuredimage
                );

                if($houndAdmin->write_param($arrayvalue, $file)) {
                    echo '<div class="thin-ui-notification thin-ui-notification-success">Changes saved successfully.</div>';
                } else {
                    echo '<div class="thin-ui-notification thin-ui-notification-error">An error occurred while saving changes.</div>';
                }

                rename("../site/pages/page-$page.txt","../site/pages/page-$slug.txt");
                $page=$slug;

          }
            $paramofpage=$houndAdmin->read_param("../site/pages/page-$page.txt");
            $paramofpage['content']=str_replace("\\","",$paramofpage['content']);
          ?>


            <h2>Edit page</h2>
            <div>
                <a class="thin-ui-button thin-ui-button-primary" target="_blank" href="../<?php echo $page; ?>/">Page preview </a>
            </div>    

            <br>

            <form role="form" id="commentForm" action="edit-page.php?page=<?php echo $page;?>" method="post">
                <input type="hidden" value="mod" name="op">

                <p>
                    <b>Title</b><br>
                    <input name="title" value="<?php echo $paramofpage['title'];?>" type="text" class="thin-ui-input" size="64" required>
                    <br><small>Title of page</small>
                </p>

                <?php if($paramofpage['slug'] != 'index') { ?>
                    <p>
                        <b>Slug</b><br>
                        <input name="slug" value="<?php echo $paramofpage['slug'];?>" type="text" class="thin-ui-input" size="64" required>
                        <br><small>A unique page identification separated by "-" (minus)</small>
                    </p>
                <?php } else { ?>
                    <input name="slug" value="index" type="hidden">
                <?php } ?>

                <p>
                    <b>Content</b><br>
                    <textarea id="txtTextArea1" name="content" style="width:100%" rows="15"><?php echo $paramofpage['content']; ?></textarea>
                </p>

                <p>
                    <b>Template</b>
                    <br><small>Template of page</small>
                    <div class="thin-ui-select-wrapper">
                        <select name="template" id="template">
                            <?php
                            $dirtmpl = scandir('../site/templates/' . $param['template'] . '/');
                            foreach($dirtmpl as $itemtpl) {
                                if(!is_dir('../site/templates/' . $param['template'] . '/' . $itemtpl) && $itemtpl != '.' && $itemtpl != '..' && $itemtpl != '.DS_Store') {
                                    if($itemtpl == $paramofpage['template'])
                                        $sel2 = 'selected';
                                    else
                                        $sel2 = '';
                                    echo '<option ' . $sel2 . ' value="' . $itemtpl . '">' . $itemtpl . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </p>

                <p>
                    <b>Featured image</b><br>
                    <input name="featuredimage" value="<?php echo $paramofpage['featuredimage'];?>" type="text" size="64" class="thin-ui-input">
                    <br><small>Full path of featured image</small>
                </p>

                <p>
                    <b>Meta Title</b><br>
                    <input name="metatitle" value="<?php echo $paramofpage['meta.title'];?>" required type="text" size="64" class="thin-ui-input">
                    <br><small>Search engine Meta Title</small>
                </p>

                <p>
                    <b>Meta description</b><br>
                    <textarea name="metadescription" class="thin-ui-input" style="width:100%" rows="3"><?php echo $paramofpage['meta.description'];?></textarea>
                    <br><small>Search engine Meta description</small>
                </p>

                <p><button type="submit" class="thin-ui-button thin-ui-button-primary">Save Changes</button></p>
            </form>
        </div>
    </div>
    <?php
    include 'includes/footer.php';
}
else {
    php_redirect('index.php?err=1');
}