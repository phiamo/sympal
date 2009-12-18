<?php

$app = 'sympal';
require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(8, new lime_output_color());

$dataGrid = sfSympalDataGrid::create('sfGuardUser', 'u')
  ->addColumn('u.id', 'renderer=test/data_grid_id')
  ->addColumn('u.username', 'method=getDataGridUsername')
  ->addColumn('name');

$t->is($dataGrid->getRows(), array(
  array(
    'u.id' => 'partial_1',
    'u.username' => 'method_admin',
    'name' => 'Sympal Admin'
  )
));

$t->is($dataGrid->getPagerHeader(), '<div class="sympal_pager_header"><h3>Showing 1 to 1 of 1 total results.</h3></div>');

$dataGrid = sfSympalDataGrid::create('ContentType', 'c')
  ->setMaxPerPage(1)
  ->setPage(1)
  ->configureColumn('c.id', 'renderer=test/data_grid_id');

$t->is($dataGrid->getRows(), array(
    array(
      'c.id' => 'partial_1',
      'c.name' => 'Page',
      'c.description' => 'The page content type is the default Sympal content type. It is a simple page that only consists of a title and body. The contents of the body are a sympal content slot that can be filled with your selected type of content.',
      'c.label' => 'Page',
      'c.plugin_name' => 'sfSympalPagesPlugin',
      'c.default_path' => '/pages/:slug',
      'c.layout' => NULL,
      'c.slug' => 'page',
    )
  )
);

$dataGrid = sfSympalDataGrid::create('ContentType', 'c')
  ->setMaxPerPage(1)
  ->setRenderingModule('test');

$t->is($dataGrid->render(), 'Test');

$dataGrid = sfSympalDataGrid::create('ContentTemplate', 't')
  ->where('t.name = ?', 'Register')
  ->addColumn('t.name');

$rows = $dataGrid->getRows();
$t->is($rows[0]['t.name'], 'Register');

$dataGrid = sfSympalDataGrid::create('ContentTemplate', 't')
  ->where('t.name = ?', 'Register')
  ->addColumn('t.name', 'is_sortable=false label=Test');

$t->is($dataGrid->getColumnSortLink($dataGrid->getColumn('t.name')), 'Test');

$dataGrid = sfSympalDataGrid::create('ContentTemplate', 't')
  ->where('t.name = ?', 'Register')
  ->addColumn('t.name')
  ->isSortable(false);

$t->is($dataGrid->getColumnSortLink($dataGrid->getColumn('t.name')), 'Name');

$dataGrid = sfSympalDataGrid::create('ContentTemplate', 't')
  ->select('t.id, t.name')
  ->setSort('t.name', 'DESC')
  ->init();

$t->is($dataGrid->getDql(), 'SELECT t.id, t.name FROM ContentTemplate t ORDER BY t.name desc LIMIT 10 OFFSET 0');