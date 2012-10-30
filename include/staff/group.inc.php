<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die(_('Access Denied'));
$info=array();
$qstr='';
if($group && $_REQUEST['a']!='add'){
    $title=_('Update Group');
    $action='update';
    $submit_text=_('Save Changes');
    $info=$group->getInfo();
    $info['id']=$group->getId();
    $info['depts']=$group->getDepartments();
    $qstr.='&id='.$group->getId();
}else {
    $title=_('Add New Group');
    $action='create';
    $submit_text=_('Create Group');
    $info['isactive']=isset($info['isactive'])?$info['isactive']:1;
    $info['can_create_tickets']=isset($info['can_create_tickets'])?$info['can_create_tickets']:1;
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="groups.php?<?php echo $qstr; ?>" method="post" id="save" name="group">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2><?=_('User Group')?></h2>
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
                <em><strong><?= _('Group Information')?></strong>: <?= _('Disabled group will limit staff members access. Admins are exempted.')?></em>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td width="180" class="required">
               <?= _('Name')?>:
            </td>
            <td>
                <input type="text" size="30" name="name" value="<?php echo $info['name']; ?>">
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['name']; ?></span>
            </td>
        </tr>
        <tr>
            <td width="180" class="required">
                <?= _('Status')?>:
            </td>
            <td>
                <input type="radio" name="isactive" value="1" <?php echo $info['isactive']?'checked="checked"':''; ?>><strong><?= _('Active')?></strong>
                <input type="radio" name="isactive" value="0" <?php echo !$info['isactive']?'checked="checked"':''; ?>><strong><?= _('Disabled')?></strong>
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['status']; ?></span>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?= _('Group Permissions')?></strong>: <?= _('Applies to all group members')?>&nbsp;</em$>
            </th>
        </tr>
        <tr><td><?= _('Can')?> <b> <?= _('Create')?></b> <?= _('Tickets')?></td>
            <td>
                <input type="radio" name="can_create_tickets"  value="1"   <?php echo $info['can_create_tickets']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_create_tickets"  value="0"   <?php echo !$info['can_create_tickets']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to open tickets on behalf of clients.')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can')?> <b><?= _('Edit')?></b> <?= _('Tickets')?></td>
            <td>
                <input type="radio" name="can_edit_tickets"  value="1"   <?php echo $info['can_edit_tickets']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_edit_tickets"  value="0"   <?php echo !$info['can_edit_tickets']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to edit tickets.')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can')?> <b><?= _('Close')?></b> <?= _('Tickets')?></td>
            <td>
                <input type="radio" name="can_close_tickets"  value="1" <?php echo $info['can_close_tickets']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_close_tickets"  value="0" <?php echo !$info['can_close_tickets']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to close tickets. Staff can still post a response.')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can')?> <b><?= _('Assign')?></b> <?= _('Tickets')?></td>
            <td>
                <input type="radio" name="can_assign_tickets"  value="1" <?php echo $info['can_assign_tickets']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_assign_tickets"  value="0" <?php echo !$info['can_assign_tickets']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to assign tickets to staff members.')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can')?> <b> <?= _('Transfer')?></b> <?= _('Tickets')?></td>
            <td>
                <input type="radio" name="can_transfer_tickets"  value="1" <?php echo $info['can_transfer_tickets']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_transfer_tickets"  value="0" <?php echo !$info['can_transfer_tickets']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to transfer tickets between departments.')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can')?> <b> <?= _('Delete')?></b> <?= _('Tickets')?></td>
            <td>
                <input type="radio" name="can_delete_tickets"  value="1"   <?php echo $info['can_delete_tickets']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_delete_tickets"  value="0"   <?php echo !$info['can_delete_tickets']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Deleted tickets can\'t be recovered!')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can Ban Emails')?></td>
            <td>
                <input type="radio" name="can_ban_emails"  value="1" <?php echo $info['can_ban_emails']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_ban_emails"  value="0" <?php echo !$info['can_ban_emails']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to add/remove emails from banlist via ticket interface.')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can Manage Premade')?></td>
            <td>
                <input type="radio" name="can_manage_premade"  value="1" <?php echo $info['can_manage_premade']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_manage_premade"  value="0" <?php echo !$info['can_manage_premade']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to add/update/disable/delete canned responses and attachments.')?></i>
            </td>
        </tr>
        <tr><td><?= _('Can Manage FAQ')?></td>
            <td>
                <input type="radio" name="can_manage_faq"  value="1" <?php echo $info['can_manage_faq']?'checked="checked"':''; ?> /><?= _('Yes')?>
                &nbsp;&nbsp;
                <input type="radio" name="can_manage_faq"  value="0" <?php echo !$info['can_manage_faq']?'checked="checked"':''; ?> /><?= _('No')?>
                &nbsp;&nbsp;<i><?= _('Ability to add/update/disable/delete knowledgebase categories and FAQs.')?></i>
            </td>
        </tr>
        <tr>
            <th colspan="2">
                <em><strong><?= _('Department Access')?></strong>: <?= _('Check all departments the group members are allowed to access.')?>&nbsp;&nbsp;&nbsp;<a id="selectAll" href="#deptckb"><?= _('Select All')?></a>&nbsp;&nbsp;<a id="selectNone" href="#deptckb"><?= _('Select None')?></a>&nbsp;&nbsp;</em>
            </th>
        </tr>
        <?php
         $sql='SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name';
         if(($res=db_query($sql)) && db_num_rows($res)){
            while(list($id,$name) = db_fetch_row($res)){
                $ck=($info['depts'] && in_array($id,$info['depts']))?'checked="checked"':'';
                echo sprintf('<tr><td colspan=2>&nbsp;&nbsp;<input type="checkbox" class="deptckb" name="depts[]" value="%d" %s>%s</td></tr>',$id,$ck,$name);
            }
         }
        ?>
        <tr>
            <th colspan="2">
                <em><strong><?= _('Admin Notes')?></strong>: <?= _('Internal notes viewable by all admins.')?>&nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea name="notes" cols="21" rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:225px;">
    <input type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input type="reset"  name="reset"  value="<?= _('Reset')?>">
    <input type="button" name="cancel" value="<?= _('Cancel')?>" onclick='window.location.href="groups.php"'>
</p>
</form>
