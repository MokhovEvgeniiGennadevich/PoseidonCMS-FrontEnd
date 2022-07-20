
<?php include('../template/elements/menu.footer.php'); ?>
<p>&copy; 2009 &mdash; <?=date('Y');?> </p>

    <script src="/js/crypto-js.min.js?<?=$version?>" integrity="sha512-E8QSvWZ0eCLGk4km3hxSsNmGWbLtSCSUcewDQPQWZF6pEU8GlT8a5fF32wOl1i8ftdMhssTrF/OhyGWwonTcXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/js/app.js?<?=$version?>"></script>
    <p><?=__lang('page_generated'); ?>: <?php echo number_format((float)(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2, '.', ''); ?></p>
    <br />
</body>
</html>