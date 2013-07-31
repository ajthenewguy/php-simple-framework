<?php
class Upload {
	
	private $uploads_directory;
	private $Inputs = array();
	
	
	public function __construct($Form = NULL, $uploads_directory = '') {
		if(NULL !== $Form) {
			$this->set_Form( $Form );
		}
		
		$this->set_upload_directory( $uploads_directory );
	}
	
	public function set_Form(Form $Form) {
		$this->Inputs = $Form->get_FileFields();
	}
	
	public function set_input(Form_Field $Form_Field) {
		$this->Inputs[] = $Form_Field;
		return $this;
	}
	
	public function set_upload_directory($uploads_directory = '') {
		/// CHECK DIRECTORY EXISTS?
		if($uploads_directory == '') {
			$this->uploads_directory = UPLOADS_PATH . '/';
		} elseif(Director::is_absolute( $uploads_directory )) {
			$this->uploads_directory = rtrim( $uploads_directory, '/' ) . '/';
		} else {
			$this->uploads_directory = UPLOADS_PATH . '/' . rtrim( $uploads_directory, '/' ) . '/';
		}
	}
	
	public function file_selected() {
		$files = $this->get_files();
		return (bool)count($files);
	}
	
	public function get_files() {
		$files = array();
		
		// SCAN $_FILES ARRAY
		if(isset( $this->Inputs ) && !empty( $this->Inputs )) {
			foreach($this->Inputs as $Input) {
				
				// REMOVE MULTIPLE FILE UPLOAD BRACKETS FROM INPUT NAME (ie. <input name="userfile[]" type="file" />)
				$input_name = (substr($Input->name, -2) == '[]' ? substr($Input->name, 0, -2) : $Input->name);
				
				if(isset( $_FILES[$input_name]['tmp_name'] ) && $_FILES[$input_name]['error'] != UPLOAD_ERR_NO_FILE) {
					
					// CHECK FOR MULTIPLE FILE UPLOAD FIELD (ie. <input name="userfile[]" type="file" />)
					if(is_array( $_FILES[$input_name]['name'] )) {
						$file_count = count( $_FILES[$input_name]['name'] );
						$file_keys = array_keys( $_FILES[$input_name] );
						
						for($i = 0; $i < $file_count; $i++) {
							$file_key = count( $files );
							foreach($file_keys as $key) {
								$files[$file_key][$key] = $_FILES[$input_name][$key][$i];
							}
						}
						
					} else {
						$file_keys = array_keys( $_FILES[$input_name] );
						$file_key = count( $files );
						foreach($file_keys as $key) {
							$files[$file_key][$key] = $_FILES[$input_name][$key];
						}
					}
				}
			}
		}
		
		return $files;
	}
	
	public function upload($uploads_directory = NULL, $can_overwrite = false) {
		$uploaded_files = array();
		$file_upload_errors = array();
		
		if(NULL !== $uploads_directory) {
			$this->set_upload_directory( $uploads_directory );
		}
		
		$files = $this->get_files();
		
		// UPLOAD FILES
		if(!empty( $files )) {
			foreach($files as $key => $file) {
				$ext = pathinfo( $file['name'], PATHINFO_EXTENSION );
				$filename = basename( $file['name'] );
				
				// ALLOW OVERWRITE OF EXISTING FILE?
				if(!$can_overwrite && file_exists( $this->uploads_directory.$filename )) {
					$i = 0;
					do {
						$filename = date("YmdHis").($i > 0 ? $i : '').'.'.$ext;
						$i++;
					} while(file_exists( $this->uploads_directory.$filename ));
				}
				
				if($file['error'] == UPLOAD_ERR_OK) {
					if(move_uploaded_file( $file['tmp_name'], $this->uploads_directory.$filename )) {
						$file['title'] = strtotitle( strtok( basename( $file['name'] ), '.' ) );
						$file['path'] = $this->uploads_directory.$filename;
						$uploaded_files[$key] = $file;
					} else {
						$file_upload_errors[$key] = 'Failed to move '.$filename.' to '.$this->uploads_directory;
					}
				} else {
					$file_upload_errors[$key] = $file['error'];
				}
			}
		}
		
		return array( $uploaded_files, $file_upload_errors);
	}
	
}

