<?php
include_once('ww_config/model_functions.php');

	// exit();

// functions to split tags and attachments into separate tables

	function split_tags() {
		$conn = author_connect();
		$query = "SELECT articleID, map_tags
					FROM ww_articles
					WHERE map_tags > 0";
		$result = $conn->query($query);
		$tag_data = array();
		while($row = $result->fetch_assoc()) { 
			$tags = explode(',', $row['map_tags']);
			foreach($tags as $tag) {
				$newtag['article_id'] = $row['articleID'];
				$newtag['tag_id'] = $tag;
				$tag_data[] = $newtag;
			}
		}
		// insert data
		foreach($tag_data as $insert_tag) {
			$insert = "INSERT INTO tags_map (tag_id, article_id) 
						VALUES (".(int)$insert_tag['tag_id'].", ".(int)$insert_tag['article_id'].")";
			$result = $conn->query($insert);
		}
		return;
	}

/* attachments */
	
	function split_attachments() {
		$conn = author_connect();
		$query = "SELECT articleID, map_attachments
					FROM ww_articles
					WHERE map_attachments > 0";
		$result = $conn->query($query);
		$attach_data = array();
		while($row = $result->fetch_assoc()) { 
			$attachments = explode(',', $row['map_attachments']);
			$article = $row['articleID'];
			foreach($attachments as $attach) {
				$newattach['article_id'] = $article;
				$newattach['attachment_id'] = $attach;
				$attach_data[] = $newattach;
			}
		}
		// insert data
		foreach($attach_data as $attachment) {
			$insert = "INSERT INTO attachments_map (attachment_id, article_id) 
						VALUES (".(int)$attachment['attachment_id'].", ".(int)$attachment['article_id'].")";
			$result = $conn->query($insert);
		}
		return;
	}
	
// articles

	$sql['articles'] = "
	REPLACE INTO articles
	SELECT	articleID,
			art_title,
			art_permatitle,
			art_summary,
			art_body,
			catID,
			authorID,
			art_status,
			art_uploaded,
			art_amended,
			art_seo_title,
			art_seo_desc,
			art_keywords,
			view_count,
			visit_count,
			comments_disable,
			comments_hide
	FROM ww_articles;";

// attachments

	$sql['attachments'] = "
	REPLACE INTO attachments 
	SELECT	attID,
			att_filename,
			att_title,
			att_desc,
			att_authorID,
			att_ext,
			att_mime,
			att_size,
			att_counter,
			att_uploaded
	FROM ww_attachments;";

// authors

	$sql['authors'] = "
	REPLACE INTO authors
	SELECT	authorID,
			author_name,
			author_url,
			author_summary,
			author_bio,
			author_email,
			author_pass,
			guest_flag,
			guest_areas,
			sub_expiry,
			last_login,
			last_ip,
			last_sess,
			author_img,
			show_email
	FROM ww_authors;";
	
// categories

	$sql['categories'] = "
	REPLACE INTO categories
	SELECT	catID,
			parent_catID,
			cat_name,
			cat_url,
			cat_desc,
			'',
			''
	FROM ww_categories;";
	
// comments

	$sql['comments'] = "
	REPLACE INTO comments
	SELECT	commentID,
			replytoID,
			adminID,
			articleID,
			'',
			com_body,
			com_uploaded,
			com_posted_by,
			com_userlink,
			com_email,
			com_IP,
			com_approved
	FROM ww_comments;";

// images

	$sql['images'] = "
	REPLACE INTO images
	SELECT	imgID,
			img_filename,
			img_title,
			img_alt,
			img_credit,
			img_caption,
			img_authorID,
			img_w,
			img_h,
			'',
			'',
			img_size,
			img_uploaded
	FROM ww_images;";
	
// links

	$sql['links'] = "
	REPLACE INTO links
	SELECT	linkID,
			link_name,
			link_url,
			'',
			link_desc,
			link_cat,
			''
	FROM ww_links;";
	
// tags

	$sql['tags'] = "
	REPLACE INTO tags
	SELECT * FROM ww_tags;";

// copy settings

	$update = "
	UPDATE settings, ww_config 
	SET settings.property_value = ww_config.property_value
	WHERE settings.property_name = ww_config.property_name";

// run sql
	
	$conn = author_connect();

// check attachments_map
	
	$a_check = "SELECT * FROM attachments_map";
	$a_total = $conn->query($a_check);
	$a_rows = $a_total->num_rows;
	echo 'attachments_map has '.$a_rows.' rows<br/>';
	if(empty($a_rows)) {
		$attach_check = split_attachments();
		echo '-- attachments_map updated<br/>';
	} else {
		echo '-- attachments_map NOT updated<br/>';
	}
				
// check tags_map
		
	$t_check = "SELECT * FROM tags_map";
	$t_total = $conn->query($t_check);
	$t_rows = $t_total->num_rows;
	echo 'tags_map has '.$t_rows.' rows<br/>';
	if(empty($t_rows)) {
		$tags_check = split_tags();
		echo '-- tags_map updated<br/>';
	} else {
		echo '-- tags_map NOT updated<br/>';
	}		
	
// update settings

	$s_check = "SELECT * FROM settings";
	$s_total = $conn->query($s_check);
	$s_rows = $s_total->num_rows;
	echo 'settings has '.$s_rows.' rows<br/>';
	$update_result = $conn->query($update);
	if(!$update_result) {
		echo '-- settings not copied over<br />';
	} else {
		echo '-- copying settings over -> success<br/>';
		$rename = 'RENAME TABLE ww_config TO ww_migrated_config';
		$conn->query($rename);
	}

// all other tables
	
	foreach($sql as $table => $insert) {
		$check = "SELECT * FROM ".$table;
		$total = $conn->query($check);
		$rows = $total->num_rows;
		echo $table.' has '.$rows.' rows<br/>';
		if(empty($rows)) {
			$result = $conn->query($insert);
			if(!$result) {
				echo $conn->error.'<br />';
			} else {
				echo '-> success<br/>';
				$rename = 'RENAME TABLE ww_'.$table.' TO ww_migrated_'.$table;
				$conn->query($rename);
			}			
		} else {
			echo '-- '.$table.' NOT updated<br/>';
		}
	}
	


// advisory

	echo '
	<h4>although the migration process can only be run once you should do the following:</h4>
		<ul>
			<li>log into your site via ftp and delete this file (migrate.php)</li>
			<li>log into your mysql database and delete the old tables</li>
		<ul>
	';
?>