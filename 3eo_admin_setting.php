<?php
global $wpdb;
if(isset($_POST['s3eo_tag_new'])) { 
	add_tag_other($_POST['s3eo_tag_new']);
}
if(isset($_GET['rid'])) { 
	if(remove_tag($_GET['rid']) ) { print 'تگ موردنظر حذف شد ';} 
}

if (isset($_POST['do'])) { 
	global $wpdb;
	$counter=1;
	foreach ($_POST['catid'] as $ccid) { 
		if($counter == count($_POST['catid'])) {
				$ccid2 .= $ccid;
			} else { 
				$ccid2 .= $ccid.':';
			}
			$counter=$counter+1;
	}
	
	$ress=$wpdb->update('wp_3eo_tagbank',array('title'=>$_POST['editme'],'cat'=>$ccid2),array('id'=>$_POST['do']),array('%s','%s'),array( '%s' ) );
	if($ress) { print 'ویرایش با موفقیت ثبت شد'; }
	
} else { 
if(isset($_GET['eid'])) { 
	$catlist=explode(':',getcatlistbytagid($_GET['eid']));
	$params['hide_empty']=0;
	$listcat=get_categories($params);
	echo "<form method='post'><input type='text' name='editme' value='".tagnamebyid($_GET['eid'])."'><br />";
	foreach ($listcat as $cat) {
		
		print "<input type='checkbox' name='catid[]' value='".$cat->term_id."' ";
		if(catidintaglist($cat->term_id,$_GET['eid'])) { print 'checked'; }
		
		print "> ".$cat->name." <br />";
	}
	echo '<input type="submit" value="ذخیره">
	<input type="hidden" name="do" value="'.$_GET['eid'].'">
	</form>';
	
} else {

	if(isset($_POST['autotagsb'])) { 
		update_option('3eo_autotag','1');
		
	}
	
?>
<form method="POST">
	<input type="checkbox" name="autotag" <? if(get_option('3eo_autotag') == '1') { print "checked"; } ?>> <?php echo _e('autotagging_enable','wpfixtag');?> 
	<input type="submit" name="autotagsb" value="<?php _e('done','wpfixtag'); ?>">
	</form><hr>
		<table class="sortable widefat" cellspacing="0">
				<thead>
				<tr>
					<th scope="col" ><?php _e('id','wpfixtag');?></th>
					<th scope="col" ><?php _e('tag_title','wpfixtag');?></th>
					<th scope="col" ><?php _e('categories','wpfixtag');?></th>
					<th scope="col" ><?php _e('operations','wpfixtag');?></th>
				</tr>
				</thead>
				<tbody>
	<?php

$gettagz=$wpdb->get_results("SELECT * FROM wp_3eo_tagbank",ARRAY_A);
foreach ($gettagz as $tagz ) { 
	$cattagz=explode(':',$tagz['cat']);
	foreach ($cattagz as $k) { 
		$catnames .= get_category($k)->name." | ";
	}
	?>
	<tr onmouseover="this.style.backgroundColor='lightblue';" onmouseout="this.style.backgroundColor='white';">
	<?
	echo "<td>".$tagz['id']."</td><td>".$tagz['title']."</td><td>".$catnames."</td><td><a href='admin.php?page=s3eomenu&rid=".$tagz['id']."'>".__('delete','wpfixtag')."<a/> - <a href='admin.php?page=s3eomenu&eid=".$tagz['id']."'>".__('edit','wpfixtag')."</a></td>";
	?></tr><?php
}
}
}
?>
</table>