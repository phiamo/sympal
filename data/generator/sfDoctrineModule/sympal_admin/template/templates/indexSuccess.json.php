<?php
$columns = array();
foreach ($this->configuration->getValue('list.display') as $name => $field)
{
  $columns[$name] = $field->getConfig('label', '', true);
}
?>
[?php echo $helper->getNestedSetJsonResults($sf_request->getParameter('root_id'), $pager, <?php echo var_export($columns, true) ?>) ?]