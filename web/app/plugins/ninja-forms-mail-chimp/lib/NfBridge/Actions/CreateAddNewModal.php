<?php


namespace NFMailchimp\EmailCRM\NfBridge\Actions;

use NFMailchimp\EmailCRM\NfBridge\Entities\Modal;

/**
 * Generate markup that constructs a Modal entity for Add New forms
 *
 */
class CreateAddNewModal
{

	/**
	 *
	 * @var Modal
	 */
	protected $modal;

	/**
	 * Complete REST encpoint for Add New button, including WP REST URL
	 * @var string
	 */
	protected $restEndpoint;
	
	/**
	 * Generated Nonce Field
	 *
	 * Constructed in WP using
	 *         ob_start();
	 *         wp_nonce_field('wp_rest');
	 *         $nonceField = ob_get_clean();
	 *
	 * @var string
	 */
	protected $nonceField;
	
	/**
	 * Construct and return the modal
	 * @return Modal
	 */
	public function handle(Modal $modal, string $restEndpoint, string $nonceField): Modal
	{

		$this->modal = $modal;
		
		$this->restEndpoint = $restEndpoint;
		
		$this->nonceField = $nonceField;
		
		$this->constructModalContent();

		$this->addFormTitleInput();
		
		$this->closeModalContent();
		
		return $this->modal;
	}


	/**
	 * Construct full markup from incoming modal content
	 *
	 * Incoming modal content does not include markup for button or title input
	 * This method adds opening and closing div along with input box for title
	 * and button with link to endpoint
	 */
	protected function constructModalContent()
	{

		$array = $this->modal->toArray();
		
		$incomingContent = $array['modalContent'];
		
		$this->modal->setModalContent('<div class="modal-template">');
		
		$this->modal->appendModalContent($incomingContent);

		$this->modal->appendModalContent('<form action  = "' .  $this->restEndpoint.'" method = "POST">');

		$this->modal->appendModalContent($this->nonceField);
	}


	/**
	 * Append form title input box
	 */
	protected function addFormTitleInput()
	{

		$textbox = $this->generateFormTitleTextbox();
		$this->modal->appendModalContent($textbox);
	}


	/**
	 * Generate HTML for form title text box
	 * @return string
	 */
	public function generateFormTitleTextbox(): string
	{

		$textbox = '<div class="nf-realistic-field nf-realistic-field--label-above" id="nf-field-field-1-wrap">
            <div class="nf-realistic-field--label">
                <div>
                    <div class="nf-field-label">
                        <div id="nf-label-formTitle" class="">
                            Form Title
                            <span class="ninja-forms-req-symbol">*</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nf-realistic-field--description"></div>
            <div class="nf-realistic-field--element">
                <div style="margin-bottom:24px;">
                    <input type="text" value="" class=" nf-element" placeholder="" id="formTitle" name="formTitle" aria-invalid="false" aria-describedby="nf-error-1" aria-labelledby="nf-label-formTitle">
                </div>
            </div>
        </div>';
		return $textbox;
	}

	/**
	 * Close modal content HTML
	 */
	protected function closeModalContent()
	{
		$this->modal->appendModalContent('<input type="submit" class="button button-primary" value="Create"></form></div>');
	}
}
