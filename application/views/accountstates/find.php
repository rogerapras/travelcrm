<div class="container findform"><?= form_open(get_currcontroller()."/find/go", array('id'=>'find_'.$orid, 'autocomplete'=>'off'))?><?=form_hidden('obj_rid', $orid)?><div class="column span-3">	<h6><?=lang('SEARCH_TITLE')?></h6></div><div class="column span-6">	<?=form_label(lang('CODE'), 'code')?>	<br>	<?=form_input('code', element('code', $search, ''), 'id="code" class="text part-3"')?></div><div class="column span-6">	<?=form_label(lang('NAME'), 'state_name')?>	<br>	<?=form_input('state_name', element('state_name', $search, ''), 'id="state_name" class="text part-5"')?></div><div class="column span-4">	<?=form_label(lang('TYPE'), 'koef')?>	<br>	<?=form_dropdown('koef', get_koef_list(), element('koef', $search, ''), 'id="koef" class="text"')?></div><div class="column span-5 last">	<?=form_label(lang('HIDE_ARCHIVE'), 'archive')?>	<br>	<?=form_dropdown('archive', array(1=>lang('NO'), 0=>lang('YES')), element('_account_states.archive', $search, ''), 'id="archive" class="text"')?></div><?= form_close(); ?><div class="column span-24 last find-tools">	<input type="button" value="<?=lang('GOFIND')?>" onclick="" class="button" id="find_submit" name="find_submit"> <input type="button" value="<?=lang('GOCLEAR')?>" onclick="" class="button"  id="find_reset" name="find_reset"></div></div><script type="text/javascript">$(document).ready(		function(){			$('#find_submit').click(function(){$('#find_<?=$orid?>').submit();});			$('#find_reset').click(function(){					$('#code').val('');					$('#state_name').val('');					$('#koef').val('');					$('#archive').val('1');					$('#find_<?=$orid?>').submit();				});		})	</script>
