<?php
include_once('ww_config/model_functions.php');

	exit();

// functions to split tags and attachments into separate tables

	function split_tags() {
		$conn = author_connect();
		$query = "SELECT articleID, map_tags
					FROM ww_articles
					WHERE map_tags > 0";
		$result = $conn->query($query);
		$data = array();
		while($row = $result->fetch_assoc()) { 
			$tags = explode(',', $row['map_tags']);
			$article = $row['articleID'];
			foreach($tags as $tag) {
				$rowtag['article_id'] = $article;
				$rowtag['tag_id'] = $tag;
				$datatags[] = $rowtag;
			}
		}
		// insert data
		foreach($datatags as $tag) {
			$insert = "INSERT INTO tags_map (tag_id, article_id) 
						VALUES (".(int)$tag['tag_id'].", ".(int)$tag['article_id'].")";
			$result = $conn->query($insert);
		}
		return $datatags;
	}

/* attachments */
	
	function split_attachments() {
		$conn = author_connect();
		$query = "SELECT articleID, map_attachments
					FROM ww_articles
					WHERE map_attachments > 0";
		$result = $conn->query($query);
		$data = array();
		while($row = $result->fetch_assoc()) { 
			$tags = explode(',', $row['map_attachments']);
			$article = $row['articleID'];
			foreach($tags as $tag) {
				$rowtag['article_id'] = $article;
				$rowtag['attachment_id'] = $tag;
				$datatags[] = $rowtag;
			}
		}
		// insert data
		foreach($datatags as $attach) {
			$insert = "INSERT INTO attachments_map (attachment_id, article_id) 
						VALUES (".(int)$attach['attachment_id'].", ".(int)$attach['article_id'].")";
			$result = $conn->query($insert);
		}
		return $datatags;
	}

/*
	$tags_check = split_tags();
	debug_array($tags_check);
*/

/*
	$attach_check = split_attachments();
	debug_array($attach_check);
*/

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
			cat_summary,
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
			link_cat
	FROM ww_links;";
	
// tags

	$sql['tags'] = "
	REPLACE INTO tags
	SELECT * FROM ww_tags;";

// copy settings

	$update = "
	UPDATE settings, ww_config 
	SET property_value = ww_config.property_value
	WHERE settings.property_name = ww_settings.property_name";

// run sql
	
	$conn = author_connect();
	
	foreach($sql as $table => $insert) {
		$check = "SELECT * FROM ".$table;
		$total = $conn->query($check);
		$rows = $total->num_rows;
		echo $table.' has '.$rows.' rows<br/>';
		if(empty($rows)) {
			$result = $conn->query($total);
			if(!$result) {
				echo $conn->error.'<br />';
			} else {
				echo '-> success<br/>';
			}			
		}
	}
	
	// update settings
	$update_result = $conn->query($update);
	if(!$result) {
		echo 'settings not copied over '.$conn->error.'<br />';
	} else {
		echo 'copying settings over -> success<br/>';
	}	
?>