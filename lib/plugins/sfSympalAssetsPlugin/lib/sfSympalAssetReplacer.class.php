<?php

/**
 * Class responsible for parsing and replacing syntaxes for linking to assets,
 * embedding assets, linking to other content, etc.
 *
 * Examples:
 *
 *  * [asset:1 label="Linking to content id #1"]
 *  * [link:123]
 *  * [asset:1 link=true]
 *  * [asset:1 embed=true]
 */
class sfSympalAssetReplacer
{
  private
    $_slot,
    $_content;

  public function __construct(sfSympalContentSlot $slot)
  {
    $this->_slot = $slot;
    $this->_content = $slot->getContentRenderedFor();
  }

  public static function listenToFilterSlotContent(sfEvent $event, $content)
  {
    $replacer = new self($event->getSubject());
    return $replacer->replace($content);
  }

  public function replace($content)
  {
    if ($parsed = $this->_parseSyntaxes($content))
    {
      $ids = $parsed['ids'];
      $replacements = $parsed['replacements'];

      if (isset($ids['asset']) && $ids['asset'])
      {
        $content = $this->_replaceAssets($ids['asset'], $replacements['asset'], $content);
      }
      if (isset($ids['link']) && $ids['link'])
      {
        $content = $this->_replaceLinks($ids['link'], $replacements['link'], $content);
      }
    }
    return $content;
  }

  private function _parseSyntaxes($content)
  {
    preg_match_all("/\[(link|asset):(.*)\]/", $content, $matches);

    if (isset($matches[0]) && $matches[0])
    {
      $ids = array();
      $replacements = array();

      $types = $matches[1];
      $bodies = $matches[2];

      foreach ($bodies as $key => $body)
      {
        $e = explode(' ', $body);
        $ids[$types[$key]][] = $e[0];
        $replacements[$types[$key]][] = array(
          'id' => $e[0],
          'options' => _parse_attributes(str_replace($e[0], null, $body)),
          'replace' => $matches[0][$key]
        );
      }
      return array(
        'ids' => $ids,
        'replacements' => $replacements
      );
    } else {
      return false;
    }
  }

  private function _replaceAssets($ids, $replacements, $content)
  {
    $assetObjects = $this->_content->Assets;
    if ($assetObjects->count() != count($ids))
    {
      $assetObjects = Doctrine_Core::getTable('sfSympalAsset')
        ->createQuery()
        ->from('sfSympalAsset a INDEXBY a.id')
        ->whereIn('a.id', array_unique($ids))
        ->execute();
      foreach ($assetObjects as $assetObject)
      {
        $this->_content->Assets[] = $assetObject;
      }
    }

    foreach ($replacements as $replacement)
    {
      $assetObject = $assetObjects[$replacement['id']];
      $content = $assetObject->filterContent($content, $replacement['replace'], $replacement['options']);
    }
    return $content;
  }

  private function _replaceLinks($ids, $replacements, $content)
  {
    $contentObjects = $this->_content->Links;
    if ($contentObjects->count() != count($ids))
    {
      $q = Doctrine_Core::getTable('sfSympalAsset')
        ->createQuery('c')
        ->from('sfSympalContent c INDEXBY c.id')
        ->innerJoin('c.Type t')
        ->whereIn('c.id', array_unique($ids));

      if (sfSympalConfig::isI18nEnabled('sfSympalContent'))
      {
        $q->leftJoin('c.Translation ct');
      }

      $contentObjects = $q->execute();
      foreach ($contentObjects as $contentObject)
      {
        $this->_content->Links[] = $contentObject;
      }
      $this->_content->save();
    }

    foreach ($replacements as $replacement)
    {
      $contentObject = $contentObjects[$replacement['id']];
      $label = isset($replacement['options']['label']) ? $this->replace($replacement['options']['label']) : 'Link to #'.$replacement['id'];
      unset($replacement['options']['label']);

      $content = str_replace($replacement['replace'], link_to($label, $contentObject->getRoute(), $replacement['options']), $content);
    }
    return $content;
  }
}