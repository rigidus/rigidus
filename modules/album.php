<?php


if (!$bFlagLastModule) return;

// BreadCrumbs
$BreadCrumbs->addBreadCrumbs($sKey, $sTitleMenu);
$_t->assign('BreadCrumbs', $BreadCrumbs->getBreadCrumbs());


// OPEN
$tpl = $_t->fetchBlock('ContentBlock');



// SAPE
if (defined("_SAPE_USER")) {
  $tpl->assign('content', $sape_context->replace_in_text_segment(crbr($sText)));
  $tpl->assign('my_links', $sape->return_links());
} else {
  $tpl->assign('content', crbr($sText));
  $tpl->assign('my_links', '');
}


if (!is_numeric($sModuleParam)) {

  // Ошибка - не указан идентификатор альбома в параметрах страницы
  $tpl->assign('content', 'С этой страницей не связан ни один фотоальбом.');

} elseif(!$bFlag404) {

  define('IMG_PER_PAGE', 66);

  if (!isset($_GET['from'])) {
	$_GET['from'] = 0;
  } elseif (!is_numeric($_GET['from'])) {
	$_GET['from'] = 0;
  }

  $sql = "SELECT COUNT(*) FROM `".DB_PREFIX.DB_TBL_IMAGES."` WHERE `album` = ".$sModuleParam;
  $sql = mysql_query($sql);
  if (false == $sql) my_die();
  $row = current(mysql_fetch_assoc($sql));

  $tplAlbum = new KTemplate(FLGR_TEMPLATES.'/album.htm');

  if ($row >= IMG_PER_PAGE) {
	$tplPaginator = $tplAlbum->fetchBlock('Paginator');
	$tplPaginator->assign('PageSelected', '');
	$tplPaginator->assign('Page', '');
	$j = 0;
	for ($i=0; $i<$row; $i=$i+IMG_PER_PAGE) {
	  $j++;
	  if ($i == $_GET['from']) {
		$tplPage = $tplPaginator->fetchBlock('PageSelected');
		$tplPage->assign('bgcolor', '#FFFFCC');
		$tplPage->assign('num', $j);
		$tplPaginator->assign('PagePlace', $tplPage);
		$tplPage->reset();
	  } else {
		$tplPage = $tplPaginator->fetchBlock('Page');
		$tplPage->assign('link', slashify($sRequest).'?from='.$i);
		$tplPage->assign('num', $j);
		$tplPaginator->assign('PagePlace', $tplPage);
		$tplPage->reset();
	  }
	}

	$tplAlbum->assign('Paginator', $tplPaginator);
	$tplAlbum->assign('BottomPaginator', $tplPaginator);
	$tplPaginator->reset();

  } else {

	$tplAlbum->assign('Paginator', '');
	$tplAlbum->assign('BottomPaginator', '');

  }

  $sql = "SELECT * FROM `".DB_PREFIX.DB_TBL_IMAGES."` WHERE `album` = ".$sModuleParam." ORDER BY `order`
		  LIMIT ".$_GET['from'].", ".IMG_PER_PAGE;
  $sql = mysql_query($sql);
  if (false == $sql) my_die();
  $aPhotos = array();
  while ($row = mysql_fetch_assoc($sql)) {
	$aPhotos[] = $row;
  }
  /* dbg($aPhotos); */

  if (empty($aPhotos)) {
	$tplAlbum->assign('ThumbNail', 'В этом альбоме нет изображений');
  }

  foreach ($aPhotos as $v) {
	$tplThumbNail = $tplAlbum->fetchBlock('ThumbNail');
	$tplThumbNail->assign('link', slashify($sRequest).$v['file']);
	$tplThumbNail->assign('img', slashify(ADDR_PHOTOS_THUMBNAILS).$v['file']);
	$tplAlbum->assign('ThumbNail', $tplThumbNail);
	$tplThumbNail->reset();
  }

  $tpl->assign('content', $tplAlbum);
  $tplAlbum->reset();

} else {

  $sql = "SELECT * FROM `".DB_PREFIX.DB_TBL_IMAGES."` WHERE `file` = '".$aRequest[count($aRequest)-1]."'";
  $sql = mysql_query($sql);
  if (false == $sql) my_die();
  $aP = array();
  while ($row = mysql_fetch_assoc($sql)) {
	$aP = $row;
  }

  if (!empty($aP)) {

	$tpl->assign('content', '<center><img src="'.ADDR_PHOTOS_NORMALS.'/'.$aRequest[count($aRequest)-1].'" />');
	if (!empty($aP['title'])) {
	  $sTitle = $aP['title'];
	}
	if (!empty($aP['descr'])) {
	  $tpl->assign('content', '<br/><br/><center>'.crbr($aP['descr']).'</center>');
	}
	$tpl->assign('content', '<br/><br/><center><a href="javascript: history.back();">Назад</a></center>');

	$bFlag404 = false;
  }

}

// ANNOTATIONS
$sql = "SELECT `key`, `title`, `annotation`
		FROM `".DB_PREFIX.DB_TBL_PAGES."`
		WHERE parent = ".$nId."
		ORDER BY `order`";
$sql = mysql_query($sql);
if (false == $sql) my_die();
while ($row = mysql_fetch_assoc($sql)) {
  $tpl->assign('content', crbr('<div style="font-size: 90%; margin-bottom: 8px;"><a href="'.$sRequest.'/'.$row['key'].'">'.$row['title'].'</a><br />'.$row['annotation'].'</div>'));
}



// TITLE
$tpl->assign('title', $sTitle);
$_t->assign('head_title', $sTitle);


// CLOSE
$_t->assign('ContentBlock', $tpl);
$tpl->reset();

// SEO
$_t->assign('seo_title', $sSeoTitle);
$_t->assign('seo_keywords', $sSeoKeywords);
$_t->assign('seo_description', $sSeoDescription);


?>