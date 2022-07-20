
<div class="navigation background2">
  <a href="">ЖАНРЫ</a>
  <a href="">ГОД</a>
  <a href="">Главная</a>
  <a href="">Онгоинги</a>
  <a href="">Аниме</a>
  <a href="">OST</a>
  <a href="">Публикации</a>
  <a href="">О Японии</a>
  <a href="">Развлечения</a>
  <a href="">Контакты</a>
  <a href="">Дорамы</a>
</div>

<div class="navigation third">
  <a href="">Вопросы и ответы</a>
  <a href="">Рекомендации</a>
  <a href="">Администрация</a>
  <a href="">Набор в команду</a>
  <a href="">Ошибки</a>
  <a href="">Правила Сайта</a>
  <a href="">Заявки</a>
  <a href="">ТОП пользователей</a>
  <a href="">Топ аниме</a>
</div>

<div class="navigation">
    <div style="display: inline-flex; margin-right: 1em;">
      <b><a href="<?=__urlWeb(['m' => 'main']); ?>" class="logo"><?=__lang('site_name'); ?></a></b>
    </div>
    
    <div style="display: inline-flex; margin-right: 1em;">
      <?php foreach ( $site_languages as $key => $val ) : ?>
          <?php if ( $val === $user_language ) : ?>
            <span><?=$user_language;?></span>
          <?php else: ?>
            <a href="<?=__urlWeb(['m' => 'main', 'l' => $val]); ?>"><?=$val?></a>
          <?php endif; ?>
      <?php endforeach; ?>
    </div>


      <div style="flex-direction: row-reverse;">
        <a href="<?=__urlWeb(['m' => 'user_login']); ?>"><?=__lang('login'); ?></a> | 
        <a href="<?=__urlWeb(['m' => 'user_register']); ?>"><?=__lang('register'); ?></a> 
      </div>
</div>