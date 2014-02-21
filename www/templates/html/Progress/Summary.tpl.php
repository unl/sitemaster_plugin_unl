Summary
<?php
$user = \SiteMaster\Core\User\Session::getCurrentUser();

if ($user && $context->site->userIsVerified($user)) {
    ?>
    <a href="<?php echo $context->site->getURL() ?>unl_progress/edit/" class="wdn-button">Edit</a>
    <?php 
}
?>
