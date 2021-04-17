<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NF_FU_Integrations_NinjaForms_FileUploadMergeTags extends NF_Abstracts_MergeTags {

	protected $id = 'file_uploads';

	protected $filename;
	protected $extension;

	public function __construct()
	{
		parent::__construct();
		$this->title = __( 'File Uploads', 'ninja-forms-uploads' );

		$this->merge_tags = array(
			'fu_filename' => array(
				'id' => 'fu_filename',
				'tag' => '{file:name}',
				'label' => __( 'Filename', 'ninja-forms-uploads' ),
				'callback' => array( $this, 'get_filename' )
			),
			'fu_extension' => array(
				'id' => 'fu_extension',
				'tag' => '{file:extension}',
				'label' => __( 'File Extension', 'ninja-forms-uploads' ),
				'callback' =>  array( $this, 'get_extension' )
			),
		);
	}

	public function set_filename( $filename = '' ) {
		$this->filename = $filename;
	}

	public function set_extension( $extension ) {
		$this->extension = $extension;
	}

	public function get_filename() {
		return $this->filename;
	}

	public function get_extension() {
		return $this->extension;
	}
}