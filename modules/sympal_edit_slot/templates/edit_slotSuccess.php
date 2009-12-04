<div id="edit_slot" class="sf_admin_form sympal_form">
  <?php echo get_sympal_flash() ?>

  <div id="sf_admin_container">
    <?php echo $form->renderFormTag(url_for('@sympal_save_content_slot?id='.$sf_request->getParameter('id')), array('onSubmit' => "javascript: save_sympal_content_slot('".$sf_request->getParameter('id')."'); return false;", 'id' => 'edit_content_slot_form_'.$sf_request->getParameter('id'), 'method' => 'post')) ?>
    <input type="hidden" name="is_column" value="<?php echo $sf_request->getParameter('is_column') ?>" />
    <input type="hidden" name="name" value="<?php echo $sf_request->getParameter('name') ?>" />

    <table>
      <?php echo $form ?>
    </table>

    <div id="save">
      <input type="button" id="save_button" name="save" value="<?php echo __('Save') ?>" onClick="javascript: save_sympal_content_slot('<?php echo $sf_request->getParameter('id') ?>');" />
    </div>
    <div id="loading"></div>
    <br style="clear: both;" />
    </form>
  </div>
</div>