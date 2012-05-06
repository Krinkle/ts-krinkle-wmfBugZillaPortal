<?php
/**
 * wmfBugZillaPortal: Portal for Wikimedia's BugZilla
 * Created on June 2, 2012
 *
 * @author Timo Tijhof <krinklemail@gmail.com>, 2012
 * @license CC-BY-SA 3.0 Unported: creativecommons.org/licenses/by/3.0/
 */

/**
 * Configuration
 * -------------------------------------------------
 */
// BaseTool & Localization
require_once( '../ts-krinkle-basetool/InitTool.php' );
require_once( KR_TSINT_START_INC );

$I18N = new TsIntuition( 'general' );

$toolConfig = array(
	'displayTitle'     => 'wmfBugZillaPortal',
	'remoteBasePath'   => $kgConf->getRemoteBase() . '/wmfBugZillaPortal/',
	'localBasePath'    => __DIR__,
	'revisionId'       => '0.1.5',
	'revisionDate'     => '2012-05-05',
	'I18N'             => $I18N,
	'styles'           => array(
		'main.css',
	),
);

$Tool = BaseTool::newFromArray( $toolConfig );
$Tool->setSourceInfoGithub( 'Krinkle', 'ts-krinkle-wmfBugZillaPortal' );

$Tool->doHtmlHead();
$Tool->doStartBodyWrapper();



/**
 * Settings
 * -------------------------------------------------
 */
$bugZillaStuff = array(
	'mediawiki' => array(
		'versions' => array(
			'1.20-git',
			'1.19.0rc1',
			'1.19beta2',
			'1.19beta1',
			'1.19.0',
			'1.19',
			'1.18.3',
			'1.18.2',
			'1.18.1',
			'1.17.4',
			'1.17.3',
			'1.17.2',
			'1.17.1',
			'1.17.0rc1',
			'1.17.0beta1',
			'1.17.0',
			'1.17',
			'unspecified',
		),
		// re-used for mediawiki-extensions
		'milestones' => array(
			'1.20.0 release',
			'1.19.x release',
			'1.19.0 release',
			'1.18.x release',
			'1.18.0 release',
			'Future release',
		),
	),
	'wikimedia' => array(
		'deployment' => array(
			// Map Wikimedia deployment milestones to the tracker bug for MediaWiki bugs
			'1.20wmf3' => null,
			'1.20wmf2' => '36465',
			'1.20wmf1' => '36464',
			'1.19wmf1' => '31217',
			'1.18wmf1' => '29068',
		),
	),
);

function wbpBuglistLinks( $buglistQuery, $label ) {
	return  '('
	. Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/buglist.cgi?' . http_build_query(array(
				'resolution' => '---',
			) + $buglistQuery),
			'target' => '_blank',
			'title' => "Unresolved bugs $label"
		), 'unresolved'
	)

	. ' &bull; '
	. Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/buglist.cgi?' . http_build_query(array(
				'resolution' => '---',
				'keywords' => 'code-update-regression',
				'keywords_type' => 'anywords',
			) + $buglistQuery),
			'target' => '_blank',
			'title' => "Unresolved regressions $label"
		), 'regr.'
	)

	. ' &bull; '
	. Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/buglist.cgi?' . http_build_query($buglistQuery),
			'target' => '_blank',
			'title' => "All bugs $label"
		), 'all'
	)
	. ')';
}

function wbpTrackingBugLinks( $bugID ) {
	return  Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/show_bug.cgi?' . http_build_query(array(
				'id' => $bugID
			)),
			'target' => '_blank',
			'title' => "bug $bugID"
		), "bug $bugID"
	)
	. ' ('
	. Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/showdependencytree.cgi?' . http_build_query(array(
				'id' => $bugID,
				'hide_resolved' => 1,
			)),
			'target' => '_blank',
			'title' => "dependency tree for bug $bugID"
		), 'dependency tree'
	)
	. ')';
}


$Tool->addOut( '<small>'
	. '<a href="#mediawiki-core">MediaWiki core</a>'
	. ' <b>&bull;</b> '
	. '<a href="#wmf">Wikimedia</a>'
	. ' <b>&bull;</b> '
	. '<a href="#mediawiki-extensions">MediaWiki extensions</a>'
	. '</small><hr/>'
);


$Tool->addOut( 'MediaWiki core', 'h2', array( 'id' => 'mediawiki-core' ) );
$html = '<table class="wikitable krinkle-wmfBugZillaPortal-overview">'
	. '<thead><tr><th>Version</th><th>Target Milestone</th></tr></thead>'
	. '<tbody><tr>';

// Versions
$html .= '<td><p>Bugs new in a MediaWiki version</p><ul>';
foreach ( $bugZillaStuff['mediawiki']['versions'] as $mwVersion ) {
	$html .= "<li>{$mwVersion} "
	. wbpBuglistLinks(
		array(
			'query_format' => 'advanced',
			'product' => 'MediaWiki',
			'version' => $mwVersion,
		),
		'new in MediaWiki ' . $mwVersion
	)
	. '</li>';
}
$html .= '</ul></td>';

// Milestones
$html .= '<td><p>Tickets targeted for a MediaWiki milestone</p><ul>';
foreach ( $bugZillaStuff['mediawiki']['milestones'] as $mwMilestone ) {
	$html .= "<li>{$mwMilestone} "
	. wbpBuglistLinks(
		array(
			'query_format' => 'advanced',
			'product' => 'MediaWiki',
			'target_milestone' => $mwMilestone,
		),
		'targeted for MediaWiki ' . $mwMilestone
	)
	. '</li>';
}
$html .= '</ul></td>';

$html .= '</tr></tbody></table>';

$Tool->addOut( $html );


$Tool->addOut( 'Wikimedia', 'h2', array( 'id' => 'wmf' ) );
$html = '<table class="wikitable krinkle-wmfBugZillaPortal-overview krinkle-wmfBugZillaPortal-overview-wm">'
	. '<thead><tr><th>Deployment milestone</th><th>MediaWiki core/extensions (tracking)</th></tr></thead>'
	. '<tbody>';

// Deployment
$html .= '<tr>'
	. '<td>General tasks in "Wikimedia" category for a deployment milestone</td>'
	. '<td><p>Tickets in MediaWiki core/extensions<br> blocking this deployment</td>'
	. '</tr>';

foreach ( $bugZillaStuff['wikimedia']['deployment'] as $wmDeploy => $mwTrackingBug ) {
	$html .= "<tr><td>{$wmDeploy} "
	. wbpBuglistLinks(
		array(
			'query_format' => 'advanced',
			'product' => 'Wikimedia',
			'target_milestone' => "$wmDeploy deployment",
		),
		'tasks for ' . $wmDeploy
	)
	. '</td><td>';
	$html .= $mwTrackingBug ? wbpTrackingBugLinks( $mwTrackingBug ) : '<em>(no tracking bug yet)</em>';
	$html .= '</td></tr>';
}

$html .= '</tbody></table>';

$Tool->addOut( $html );

$Tool->addOut( 'MediaWiki extensions', 'h2', array( 'id' => 'mediawiki-extensions' ) );
$html = '<table class="wikitable krinkle-wmfBugZillaPortal-overview">'
	. '<thead><tr><th>Target Milestone</th></tr></thead>'
	. '<tbody><tr>';

// Milestones
$html .= '<td><p>Tickets targeted for a MediaWiki milestone</p><ul>';
foreach ( $bugZillaStuff['mediawiki']['milestones'] as $mwMilestone ) {
	$html .= "<li>{$mwMilestone} "
	. wbpBuglistLinks(
		array(
			'query_format' => 'advanced',
			'product' => 'MediaWiki extensions',
			'target_milestone' => $mwMilestone,
		),
		'targeted for MediaWiki ' . $mwMilestone
	)
	. '</li>';
}
$html .= '</ul></td>';

$html .= '</tr></tbody></table>';

$Tool->addOut( $html );


/**
 * Close up
 * -------------------------------------------------
 */
$Tool->flushMainOutput();
