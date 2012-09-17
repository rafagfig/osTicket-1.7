<?php
if(!defined('OSTADMININC') || !$thisstaff || !$thisstaff->isAdmin()) die(_('Access Denied'));

$qstr='';
if($_REQUEST['type']) {
    $qstr.='&amp;type='.urlencode($_REQUEST['type']);
}
$type=null;
switch(strtolower($_REQUEST['type'])){
    case 'error':
        $title=_('Errors');
        $type=$_REQUEST['type'];
        break;
    case 'warning':
        $title=_('Warnings');
        $type=$_REQUEST['type'];
        break;
    case 'debug':
        $title=_('Debug logs');
        $type=$_REQUEST['type'];
        break;
    default:
        $type=null;
        $title=_('All logs');
}

$qwhere =' WHERE 1';
//Type
if($type)
    $qwhere.=' AND log_type='.db_input($type);

//dates
$startTime  =($_REQUEST['startDate'] && (strlen($_REQUEST['startDate'])>=8))?strtotime($_REQUEST['startDate']):0;
$endTime    =($_REQUEST['endDate'] && (strlen($_REQUEST['endDate'])>=8))?strtotime($_REQUEST['endDate']):0;
if( ($startTime && $startTime>time()) or ($startTime>$endTime && $endTime>0)){
    $errors['err']=_('Entered date span is invalid. Selection ignored.');
    $startTime=$endTime=0;
}else{
    if($startTime){
        $qwhere.=' AND created>=FROM_UNIXTIME('.$startTime.')';
        $qstr.='&startDate='.urlencode($_REQUEST['startDate']);
    }
    if($endTime){
        $qwhere.=' AND created<=FROM_UNIXTIME('.$endTime.')';
        $qstr.='&endDate='.urlencode($_REQUEST['endDate']);
    }
}
$sortOptions=array('id'=>'log.log_id', 'title'=>'log.title','type'=>'log_type','ip'=>'log.ip_address'
                    ,'date'=>'log.created','created'=>'log.created','updated'=>'log.updated');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'id';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'log.created';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}
$order=$order?$order:'DESC';

if($order_column && strpos($order_column,',')){
    $order_column=str_replace(','," $order,",$order_column);
}
$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$qselect = 'SELECT log.* ';
$qfrom=' FROM '.SYSLOG_TABLE.' log ';
$total=db_count("SELECT count(*) $qfrom $qwhere");
$page = ($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
//pagenate
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$pageNav->setURL('logs.php',$qstr);
$qstr.='&order='.($order=='DESC'?'ASC':'DESC');
$query="$qselect $qfrom $qwhere ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
$res=db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing=$pageNav->showing().' '.$title;
else
    $showing=_('No logs found!');
?>

<h2><?= _('System Logs')?></h2>
<div id='filter' >
 <form action="logs.php" method="get">
    <div style="padding-left:2px;">
        <b><?= _('Date Span')?></b>:
        &nbsp;<?= _('From')?>&nbsp;<input class="dp" id="sd" size=15 name="startDate" value="<?php echo Format::htmlchars($_REQUEST['startDate']); ?>" autocomplete=OFF>
            &nbsp;&nbsp; <?= _('to')?> &nbsp;&nbsp;
            <input class="dp" id="ed" size=15 name="endDate" value="<?php echo Format::htmlchars($_REQUEST['endDate']); ?>" autocomplete=OFF>
            &nbsp;&nbsp;
            &nbsp;<?= _('Type')?>:
            <select name='type'>
                <option value="" selected>All</option>
                <option value="Error" <?php echo ($type=='Error')?'selected="selected"':''; ?>><?= _('Errors')?></option>
                <option value="Warning" <?php echo ($type=='Warning')?'selected="selected"':''; ?>><?= _('Warnings')?></option>
                <option value="Debug" <?php echo ($type=='Debug')?'selected="selected"':''; ?>><?= _('Debug')?></option>
            </select>
            &nbsp;&nbsp;
            <input type="submit" Value="<?= _('Go!')?>" />
    </div>
 </form>
</div>
<form action="logs.php" method="POST" name="logs" onSubmit="return checkbox_checker(this,1,0);">
<?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <table class="list" border="0" cellspacing="1" cellpadding="0" width="940">
    <caption><?php echo $showing; ?></caption>
    <thead>
        <tr>
            <th width="7">&nbsp;</th>        
            <th width="320"><a <?php echo $title_sort; ?> href="logs.php?<?php echo $qstr; ?>&sort=title"><?= _('Log Title')?></a></th>
            <th width="100"><a  <?php echo $type_sort; ?> href="logs.php?<?php echo $qstr; ?>&sort=type"><?= _('Log Type')?></a></th>
            <th width="200" nowrap><a  <?php echo $date_sort; ?>href="logs.php?<?php echo $qstr; ?>&sort=date"><?= _('Log Date')?></a></th>
            <th width="120"><a  <?php echo $ip_sort; ?> href="logs.php?<?php echo $qstr; ?>&sort=ip"><?= _('IP Address')?></a></th>
        </tr>
    </thead>
    <tbody>
    <?php
        $total=0;
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($res && db_num_rows($res)):
            while ($row = db_fetch_array($res)) {
                $sel=false;
                if($ids && in_array($row['log_id'],$ids)){
                    $class="$class highlight";
                    $sel=true;
                }
                ?>
            <tr id="<?php echo $row['log_id']; ?>">
                <td width=7px>
                  <input type="checkbox" name="ids[]" value="<?php echo $row['log_id']; ?>" 
                            <?php echo $sel?'checked="checked"':''; ?> onClick="highLight(this.value,this.checked);"> </td>
                <td>&nbsp;<a class="tip" href="log/<?php echo $row['log_id']; ?>"><?php echo Format::htmlchars($row['title']); ?></a></td>
                <td><?php echo _($row['log_type']); ?></td>
                <td>&nbsp;<?php echo Format::db_daydatetime($row['created']); ?></td>
                <td><?php echo $row['ip_address']; ?></td>
            </tr>
            <?php
            } //end of while.
        endif; ?>
    </tbody>
    <tfoot>
     <tr>
        <td colspan="6">
            <?php if($res && $num){ ?>
            <?= _('Select')?>:&nbsp;
            <a href="#" onclick="return select_all(document.forms['logs'],true)"><?= _('All')?></a>&nbsp;&nbsp;
            <a href="#" onclick="return reset_all(document.forms['logs'])"><?= _('None')?></a>&nbsp;&nbsp;
            <a href="#" onclick="return toogle_all(document.forms['logs'],true)"><?= _('Toggle')?></a>&nbsp;&nbsp;
            <?php }else{
                echo _('No logs found');
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if($res && $num): //Show options..
    echo '<div>&nbsp;'._('Page').':'.$pageNav->getPageLinks().'&nbsp;</div>';
?>
<p class="centered">
    <input class="button" type="submit" name="delete" value="<?= _('Delete Selected Entries')?>"
                onClick=' return confirm("<?= _('Are you sure you want to DELETE selected log entries?')?>");'>
</p>
<?php
endif;
?>
</form>
