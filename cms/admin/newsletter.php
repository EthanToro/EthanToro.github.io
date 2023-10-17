<?php
include('includes/config.php');
CleanRequestVars();
CheckLogin();
$user = getUser();
$action = get('action');
$output = '';
$message = '';

switch ($action) {
    default:
        if(Post('submit',false)){
           newsLetterProcess();
        }
        newsLetterView();
        break;
}

function newsLetterProcess(){
  global $output, $cms,$siteFileUrl;
  ob_start();
  chdir('../');
  $storeIn = getcwd().'uploads\\files\\newsletter.pdf';
  chdir('admin/');

  if ($_FILES["file"]["error"] > 0){
    echo "Error: " . $_FILES["file"]["error"] . "<br />";
  }else{
    if($_FILES["file"]["type"]=="application/pdf"){
      if(is_uploaded_file($_FILES["file"]["tmp_name"])){
        if(move_uploaded_file($_FILES["file"]["tmp_name"], $storeIn)){
          echo "Newsletter UPLOADED<br>";
        }else{
          echo "Newsletter upload failed, contact Synergy<br>";
        }
      }
    }
  }
  $output .= ob_get_clean();
}
function newsLetterView() {
    global $output, $cms;
    ob_start();
    chdir('../');
    $storeIn = getcwd().'uploads\\files\\newsletter.pdf';
    chdir('admin/');
    echo "Last Uploaded Date: ".$fileTime =  date("F j, Y, g:i a",filemtime($storeIn));

    ?>
      <form method="post" action='newsletter.php' enctype="multipart/form-data">
      <label for="file">Newsletter:</label>
      <input type="file" name="file" id="file" /> 
      <br />
      <input type="submit" name="submit" value="Submit" />
      </form>
    <?php
    $output .= ob_get_clean();
}

include('includes/header.php');
echo $output;
include('includes/footer.php');
