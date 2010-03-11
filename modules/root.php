<?php

// SPECAL MODULES

if ($bFlagLastModule) {
	if ($bFlag404) {
		foreach ($aSpecialModules as $v) {
			if ($aRequest[1] == $v) {
				include_once(FLGR_CMS_MODULES.'/'.$v.'.php');
			}
		}
	}
}


// login
if ((isset($_POST['act'])) && ($_POST['act'] == 'login')) {
	$Permissions->login($_POST['login'], $_POST['password']);
	header('Location: '.$_SERVER['HTTP_REFERER']);
	include(FLGR_COMMON.'/exit.php');
}


/* define('TOP_MENU_ID', 20); */
/* define('TOP_MENU_KEY', 'info'); */
/* define('TOP_MENU_LEVEL', 1); */
/* define('SHOW_MENU_LEVELS', 3); */
/* foreach (aGetMenu() as $k=>$v) { */

/* 	if ( 	(isset($v['level'][TOP_MENU_LEVEL])) && */
/* 			($v['level'][TOP_MENU_LEVEL] == TOP_MENU_KEY) && */
/* 			($v['parent'] == TOP_MENU_ID) ) { */
/* 		$tplMenuElt = $_t->fetchBlock('TopMenuElt'); */
/* 		$tplMenuElt->assign('title_menu', $v['title_menu']); */
/* 		$link = implode('/', $v['level']); */
/* 		if ($link == '') { $link = '/';	} */
/* 		$tplMenuElt->assign('link', $link); */
/* 		$_t->assign('TopMenuElt', $tplMenuElt); */
/* 		$tplMenuElt->reset(); */
/* 	} elseif ($v['id'] != TOP_MENU_ID) { */
/* 		if (count($v['level']) <= SHOW_MENU_LEVELS) { */
/* 			$tplMenuElt = $_t->fetchBlock('MainMenuElt'); */
/* 			$tplMenuElt->assign('key', $v['key']); */
/* 			$tplMenuElt->assign('title', $v['title']); */
/* 			$tplMenuElt->assign('level', count($v['level'])*15); */
/* 			$tplMenuElt->assign('if_admin', ''); */
/* 			$link = implode('/', $v['level']); */
/* 			if ($link == '') { $link = '/';	} */
/* 			$tplMenuElt->assign('link', $link); */
/* 			$_t->assign('MainMenuElt', $tplMenuElt); */
/* 			$tplMenuElt->reset(); */
/* 		} */
/* 	} */

/* } */

if (!$Permissions->bIsLogged()) {
	$_t->assign('logon', $_t->fetchBlock('logon'));
	$_t->assign('logout', '');
} else {
	$_t->assign('logon', '');
	$tplLogout = $_t->fetchBlock('logout');
	$tplLogout->assign('user_name', $_SESSION['user']['name']);
	$_t->assign('logout', $tplLogout);
}

// ADD_BREADCRUMBS
$BreadCrumbs->addBreadCrumbs($sKey, $sTitle);



if (!$bFlagLastModule) return;


// BREADCRUMBS
$_t->assign('BreadCrumbs', $BreadCrumbs->getBreadCrumbs());



// HEAD_TITLE
$_t->assign('head_title', HEAD_TITLE);


// OPEN
if ($sModuleTpl != '') {
	$tpl = new KTemplate(FLGR_TEMPLATES.'/'.$sModuleTpl.'.htm');
} else {
	$tpl = $_t->fetchBlock('ContentBlock');
}


// SAPE
if (defined("_SAPE_USER")) {
  /* $tpl->assign('content', $sape_context->replace_in_text_segment(crbr($sText))); */
  $tpl->assign('my_links', $sape->return_links());
} else {
  /* $tpl->assign('content', crbr($sText)); */
  $tpl->assign('my_links', '');
}


// CLOSE
$_t->assign('ContentBlock', $tpl);
$tpl->reset();


// SEO
$_t->assign('seo_title', $sSeoTitle);
$_t->assign('seo_keywords', $sSeoKeywords);
$_t->assign('seo_description', $sSeoDescription);


?>