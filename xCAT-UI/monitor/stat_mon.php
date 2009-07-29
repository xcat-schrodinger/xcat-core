<?php
/* 
 * This web page is for "Node status Monitoring" and "Application Status Monitioring",
 * The user can enable/disable "Node/Application Status Monitoring" from this page.
 */
if(!isset($TOPDIR)) { $TOPDIR="/opt/xcat/ui";}

require_once "$TOPDIR/lib/security.php";
require_once "$TOPDIR/lib/functions.php";
require_once "$TOPDIR/lib/display.php";
require_once "$TOPDIR/lib/monitor_display.php";

//get the name of the selected plug-in
$name = $_REQUEST['name'];

?>
<script type="text/javascript">
function node_stat_control(plugin)
{
    //get the label of the button
    var action = $("#node_stat span").text();
    if(action=='Enable') {
        //enable node_stat_monitor
        $.get("monitor/control_node_stat.php",{name:plugin, action:"enable"},function(data) {
            if(data=='successful') {
                //change the label to "Disable"
                $("#node_stat span").text("Disable");
            }
        });
    }else if(action=='Disable') {
        //disable node_stat_monitor
        $.get("monitor/control_node_stat.php",{name:plugin, action:"disable"},function(data) {
            if(data=='successful') {
                //change the label to "enable"
                $("#node_stat span").text("Enable");
            }
        })
        //then, change the label to "Enable""
    }
}
</script>

<?php
displayMapper_mon(array('home'=>'main.php', 'monitor'=>'monitor/monlist.php'));
displayTips(array(
    "Enable/disable Node/App Status Monitoring by clicking the button",
    "Click the \"Next\" button to define Events for the desired plug-in"));

//get the current status for "node-status-monitor"
$xml = docmd("monls", ' ', array($name));
if(getXmlErrors($xml,$errors)) {
    echo "<p class=Error>",implode(' ',$errors), "</p>";
    exit;
}
#then, parse the xml data
foreach($xml->children() as $response) foreach($response->children() as $data) {
    list($n, $stat, $nodemonstatus) = preg_split("/\s+/",$data);
    if(isset($nodemonstatus)) {
        $ns = "Enabled";
    }else {
        $ns = "Disabled";
    }
}

display_stat_mon_table(array("$name"=>
        array(
            'nodestat'=>$ns,
            'appstat'=>'Disabled',  //currently application status monitoring is not supported by xCAT monitor Arch.
        )));

displayStatus();

insertButtons(array('label'=>'Next', id=>'next', 'onclick'=>''));
?>
