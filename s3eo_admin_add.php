<?php
global $wpdb;
?>

<form method="POST">
عنوان تگ : <input type="text" name="s3eo_tag_new"><br />
	دسته بندی :<br />
	
<?php
	$params['hide_empty']=0;
	$listcat=get_categories($params);
	foreach ($listcat as $cat) {
		print "<input type='checkbox' name='catid[]' value='".$cat->term_id."'> ".$cat->name." <br />";
	}
	
	?>	
	<input type="submit">
</form><hr>
		<?php
	$counter=1;
		if(isset($_POST['catid'])) { 
			foreach ($_POST['catid'] as $cid) { 
				if($counter == count($_POST['catid'])) { 
					$catz .= $cid;
				} else { 
					$catz .= $cid.":";
				}
				$counter=$counter+1;
			}
			
			$data=array('title'=>$_POST['s3eo_tag_new'],'cat'=>$catz);
			$dtype=array('%s','%s');
			
			$result=$wpdb->insert('wp_3eo_tagbank',$data,$dtype);
			if($result) { print 'اضافه شد '; } else { print mysql_error(); }
		}
		?>
