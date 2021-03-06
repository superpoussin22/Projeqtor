<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2015 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 *
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/

/* ============================================================================
 * Presents the list of objects of a given class.
 *
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/view/planningList.php');

$startDate=date('Y-m-d');
$endDate=null;
$user=getSessionUser();
$saveDates=false;
$paramStart=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningStartDate'));
if ($paramStart->id) {
  $startDate=$paramStart->parameterValue;
  $saveDates=true;
}
$paramEnd=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningEndDate'));
if ($paramEnd->id) {
  $endDate=$paramEnd->parameterValue;
  $saveDates=true;
}
//$saveShowWbsObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowWbs'));
//$saveShowWbs=$saveShowWbsObj->parameterValue;
$saveShowWbs=Parameter::getUserParameter('planningShowWbs');
//$saveShowResourceObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowResource'));
//$saveShowResource=$saveShowResourceObj->parameterValue;
$saveShowResource=Parameter::getUserParameter('planningShowResource');
//$saveShowWorkObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowWork'));
//$saveShowWork=$saveShowWorkObj->parameterValue;
$saveShowWork=Parameter::getUserParameter('planningShowWork');
//$saveShowClosedObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowClosed'));
//$saveShowClosed=$saveShowClosedObj->parameterValue;
$saveShowClosed=Parameter::getUserParameter('planningShowClosed');
if ($saveShowClosed) {
	$_REQUEST['idle']=true;
}
$automaticRunPlanning=Parameter::getUserParameter('automaticRunPlanning');

$plannableProjectsList=getSessionUser()->getListOfPlannableProjects();
$canPlan=(count($plannableProjectsList)>0)?true:false;
//$objectClass='Task';
//$obj=new $objectClass;
?>
  
<div id="mainPlanningDivContainer" dojoType="dijit.layout.BorderContainer">
	<div dojoType="dijit.layout.ContentPane" region="top" id="listHeaderDiv" height="27px"
	 style="z-index: 3; position: relative; overflow: visible !important;">
		<table width="100%" height="27px" class="listTitle" >
		  <tr height="27px">
		    <td width="50px" align="center">
		      <span style="position:absolute; left:10px; top:7px">
            <img src="css/images/iconPlanning32.png" width="32" height="32" />
          </span>
		    </td>
		    <td><span class="title"><?php echo i18n('menuPlanning');?></span><br/></td>
		    <td>   
		      <form dojoType="dijit.form.Form" id="listForm" action="" method="" >
		        <table style="width: 100%;">
		          <tr>
		            <td style="width:70px">
		              <input type="hidden" id="objectClass" name="objectClass" value="" /> 
		              <input type="hidden" id="objectId" name="objectId" value="" />
		              &nbsp;&nbsp;&nbsp;
<?php if ($canPlan) { ?>
		              <button id="planButton" dojoType="dijit.form.Button" showlabel="false"
		                title="<?php echo i18n('buttonPlan');?>"
		                iconClass="iconPlanStopped" >
		                <script type="dojo/connect" event="onClick" args="evt">
                     showPlanParam();
                     return false;
                    </script>
		              </button>
<?php $showAutoPlan=true;?>
                  <div style="white-space:nowrap;<?php if (!isset($showAutoPlan) or $showAutoPlan!=true) echo 'visibility:hidden;'?>">
		              <span title="<?php echo i18n('automaticRunPlanHelp');?>" dojoType="dijit.form.CheckBox"
                        type="checkbox" id="automaticRunPlan" name="automaticRunPlan" class="whiteCheck"
                        <?php if ( $automaticRunPlanning) {echo 'checked="checked"'; } ?>  >  
                        <script type="dojo/connect" event="onChange" args="evt">
                          saveUserParameter('automaticRunPlanning',((this.checked)?'1':'0'));
                        </script>                    
                  </span>&nbsp;<?php echo i18n('automaticRunPlan')?>
                  </div>
<?php }?>             
		            </td>
		            <td style="white-space:nowrap;width:240px">
		              <table>
                    <tr>
                      <td align="right">&nbsp;&nbsp;&nbsp;<?php echo i18n("displayStartDate");?>&nbsp;&nbsp;</td><td>
                        <div dojoType="dijit.form.DateTextBox"
                        	<?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
														echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
													}?>
                           id="startDatePlanView" name="startDatePlanView"
                           invalidMessage="<?php echo i18n('messageInvalidDate')?>"
                           type="text" maxlength="10" 
                           style="width:100px; text-align: center;" class="input roundedLeft"
                           hasDownArrow="true"
                           value="<?php echo $startDate;?>" >
                           <script type="dojo/method" event="onChange" >
                            refreshJsonPlanning();
                           </script>
                         </div>
                      </td>
                    </tr>
                    <tr>
                      <td align="right">&nbsp;&nbsp;&nbsp;<?php echo i18n("displayEndDate");?>&nbsp;&nbsp;</td>
                      <td>
                        <div dojoType="dijit.form.DateTextBox"
	                        <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
														echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
													}?>
                           id="endDatePlanView" name="endDatePlanView"
                           invalidMessage="<?php echo i18n('messageInvalidDate')?>"
                           type="text" maxlength="10"
                           style="width:100px; text-align: center;" class="input roundedLeft"
                           hasDownArrow="true"
                           value="<?php echo $endDate;?>" >
                           <script type="dojo/method" event="onChange" >
                            refreshJsonPlanning();
                           </script>
                        </div>
                      </td>
                    </tr>
                  </table>
		            </td>
                <td>
                  <table>
                    <tr>
                    <?php if ($canPlan) { ?>
                      <td width="32px">
                        <button id="savePlanningButton" dojoType="dijit.form.Button" showlabel="false"
                         title="<?php echo i18n('savePlannedDates');?>"
                         iconClass="dijitButtonIcon dijitButtonIconSavePlan" class="detailButton">
                         <script type="dojo/connect" event="onClick" args="evt">
		                      showPlanSaveDates();
                          return false;  
                         </script>
                        </button>
                      </td>
                    <?php }?>  
                      <td width="32px">
                        <button title="<?php echo i18n('printPlanning')?>"
                         dojoType="dijit.form.Button"
                         id="listPrint" name="listPrint"
                         iconClass="dijitButtonIcon dijitButtonIconPrint" class="detailButton" showLabel="false">
                          <script type="dojo/connect" event="onClick" args="evt">
<?php $ganttPlanningPrintOldStyle=Parameter::getGlobalParameter('ganttPlanningPrintOldStyle');
      if (!$ganttPlanningPrintOldStyle) {$ganttPlanningPrintOldStyle="NO";}
      if ($ganttPlanningPrintOldStyle=='YES') {?>
	                        showPrint("../tool/jsonPlanning.php", 'planning');
<?php } else { ?>
                          showPrint("planningPrint.php", 'planning');
<?php }?>                          
                          </script>
                        </button>
                      </td>
                      <td width="32px">
                        <button title="<?php echo i18n('reportPrintPdf')?>"
                         dojoType="dijit.form.Button"
                         id="listPrintPdf" name="listPrintPdf"
                         iconClass="dijitButtonIcon dijitButtonIconPdf" class="detailButton" showLabel="false">
                          <script type="dojo/connect" event="onClick" args="evt">
                          //showPrint("../tool/jsonPlanning.php", 'planning', null, 'pdf');
                           showPrint("../tool/jsonPlanning_pdf.php", 'planning', null, 'pdf');
                          </script>
                        </button>
                      </td>
                      <td width="32px">
                        <button title="<?php echo i18n('reportExportMSProject')?>"
                         dojoType="dijit.form.Button"
                         id="listPrintMpp" name="listPrintMpp"
                         iconClass="dijitButtonIcon dijitButtonIconMSProject" class="detailButton" showLabel="false">
                          <script type="dojo/connect" event="onClick" args="evt">
                          showPrint("../tool/jsonPlanning.php", 'planning', null, 'mpp');
                          </script>
                        </button>
                        <input type="hidden" id="outMode" name="outMode" value="" />
                      </td>
                      <td>
                       <div dojoType="dijit.form.DropDownButton"
                         id="planningColumnSelector" jsId="planningColumnSelector" name="planningColumnSelector"  
                             showlabel="false" class="comboButton" iconClass="dijitButtonIcon dijitButtonIconColumn" 
                             title="<?php echo i18n('columnSelector');?>">
                          <span>title</span>
                          <div dojoType="dijit.TooltipDialog" class="white" style="width:200px;">   
                            <script type="dojo/connect" event="onHide" args="evt">
                              if (dndMoveInProgress) { this.show(); }
                            </script>
                            <div id="dndPlanningColumnSelector" jsId="dndPlanningColumnSelector" dojotype="dojo.dnd.Source"  
                             dndType="column"
                             withhandles="true" class="container">    
                               <?php 
                                 include('../tool/planningColumnSelector.php')?>
                            </div>
                            <div style="height:5px;"></div>    
					                  <div style="text-align: center;"> 
					                    <button title="" dojoType="dijit.form.Button" 
					                      id="" name="" showLabel="true"><?php echo i18n('buttonOK');?>
					                      <script type="dojo/connect" event="onClick" args="evt">
                                  validatePlanningColumn();
                                </script>
					                    </button>
					                  </div>          
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" style="white-space:nowrap;">
                        <span title="<?php echo i18n('saveDates')?>" dojoType="dijit.form.CheckBox"
                           type="checkbox" id="listSaveDates" name="listSaveDates" class="whiteCheck"
                           <?php if ( $saveDates) {echo 'checked="checked"'; } ?>  >

                          <script type="dojo/method" event="onChange" >
                            refreshJsonPlanning();
                          </script>
                        </span>
                        <span for="listSaveDates"><?php echo i18n("saveDates");?></span>
                      </td>
                      <td>
                       <div dojoType="dijit.form.DropDownButton"
                             class="comboButton"   
                             id="planningNewItem" jsId="planningNewItem" name="planningNewItem" 
                             showlabel="false" class="" iconClass="dijitButtonIcon dijitButtonIconNew"
                             title="<?php echo i18n('comboNewButton');?>">
                          <span>title</span>
                          <div dojoType="dijit.TooltipDialog" class="white" style="width:200px;">   
                            <div style="font-weight:bold; height:25px;text-align:center">
                            <?php echo i18n('comboNewButton');?>
                            </div>
                            <?php $arrayItems=array('Project','Activity','Milestone');
                            foreach($arrayItems as $item) {?>
                            <div style="vertical-align:top;cursor:pointer;" 
                             onClick="addNewItem('<?php echo $item;?>');" >
                              <img src="../view/css/images/icon<?php echo $item;?>22.png" />&nbsp;&nbsp;    
                             <?php echo i18n($item)?>   
                            </div>
                            <div style="height:5px;"></div>
                            <?php }?>
                          </div>
                        </div>
                      </td>
                    </tr>
                  </table>
                </td>
		            <td>
                  <div id="planResultDiv" style="display:none" 
                    dojoType="dijit.layout.ContentPane" region="center" >
                  </div>
                </td>
		            <td style="text-align: right; align: right;">
		              <table width="100%"><tr><td>
                  <?php echo i18n("labelShowWbs");?>
                  </td><td width="35px">
		              <div title="<?php echo i18n('showWbs')?>" dojoType="dijit.form.CheckBox" 
                    class="whiteCheck" type="checkbox" id="showWBS" name="showWBS"
                    <?php if ($saveShowWbs=='1') { echo ' checked="checked" '; }?> >
		                <script type="dojo/method" event="onChange" >
                      saveUserParameter('planningShowWbs',((this.checked)?'1':'0'));
                      refreshJsonPlanning();
                    </script>
		              </div>&nbsp;
		              </td></tr><tr><td>
		              <?php echo i18n("labelShowIdle");?>
                  </td><td>
		              <div title="<?php echo i18n('showIdleElements')?>" dojoType="dijit.form.CheckBox" 
                     class="whiteCheck" type="checkbox" id="listShowIdle" name="listShowIdle"
                    <?php if ($saveShowClosed=='1') { echo ' checked="checked" '; }?> >
		                <script type="dojo/method" event="onChange" >
                      saveUserParameter('planningShowClosed',((this.checked)?'1':'0'));
                      refreshJsonPlanning();
                    </script>
		              </div>&nbsp;
                  </td></tr>
                  <?php if (strtoupper(Parameter::getGlobalParameter('displayResourcePlan'))!='NO') {?>
                  <tr><td>
                  <?php echo i18n("labelShowResource");?>
                  </td><td>
                  <div title="<?php echo i18n('showResources')?>" dojoType="dijit.form.CheckBox" 
                    class="whiteCheck" type="checkbox" id="listShowResource" name="listShowResource"
                    <?php if ($saveShowResource=='1') { echo ' checked="checked" '; }?> >
                    <script type="dojo/method" event="onChange" >
                      saveUserParameter('planningShowResource',((this.checked)?'1':'0'));
                      refreshJsonPlanning();
                    </script>
                  </div>&nbsp;
                  </td></tr>
                  <?php }?>
                  </table>
		            </td>
		          </tr>
		        </table>    
		      </form>
		    </td>
		  </tr>
		</table>
		<div id="listBarShow" onMouseover="showList('mouse')" onClick="showList('click');">
		  <div id="listBarIcon" align="center"></div>
		</div>
	
		<div dojoType="dijit.layout.ContentPane" id="planningJsonData" jsId="planningJsonData" 
     style="display: none">
		  <?php
		       if ($saveShowResource) $_REQUEST['showResource']='on';
            include '../tool/jsonPlanning.php';
          ?>
		</div>
	</div>
	<div dojoType="dijit.layout.ContentPane" region="center" id="gridContainerDiv">
   <div id="submainPlanningDivContainer" dojoType="dijit.layout.BorderContainer"
    style="border-top:1px solid #ffffff;">
    <?php $leftPartSize=Parameter::getUserParameter('planningLeftSize');
          if (! $leftPartSize) {$leftPartSize='325px';} ?>
	   <div dojoType="dijit.layout.ContentPane" region="left" splitter="true" 
      style="width:<?php echo $leftPartSize;?>; height:100%; overflow-x:scroll; overflow-y:hidden;" class="ganttDiv" 
      id="leftGanttChartDIV" name="leftGanttChartDIV"
      onScroll="dojo.byId('ganttScale').style.left=(this.scrollLeft)+'px'; this.scrollTop=0;" 
      onmousewheel="leftMouseWheel(event);">
      <script type="dojo/method" event="onUnload" >
         var width=this.domNode.style.width;
         setTimeout("saveUserParameter('planningLeftSize','"+width+"');",1);
         return true;
      </script>
     </div>
     <div dojoType="dijit.layout.ContentPane" region="center" 
      style="height:100%; overflow:hidden;" class="ganttDiv" 
      id="GanttChartDIV" name="GanttChartDIV" >
       <div id="mainRightPlanningDivContainer" dojoType="dijit.layout.BorderContainer" style="z-index:-4;">
         <div dojoType="dijit.layout.ContentPane" region="top" 
          style="width:100%; height:45px; overflow:hidden;" class="ganttDiv"
          id="topGanttChartDIV" name="topGanttChartDIV">
         </div>
         <div dojoType="dijit.layout.ContentPane" region="center" 
          style="z-index:-4; width:100%; overflow-x:scroll; overflow-y:scroll; position: relative; top:-10px;" class="ganttDiv"
          id="rightGanttChartDIV" name="rightGanttChartDIV"
          onScroll="dojo.byId('rightside').style.left='-'+(this.scrollLeft+1)+'px';
                    dojo.byId('leftside').style.top='-'+(this.scrollTop)+'px';"
         >
         </div>
       </div>
     </div>
   </div>
	</div>
</div>
