<script>
////	Resize
lightboxSetWidth(600);

////	INIT
$(function(){
	////	Option "espace public"
	$("input[name='public']").change(function(){
		//Affiche le password si l'espace est public
		(this.checked) ? $("#divPassword").fadeIn() : $("#divPassword").fadeOut();
		//Affiche la notification "Si votre espace public contient des données sensibles[...]"
		if(this.checked && $("#divPassword input[name=password]").isEmpty())  {notify("<?= Txt::trad("SPACE_publicSpaceNotif") ?>","warning");}
	}).trigger("change");//Trigger: init l'affichage

	////	Sélectionne un module (ou pas) : affiche/masque les options de chaque module
	$("input[name='moduleList[]']").change(function(){
		$("[name='moduleList[]']").each(function(){
			var optionsSelector=".moduleOptions"+this.id.replace("module","");
			if(this.checked)	{$(optionsSelector).show();}
			else				{$(optionsSelector).hide();}
		});
	}).trigger("change");//Trigger: init l'affichage

	////	Option "disablePolls" : active/désactive l'option "adminAddPoll"
	$("input[value='disablePolls']").change(function(){ 
		if(this.checked)	{$("input[value='adminAddPoll']").prop("disabled",true).prop("checked",false);}
		else				{$("input[value='adminAddPoll']").prop("disabled",false);}
	});

	////	Initialise le tri des modules (".vModuleLineSort" pour déplacer à partir de cette cellule. "hightlight" pour afficher un module "fantome". "y" pour déplacer uniquement en vertical)
	if(isMobile()==false)  {$("#modulesList").sortable({handle:".vModuleLineSort",placeholder:'highlight',axis:"y"}).disableSelection();}

	////	Init les affectations des Spaces<->Users (cf. "common.js")
	initSpaceAffectations();
});

////	Contrôle du formulaire
function formControl()
{
   //Controle le nb de modules cochés
   if($("input[name='moduleList[]']:checked").length==0)  {notify("<?= Txt::trad("SPACE_selectModule") ?>"); return false; }
	//Controle final (champs obligatoires, affectations/droits d'accès, etc)
	return mainFormControl();
}
</script>

<style>
textarea[name='description']		{margin-top:20px; <?= empty($curSpace->description)?"display:none;":null ?>}
.vSpaceOption						{margin-bottom:20px;}
.vSpaceOption>img					{max-width:18px;}
#divPassword						{padding-left:30px; margin-top:0px!important; font-style:italic;}
.vWallpaper							{display:table; width:100%; margin-top:10px;}
.vWallpaper>div						{display:table-cell;}
.vWallpaper>div:first-of-type		{width:90px;}
.vWallpaper img						{max-height:90px;}
label[for='allUsers']				{font-size:1.1em; font-style:italic;}
.usersFieldset						{max-height:700px; overflow:auto;}/*fieldset des users*/

/*modules*/
#modulesFieldset					{padding:0px;}/*surcharge*/
#modulesList						{list-style-type:none; margin:0px; padding:0px; width:100%;}
#modulesList .ui-state-default		{border-top:none;}
#modulesList li						{padding:8px 0px 8px 0px; background:linear-gradient(to bottom, #fff, #fcfcfc, #f5f5f5); border-bottom:#ddd 1px solid;}
#modulesList li.highlight			{border:1px dashed #aaa; height:80px; }/*module "fantome" durant le déplacement*/
.vModuleLine						{display:table; width:100%;}
.vModuleLine>div					{display:table-cell; font-weight:bold; padding:4px;}
.vModuleLine img					{max-height:20px;}
.vModuleLine img[src*='dependency']	{margin-left:8px;}
.vModuleLineIcon					{vertical-align:middle; margin-left:5px;}
.vModuleLineSort					{width:18px; cursor:move; background-image:url(app/img/dragDrop.png);}/*icone de tri*/
div[class^='moduleOptions']			{display:none; padding:3px;}/*masque par défaut les options*/

/*RESPONSIVE FANCYBOX (440px)*/
@media screen and (max-width:440px){
	.vModuleLineIcon, .vModuleLineSort	{display:none!important;}
}
</style>


<form action="index.php" method="post" onsubmit="return formControl()" enctype="multipart/form-data" class="lightboxContent">

	<!--NOM/DESCRIPTION-->
	<div class="vSpaceOption">
		<input type="text" name="name" value="<?= $curSpace->name ?>" class="textBig" placeholder="<?= Txt::trad("name") ?>">
		<img src="app/img/description.png" class="sLink" title="<?= Txt::trad("description") ?>" onclick="$('textarea[name=description]').slideToggle();">
		<textarea name="description" placeholder="<?= Txt::trad("description") ?>"><?= $curSpace->description ?></textarea>
	</div>

	<!--ESPACE PUBLIC (avec password?)-->
	<div class="vSpaceOption" title="<?= Txt::trad("SPACE_publicSpaceInfo") ?>">
		<img src="app/img/public.png"> <input type="checkbox" name="public" id="public" value="1" <?= (!empty($curSpace->public))?'checked':null ?>>
		<label for="public"><?= Txt::trad("SPACE_publicSpace") ?></label>
		<div id="divPassword">
			<img src="app/img/dependency.png"> <?= Txt::trad("password") ?> : &nbsp; 
			<input type="text" name="password" value="<?= $curSpace->password ?>">
		</div>
	</div>


	<!--INSCRIPTION A L'ESPACE-->
	<div class="vSpaceOption" title="<?= Txt::trad("userInscriptionOptionSpaceInfo") ?>">
		<img src="app/img/edit.png"> <input type="checkbox" name="usersInscription" id="usersInscription" value="1" <?= (!empty($curSpace->usersInscription))?'checked':null ?>>
		<label for="usersInscription"><?= Txt::trad("userInscriptionOptionSpace") ?></label>
	</div>

	<!--INVITATIONS PAR MAIL-->
	<div class="vSpaceOption" title="<?= Txt::trad("SPACE_usersInvitationInfo") ?>">
		<img src="app/img/mail.png"> <input type="checkbox" name="usersInvitation" id="usersInvitation" value="1" <?= (!empty($curSpace->usersInvitation))?'checked':null ?>>
		<label for="usersInvitation"><?= Txt::trad("SPACE_usersInvitation") ?></label>
	</div>

	<!--WALLPAPER-->
	<div class="vSpaceOption vWallpaper">
		<div class="fieldLabel"><?= Txt::trad("wallpaper") ?></div>
		<div><?= CtrlMisc::menuWallpaper($curSpace->wallpaper) ?></div>
	</div>

	<!--MODULES DE L'ESPACE-->
	<div class="lightboxBlockTitle"><?= Txt::trad("SPACE_spaceModules") ?></div>
	<div class="lightboxBlock" id="modulesFieldset">
		<ul id="modulesList">
		<?php
		////	SELECTION DES MODULES
		foreach($moduleList as $moduleName=>$tmpModule)
		{
			//Prépare les options du module
			$moduleOptions=null;
			foreach($tmpModule["ctrl"]::$moduleOptions as $optionName)
			{
				//Création d'agenda : uniquement pour un nouvel espace
				if($optionName=="createSpaceCalendar" && $curSpace->isNew()==false)  {continue;}
				//Init l'affichage
				$checkOption=(!empty($tmpModule["options"]) && stristr($tmpModule["options"],$optionName))  ?  "checked"  :  null;
				if($optionName=="createSpaceCalendar")  {$checkOption="checked";}//Création d'agenda préselectionné
				$inputId=$moduleName."Option".$optionName;
				$tradLabelId=strtoupper($moduleName)."_option_".$optionName;
				$labelTitle=Txt::isTrad($tradLabelId."Info")  ?  Txt::trad($tradLabelId."Info")  :  null;
				//Affiche l'option
				$moduleOptions.="<div class=\"moduleOptions".$moduleName."\">
									<img src='app/img/dependency.png'><input type='checkbox' name=\"".$moduleName."Options[]\" value=\"".$optionName."\" id=\"".$inputId."\" ".$checkOption.">
									<label for=\"".$inputId."\" title=\"".$labelTitle."\">".Txt::trad($tradLabelId)."</label>
								</div>";
			}
			//Affiche le module et ses options
			echo "<li>
					<div class='vModuleLine'>
						<div>
							<input type='checkbox' name='moduleList[]' value=\"".$moduleName."\" id=\"module".$moduleName."\" ".(empty($tmpModule["disabled"])?"checked":null).">
							<label for=\"module".$moduleName."\" title=\"".$tmpModule["description"]."\">".$tmpModule["label"]." <img src=\"app/img/".$moduleName."/icon.png\" class='vModuleLineIcon'></label>
							".$moduleOptions."
						</div>
						<div class='vModuleLineSort' title=\"".Txt::trad("SPACE_moduleRank")."\">&nbsp;</div>
					</div>
				</li>";
		}
		?>
		</ul>
	</div>

	<!--USERS DE L'ESPACE-->
	<?php if(Ctrl::$curUser->isAdminGeneral()){ ?>
	<div class="lightboxBlockTitle"><?= Txt::trad("SPACE_usersAccess") ?></div>
	<div class="lightboxBlock usersFieldset">
		<div class="spaceAffectLine">
			<label>&nbsp;</label>
			<div title="<?= Txt::trad("SPACE_userInfo") ?>"><img src="app/img/user/accesUser.png"> <?= Txt::trad("SPACE_user") ?></div>
			<div title="<?= Txt::trad("SPACE_adminInfo") ?>"><img src="app/img/user/adminSpace.png"> <?= Txt::trad("SPACE_admin") ?></div>
		</div>
		<div class="spaceAffectLine sTableRow">
			<label for="allUsers"><?= Txt::trad("SPACE_allUsers") ?></label>
			<div title="<?= Txt::trad("SPACE_userInfo") ?>"><input type="checkbox" name="allUsers" id="allUsers" value="allUsers" <?= ($curSpace->allUsersAffected())?'checked':null ?>></div>
			<div>&nbsp;</div>
		</div>
		<?php foreach($userList as $tmpUser){
			$disableRead=($curSpace->allUsersAffected())  ?  "disabled"  :  null;
			$checkRead=($curSpace->userAccessRight($tmpUser)==1 || $curSpace->allUsersAffected())  ?  "checked"  :  null;
			$checkWrite=($curSpace->userAccessRight($tmpUser)==2)  ?  "checked"  :  null;
		?>
		<div class="spaceAffectLine sTableRow" id="targetLine<?= $tmpUser->_id ?>">
			<label class="spaceAffectLabel"><?= $tmpUser->getLabel() ?></label>
			<div title="<?= Txt::trad("SPACE_userInfo") ?>"><input type="checkbox" name="spaceAffect[]" class="spaceAffectInput" value="<?= $tmpUser->_id ?>_1" <?= $checkRead." ".$disableRead ?>></div>
			<div title="<?= Txt::trad("SPACE_adminInfo") ?>"><input type="checkbox" name="spaceAffect[]" class="spaceAffectInput" value="<?= $tmpUser->_id ?>_2" <?= $checkWrite ?>></div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>

	<!--MENU COMMUN-->
	<?= $curSpace->menuEdit() ?>
</form>