<?php

// ADD_BREADCRUMBS
$BreadCrumbs->addBreadCrumbs($sKey, $sTitle);



if (!$bFlagLastModule) return;



// HEAD_TITLE
$_t->assign('head_title', $sTitle);

// BREADCRUMBS
$_t->assign('BreadCrumbs', $BreadCrumbs->getBreadCrumbs());



// OPEN
$tpl = $_t->fetchBlock('ContentBlock');


// TITLE
$tpl->assign('title', $sTitle);


// SAPE
if (defined("_SAPE_USER")) {
  $tpl->assign('content', $sape_context->replace_in_text_segment(crbr($sText)));
  $tpl->assign('my_links', $sape->return_links());
} else {
  $tpl->assign('content', crbr($sText));
  $tpl->assign('my_links', '');
}


// ANNOTATIONS
$sql = "SELECT `id`, `key`, `title`, `annotation`, `draft`
		FROM `".DB_PREFIX.DB_TBL_PAGES."`
		WHERE (parent = ".$nId.")
		ORDER BY `order`";
$sql = mysql_query($sql);
if (false == $sql) my_die();
$aPage = array();
while ($row = mysql_fetch_assoc($sql)) {
	$aPage[] = $row;
}
// ��������� ������� ��������� ��������, ���� ����
foreach ($aPage as $k=>$v) {
	if ($v['draft'] == 1) {
		$aTree = array();
		getSubVersionsRecursive($v['id']);
		foreach ($aTree as $kk=>$vv) {
			if ($vv['draft'] == 0) {
				$aPage[$k] = $vv;
				break;
			}
		}
	}
}
// �������
foreach ($aPage as $k=>$v) {
	if ($v['draft'] == 0) {
		$tpl->assign('content', crbr('<div style="margin-bottom: 8px;"><a href="'.$sRequest.'/'.$v['key'].'">'.$v['title'].'</a><br />'.$v['annotation'].'</div>'));
	}
}



// CLOSE
$_t->assign('ContentBlock', $tpl);
$tpl->reset();

// SEO
$_t->assign('seo_title', $sSeoTitle);
$_t->assign('seo_keywords', $sSeoKeywords);
$_t->assign('seo_description', $sSeoDescription);


?>
