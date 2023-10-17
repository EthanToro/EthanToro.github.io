<script src="" type="text/javascript"></script>
<form action="<?php echo $formUrl;?>" method="post" id="new_comment_cms">
    <label for="content">Comment</label>
    <textarea name="content" id="content"><?php if (isset($_POST['content'])) echo $_POST['content']; ?></textarea>
    <br />

    <input type="submit" name="submit" value="Submit" />
</form>
