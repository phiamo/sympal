<?php if ($menu = $menu->render()): ?>
  <div id="sympal_editor">
    <?php echo $menu ?>
  </div>
<?php endif; ?>

<div class="sympal_inline_edit_bar sympal_form">
  <div class="sympal_inline_edit_bar_container">
    <ul>
      <li><?php echo button_to('X', '@sympal_signout', 'title=Signout class=signout confirm=Are you sure you want to signout?') ?></li>
      <li><input type="button" class="toggle_editor_menu" name="toggle_editor_menu" value="Editor Menu" title="Click to toggle Sympal editor menu" /></li>
      <li><?php echo button_to('Dashboard', '@sympal_dashboard', array('class' => 'sympal_dashboard')) ?></li>
      <li><input type="button" class="toggle_edit_mode" value="Enable Edit Mode" /></li>
    </ul>

    <ul class="sympal_inline_edit_bar_buttons">
      <li><input type="button" class="toggle_sympal_assets" name="assets" rel="<?php echo url_for('@sympal_assets_select') ?>" value="Assets" /></li>
      <li><input type="button" class="toggle_sympal_links" name="links" rel="<?php echo url_for('@sympal_editor_links') ?>" value="Links" /></li>
      <li><input type="button" class="sympal_save_content_slots" name="save" value="Save" /></li>
      <li><input type="button" class="sympal_preview_content_slots" name="preview" value="Preview" /></li>
      <li><input type="button" class="sympal_disable_edit_mode" name="disable_edit_mode" value="Disable Edit Mode" /></li>
    </ul>
  </div>

  <?php if (sfSympalConfig::isI18nEnabled()): ?>
    <div class="sympal_inline_edit_bar_change_language">
      <?php
      $user = sfContext::getInstance()->getUser();
      $form = new sfFormLanguage($user, array('languages' => sfSympalConfig::get('language_codes', null, array($user->getCulture()))));
      unset($form[$form->getCSRFFieldName()]);
      $widgetSchema = $form->getWidgetSchema();
      $widgetSchema['language']->setAttribute('onChange', "this.form.submit();");
      ?>
  
      <?php echo $form->renderFormTag(url_for('@sympal_change_language_form')) ?>
        <?php echo $form['language'] ?>
      </form>
    </div>
  <?php endif; ?>
</div>

<div id="sympal_assets"></div>
<div id="sympal_links"></div>