<?php

namespace NFMailchimp\NinjaForms\Mailchimp\Handlers;

// NF Bridge
use NFMailchimp\EmailCRM\NfBridge\Entities\Modal;

/**
 * Create the modal box for NF Mailchimp form autogeneration
 *
 */
class CreateAutogenerateModal
{

	/**
	 *
	 * @var Modal
	 */
	protected $modal;

	/**
	 * Construct and return the modal
	 * @param array $lists Array of Mailchimp lists
	 * @return Modal
	 */
	public function handle(array $lists): Modal
	{

		$this->initializeModal();
		$this->openModalContent();
				$selectMarkup = $this->generateSelectOptionMarkup($lists);
		$this->modal->appendModalContent($selectMarkup);
		$this->addFormTitleInput();
		$this->closeModalContent();
		return $this->modal;
	}

	/**
	 * Initialize standard settings
	 */
	public function initializeModal(): Modal
	{
		$this->modal = Modal::fromArray([
					'id' => 'mailchimp-autogenerate',
					'title' => 'Mailchimp Signup Form',
                    'type'=>'autogenerate',
					'templateDesc' => 'Create a fully customizable but ready-to-use Mailchimp signup form using any Audience in your Mailchimp account.',
					'modalTitle' => 'Mailchimp Signup Form'
		]);
		
		return $this->modal;
	}

	/**
	 * Add opening HTML for modal content
	 */
	protected function openModalContent()
	{
				$restUrl = \rest_url();
			
		$this->modal->setModalContent('<div class="modal-template">');
		$this->modal->appendModalContent('<p style="margin-top:0px;">To generate a new form, select an audience list from below.</p>');
		$this->modal->appendModalContent('<p>The form will build itself and and after that you can finalize the styling to your liking.</p>');
				
		$this->modal->appendModalContent('<form action  = "'.$restUrl.'nf-mailchimp/v2/nf-autogenerate" method = "POST">');
				ob_start();
		wp_nonce_field('wp_rest');
		$nonceField=ob_get_clean();
				$this->modal->appendModalContent($nonceField);
	}

	/**
	 * Add autogenerate form button links for each list
	 *
	 * If `list['value']` is 0, it is a placeholder for headings, so skip it
	 * @param array $lists
	 */
	public function generateSelectOptionMarkup(array $lists):string
	{
				$selectOptionMarkup = '<div class="nf-realistic-field--element"><div>'
						.'<select name="listId" style="width:100%; margin-bottom:24px;">';

		foreach ($lists as $list) {
			if (isset($list['value']) && 0 === $list['value']) {
				continue;
			}
			$option = $this->generateOptionHtml($list);
			$selectOptionMarkup .= $option;
		}
		$selectOptionMarkup.= '</select></div></div>';
				
				return $selectOptionMarkup;
	}

	protected function addFormTitleInput()
	{
		
		$textbox = $this->generateFormTitleTextbox();
		$this->modal->appendModalContent($textbox);
	}
	/**
	 * Generate HTML for autogenerate option with endpoint link
	 * @param array $list
	 * @return string
	 */
	public function generateOptionHtml(array $list): string
	{

		 $optionMarkup = '<option label = "' .\esc_html($list['label']) . '">' . \esc_html($list['value']) . '</option>';
		return $optionMarkup;
	}

	/**
	 * Generate HTML for form title text box
	 * @return string
	 */
	public function generateFormTitleTextbox():string
	{

		$textbox =
		'<div class="nf-realistic-field nf-realistic-field--label-above" id="nf-field-field-1-wrap">
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
