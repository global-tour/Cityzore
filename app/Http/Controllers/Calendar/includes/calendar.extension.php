<?php
	
	class AFFC2Calendar_Extensions
	{
		
		/**
		* This function updates event drag, resize, repetitive event from jquery fullcalendar (update for repetitive events)
		*/
		protected function update_ui_repetitive($start, $end, $allDay_value, $repeat_type, $id, $extra)
		{
			$isForm = debug_backtrace();
			
			foreach($isForm as $is) 
			{
				if($is['function'] == 'updates')
				{
					$update = true;
				}
			}
			
			if(isset($update) && $update == true)
			{
				$extra['id'] = $id;
				$extra['start'] = $start;
				$extra['end'] = $end;
				unset($extra['rep_id']);
				$this->updates($extra, $_FILES, 'repeat_id');
			}
			
			if(strlen($allDay_value) == 0)
			{
				if(is_array($extra))
				{
					if(isset($extra['url']))
					{
						$url = $this->db_escape($extra['url']);
					} else {
						$url = "false";	
					}
					
					$title = $this->db_escape($extra['title']);
					$description = $this->db_escape($extra['description']);
					$color = $this->db_escape($extra['color']);
					$category = $this->db_escape($extra['category']);

					$the_query = "title = '$title', description = '$description', color = '$color', category = '$category', url = '$url',";	
				} else {
					$the_query = '';	
				}
			} else {
				$the_query = "allDay = '$allDay_value',";	
			}
			
			$query = sprintf("UPDATE %s 
									SET 
										start = '%s',
										end = '%s',
										%s
										repeat_type = '%s'
									WHERE
										id = '%d'
						",
										$this->db_escape($this->table),
										$this->db_escape($start),
										$this->db_escape($end),
										$the_query,
										$repeat_type,
										$this->db_escape($id)
						);
			
			// The result
			return $this->result = mysqli_query($this->connection, $query);
		}
		
		/**
		* This function extends the update functions (repetitive event procedure for updates)
		*/
		protected function repetitive_event_procedure($allDay, $start, $end, $id, $original_id, $extra)
		{
			$event = $this->get_event($original_id);
			$repeat_type = $event['repeat_type'];
			
			$this->update_ui_repetitive($start, $end, $allDay, $repeat_type, $original_id, $extra);
			
			$query = mysqli_query($this->connection, sprintf(
				"SELECT id FROM $this->table WHERE repeat_id = '%d'",
				$this->db_escape($id)
			));
			
			while($row = mysqli_fetch_assoc($query))
			{
				$ids[] = $row['id'];
			}
			
			$current = array_search($original_id, $ids);
			$prev = array_slice($ids,0,$current);
			$next = array_slice($ids,$current+1);
			
			switch($repeat_type)
			{
				case "every_day":
					$pTime = "-1 day";
					$nTime = "+1 day";
				break;
				
				case "every_week":
					$pTime = "-1 week";
					$nTime = "+1 week";
				break;
				
				case "every_month":
					$pTime = "-1 month";
					$nTime = "+1 month";
				break;
			}
			
			$startPi = $start;
			$endPi = $end;
			$startNi = $start;
			$endNi = $end;
			
			if($prev)
			{
				arsort($prev);
				foreach($prev as $v)
				{
					$startPrev = date('Y-m-d H:i:s', strtotime($pTime, strtotime($startPi)));
					$endPrev = date('Y-m-d H:i:s', strtotime($pTime, strtotime($endPi)));
					$this->update_ui_repetitive($startPrev, $endPrev, $allDay, $repeat_type, $v, $extra);
					$event = $this->get_event($v);
					$startPi = $event['start'];
					$endPi = $event['end'];
				}
			}
			
			if($next)
			{
				foreach($next as $v)
				{
					$startNext = date('Y-m-d H:i:s', strtotime($nTime, strtotime($startNi)));
					$endNext = date('Y-m-d H:i:s', strtotime($nTime, strtotime($endNi)));
					$this->update_ui_repetitive($startNext, $endNext, $allDay, $repeat_type, $v, $extra);
					$event = $this->get_event($v);
					$startNi = $event['start'];
					$endNi = $event['end'];
				}
			}
			
			return true; // 23.09.2021 - Kerem
		}
		
		/**
		* Insert Repetitive Events Query (since 1.6.4)
		*/
		protected function insert_repetitive_query($fields, $start, $end)
		{
			$query =  mysqli_query($this->connection, sprintf("INSERT INTO %s 
															SET 
																title = '%s',
																description = '%s',
																start = '%s',
																end = '%s',
																allDay = '%s',
																color = '%s',
																url = '%s',
																category = '%s',
																user_id = '%d',
																repeat_id = '%d',
																repeat_type = '%s'
												",
													$fields['table'],
													$fields['title'],
													$fields['description'],
													$start,
													$end,
													$fields['all-day'],
													$fields['color'],
													$fields['url'],
													$fields['categorie'],
													$fields['user_id'],
													$fields['repeat_id'],
													$fields['repeat_method']
												));
			
			$inserted_id = mysqli_insert_id($this->connection);
			
			unset($fields['title'], $fields['description']);
			unset($fields['start_date'], $fields['start_time'], $fields['end_date'], $fields['end_time']);
			unset($fields['all-day'], $fields['color'], $fields['url'], $fields['categorie']);
			unset($fields['user_id']);
			unset($fields['repeat_method'], $fields['repeat_times'], $fields['repeat_id']);
				
			// add all other custom fields
			if(!empty($fields))
			{
				foreach($fields as $k => $v)
				{ 
					$fk = $this->db_escape($k);
					mysqli_query(
						$this->connection, 
						sprintf("UPDATE %s SET `{$fk}` = '%s' WHERE id = '%d'", $this->db_escape($this->table), $this->db_escape($v), $inserted_id)
					);
				}
			}
			
		}
		
		/**
		* Insert Repetitive Events (since 1.6.4)
		*/
		protected function insert_repetitive_events($fields, $current_date, $current_month, $current_year)
		{
			$repeat_times = $fields['repeat_times'];
			
			$end_current_date = date('d', strtotime($fields['end_date']));
			$end_current_month = date('m', strtotime($fields['end_date']));
			$end_current_year = date('Y', strtotime($fields['end_date']));
			
			switch($fields['repeat_method'])
			{
				case 'every_day':
					if($repeat_times <= '30')
					{
						for($i = 1; $i <= $repeat_times; $i++)
						{
							$start = date('Y-m-d', strtotime("+$i day", strtotime($current_year.'-'.$current_month.'-'.$current_date))) . ' ' .$fields['start_time'].':00';
							$end = date('Y-m-d', strtotime("+$i day", strtotime($end_current_year.'-'.$end_current_month.'-'.$end_current_date))) . ' ' .$fields['end_time'].':00';
							$this->insert_repetitive_query($fields, $start, $end);
						}
						return true;
					}
				break;
				
				case 'every_week':
					if($repeat_times <= 30)
					{
						for($i = 1; $i <= $repeat_times; $i++)
						{
							$start = date('Y-m-d', strtotime("+$i week", strtotime($current_year.'-'.$current_month.'-'.$current_date))) . ' ' .$fields['start_time'].':00';
							$end = date('Y-m-d', strtotime("+$i week", strtotime($end_current_year.'-'.$end_current_month.'-'.$end_current_date))) . ' ' .$fields['end_time'].':00';
							$this->insert_repetitive_query($fields, $start, $end);
						}
						return true;
					}
				break;
				
				case 'every_month':
					if($repeat_times <= 30)
					{
						for($i = 1; $i <= $repeat_times; $i++)
						{
							$start = date('Y-m-d', strtotime("+$i month", strtotime($current_year.'-'.$current_month.'-'.$current_date))) . ' ' .$fields['start_time'].':00';
							$end = date('Y-m-d', strtotime("+$i month", strtotime($end_current_year.'-'.$end_current_month.'-'.$end_current_date))) . ' ' .$fields['end_time'].':00';
							$this->insert_repetitive_query($fields, $start, $end);
						}
						return true;
					}
				break;
			}
		}
		
		/**
		* This function updates custom fields
		*/
		protected function update_custom_fields($post, $id) 
		{
			if(!empty($post))
			{
				foreach($post as $k => $v)
				{
					$fk = $this->db_escape($k);					
					mysqli_query(
						$this->connection, 
						sprintf("UPDATE %s SET `{$fk}` = '%s' WHERE id = '%d'", $this->db_escape($this->table), $this->db_escape($v), $id)
					);
				}
			}
		}
		
		/**
		* This function handles file upload
		*/
		protected function handle_file_upload($post, $file) 
		{
			if(!empty($file))
			{
				if(strlen($file['file']['name']) !== 0)
				{
					// Allowed Extensions
					$targetFolder = $this->uploadDir;
					$fileTypes = array('zip','pdf','doc','ppt','xls','txt','docx','xlsx','pptx','png','jpg','gif');
					$fileParts = pathinfo($file['file']['name']);

					$tempFile = $file['file']['tmp_name'];

					$timestamp = time();
					$filename = $timestamp . $_FILES['file']['name'];
					$targetFile = $targetFolder . $filename;
					
					if(in_array($fileParts['extension'], $fileTypes)) 
					{
						$upd = move_uploaded_file($tempFile, $targetFile);
						if($upd) 
						{
							$post['file'] = SITE_FILES_URL.$filename;
							return $post;
						} else {
							$post['file'] = '';
							return $post;
						}
						
					} else {
						return $post;
					}
				} else {
					return $post;
				}
			} else {
				return $post;
			}
		}
		
		/**
		* Strip unwanted tags from the calendar
		* Those that want HTML support on the calendar use this function on the 'updates' and 'addEvent' to the $description
		* like this $this->strip_html_tags($description) to filter it and use on the function 'json_transform' htmlspecialchars_decode($event_description)
		* to render html to the event description.
		*/
		protected function strip_html_tags($text)
		{
			$text = preg_replace('~<\s*\bscript\b[^>]*>(.*?)<\s*\/\s*script\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bhead\b[^>]*>(.*?)<\s*\/\s*head\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bstyle\b[^>]*>(.*?)<\s*\/\s*style\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bobject\b[^>]*>(.*?)<\s*\/\s*object\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bapplet\b[^>]*>(.*?)<\s*\/\s*applet\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bnoframes\b[^>]*>(.*?)<\s*\/\s*noframes\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bnoscript\b[^>]*>(.*?)<\s*\/\s*noscript\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bframeset\b[^>]*>(.*?)<\s*\/\s*frameset\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bframe\b[^>]*>(.*?)<\s*\/\s*frame\s*>~is', '', $text);
			$text = preg_replace('~<\s*\biframe\b[^>]*>(.*?)<\s*\/\s*iframe\s*>~is', '', $text);
			$text = preg_replace('~<\s*\bform\b[^>]*>(.*?)<\s*\/\s*form\s*>~is', '', $text);
			$text = preg_replace('/on[a-z]+=\".*\"/i', '', $text);
			
			return $text;
			
		}
				
	}

?>