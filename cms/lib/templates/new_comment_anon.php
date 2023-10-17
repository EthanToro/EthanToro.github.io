<script src="" type="text/javascript"></script>
<form action="" method="post" id="new_comment_cms">
    <label for="content">Comment</label>
    <br/>
    <textarea name="content" id="content"><?php if (isset($_POST['content'])) echo $_POST['content']; ?></textarea>

    <br/>
    <label for="name">Username:</label>
    <input type="text" name="name" id="name" <?php if (isset($_POST['name'])) echo 'value="'.$_POST['name'].'"'; ?>/>

    <br/>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" <?php if (isset($_POST['email'])) echo 'value="'.$_POST['email'].'"'; ?>/>

    <input type="submit" name="submit" value="Submit" />
</form>
