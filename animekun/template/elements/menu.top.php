<div class="navbar_top">
    <a href="<?=__urlWeb(['m' => 'main']); ?>"><?=__lang('site_name'); ?></a> 
    
    <?php foreach ( $site_languages as $key => $val ) : ?>
        <?php if ( $val === $user_language ) : ?>
            <?=$user_language;?>
        <?php else: ?>
            <a href="<?=__urlWeb(['m' => 'main', 'l' => $val]); ?>"><?=$val?></a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>