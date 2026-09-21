<?php


namespace qcformbuilderwp\qcformbuilderforms\pro\attachments;


/**
 * Class mailer
 * @package qcformbuilderwp\qcformbuilderforms\pro\attachments
 */
class mailer extends \PHPMailer
{

	/** @inheritdoc */
	public function attachAll($disposition_type, $boundary)
	{
		return parent::attachAll($disposition_type, $boundary);
	}
}
