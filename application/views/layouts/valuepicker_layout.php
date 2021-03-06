<?=doctype('html4-trans')?>
<html>
<head>
	<title>TravelCRM - <?=$title?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<?=link_tag('public/css/blueprint/src/reset.css');?>
	<?=link_tag('public/css/blueprint/src/forms.css');?>
	<?=link_tag('public/css/blueprint/src/liquid.css');?>
	<?=link_tag('public/css/blueprint/src/typography.css');?>
	<?=link_tag('public/css/modules/common.css');?>
	<?=link_tag('public/css/modules/grid.css');?>
	<?=link_tag('public/css/modules/paging.css');?>
	<?=link_tag('public/css/modules/findform.css');?>
	<?=link_tag('public/css/modules/editform.css');?>
	<?=link_tag('public/css/modules/jquery.jdMenu.css')?>
	<?=link_tag('public/js/jquery.datapick.package-3.6.1/jquery.datepick.css')?>
	<!--[if IE]>
		<?=link_tag('public/css/blueprint/ie.css');?>
	<![endif]-->
	<script type="text/javascript" src="<?=base_url()?>public/js/jquery-1.4.1.min.js"></script>
	<script src="<?=base_url()?>public/js/jqueryslidemenu.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.dateentry.min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.timeentry.min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.textarearesizer.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/vtip-min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>public/js/jquery.jSlider.js" type="text/javascript"></script>
</head>
<body>
	<div class="vcrmpage">
		<?=$content?>
	</div>
</body>
</html>