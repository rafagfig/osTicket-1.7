<?php
/*********************************************************************
    upgrade.php

    osTicket Upgrade Wizard

    Peter Rotich <peter@osticket.com>
    Copyright (c)  2006-2012 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require_once 'admin.inc.php';
require_once INCLUDE_DIR.'class.upgrader.php';

//$_SESSION['ost_upgrader']=null;
$upgrader = new Upgrader($cfg->getSchemaSignature(), TABLE_PREFIX, SQL_DIR);
$errors=array();
if($_POST && $_POST['s'] && !$upgrader->isAborted()) {
    switch(strtolower($_POST['s'])) {
        case 'prereq':
            if(!$ost->isUpgradePending()) {
                $errors['err']=_(' Nothing to do! System already upgraded to the current version');
            } elseif(!$upgrader->isUpgradable()) {
                $errors['err']=_('The upgrader does NOT support upgrading from the current vesion!');
            } elseif(!$upgrader->check_prereq()) {
                $errors['prereq']=_('Minimum requirements not met! Refer to Release Notes for more information');
            } elseif(!strcasecmp(basename(CONFIG_FILE), 'settings.php')) {
                $errors['err']=_('Config file rename required to continue!');
            } else {
                $upgrader->setState('upgrade');
            } 
            break;
        case 'upgrade': //Manual upgrade.... when JS (ajax) is not supported.
            if($upgrader->getNumPendingTasks()) {
                $upgrader->doTasks();
            } elseif($ost->isUpgradePending() && $upgrader->isUpgradable()) {
                $upgrader->upgrade();
            } elseif(!$ost->isUpgradePending()) {
                $upgrader->setState('done');
            }

            if(($errors=$upgrader->getErrors()))  {
                $upgrader->setState('aborted');
            }
            break;
        default:
            $errors['err']=_('Unknown action!');
    }
}

switch(strtolower($upgrader->getState())) {
    case 'aborted':
        $inc='aborted.inc.php';
        break;
    case 'upgrade':
        $inc='upgrade.inc.php';
        break;
    case 'done':
        $inc='done.inc.php';
        break;
    default:
        $inc='prereq.inc.php';
        if($upgrader->isAborted())
            $inc='aborted.inc.php';
        elseif(!strcasecmp(basename(CONFIG_FILE), 'settings.php'))
            $inc='rename.inc.php';
        elseif(!$ost->isUpgradePending())
            $errors['err']=_('Nothing to do! System already upgraded to').' <b>'.$ost->getVersion().'</b> '._('with no pending patches to apply.');
        elseif(!$upgrader->isUpgradable())
            $errors['err']=sprintf(_('The upgrader does NOT support upgrading from the current patch [%s]!'), $cfg->getSchemaSignature());

}

$nav = new AdminNav($thisstaff);
$nav->setTabActive('dashboard');
$nav->addSubMenu(array('desc'=>_('Upgrader'),
                           'title'=>_('Upgrader'),
                           'href'=>'upgrade.php',
                           'iconclass'=>'preferences'),
                        true);
$ost->addExtraHeader('<script type="text/javascript" src="./js/upgrader.js"></script>');
require(STAFFINC_DIR.'header.inc.php');
require(UPGRADE_DIR.$inc);
require(STAFFINC_DIR.'footer.inc.php');
?>
