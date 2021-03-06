<div class="grid fieldscontainer">
<h3><?=$title?></h3>
<?= form_open(get_currcontroller()."/create/go", array('id'=>'create_'.$orid, 'autocomplete'=>'off'))?>
<div class="container editform">
<?if(validation_errors()){?>
<div class="error">
	<?=validation_errors('<div>', '</div>');?>
</div>	
<?}?>
<?if($success===False){?>
<div class="error">
	<?=lang('SAVE_SYSTEM_ERROR')?>
</div>
<?}?>
<?if($success===True){?>
<div class="success">
	<?=lang('SAVE_SYSTEM_SUCCESS')?>
</div>
<?}?>
<div class="column span-4">
	<?=form_label(lang('CODE').required_field(), 'country_code')?>
</div>
<div class="column span-20 last">
	<?=form_input('country_code', set_value('country_code', ''), 'id="country_code" class="text part"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('NAME').required_field(), 'country_name')?>
</div>
<div class="column span-20 last">
	<?=form_input('country_name', set_value('country_name', ''), 'id="country_name" class="text part-5"')?>
</div>

<div class="column span-4">
	<?=form_label(lang('NAME_LAT').required_field(), 'country_name_lat')?>
</div>
<div class="column span-20 last">
	<?=form_input('country_name_lat', set_value('country_name_lat', ''), 'id="country_name_lat" class="text part-5"')?>
</div>

<div class="column span-4">
	<?=form_label(lang('ARCHIVE'), 'archive')?>
</div>
<div class="column span-20 last">
	<?=form_dropdown('archive', array('0'=>lang('NO'), '1'=>lang('YES')), set_value('archive', '0'), 'id="archive" class="text"')?>
</div>

<div class="column span-4">
	<?=form_label(lang('DESCR'), 'descr')?>
</div>
<div class="column span-20 last">
	<?=form_textarea('descr', set_value('descr', ''), 'id="descr" class="text" style="width:300px; height: 50px;"')?>
</div>

</div>
<div class="column span-24 last controls">
	<input type="submit" value="<?=lang('SAVE')?>" class="button" id="submit" name="submit"> <input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller()) ?>';" id="reset" name="reset">
</div>

<?= form_close(); ?>

</div>