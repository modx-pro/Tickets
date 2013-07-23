<?php
class TicketQueue extends xPDOSimpleObject {

	public function Send() {
		$user = $this->getOne('User');
		$profile = $user->getOne('Profile');

		if (!$user->get('active') || $profile->get('blocked')) {
			return 'This user is not active.';
		}

		/* @var modPHPMailer $mail */
		$mail = $this->xpdo->getService('mail', 'mail.modPHPMailer');
		$mail->setHTML(true);

		$mail->set(modMail::MAIL_SUBJECT, $this->subject);
		$mail->set(modMail::MAIL_BODY, $this->body);
		$mail->set(modMail::MAIL_FROM, $this->xpdo->getOption('tickets.mail_from', null, $this->xpdo->getOption('emailsender'), true));
		$mail->set(modMail::MAIL_FROM_NAME, $this->xpdo->getOption('tickets.mail_from_name', null, $this->xpdo->getOption('site_name'), true));
		$mail->set(modMail::MAIL_SENDER, $this->xpdo->getOption('tickets.mail_fom_name', null, $this->xpdo->getOption('site_name'), true));

		$mail->address('to', $profile->get('email'));

		if (!$mail->send()) {
			$this->xpdo->log(xPDO::LOG_LEVEL_ERROR, 'An error occurred while trying to send the email: '.$mail->mailer->ErrorInfo);

			$mail->reset();
			return false;
		}

		$mail->reset();
		return true;
	}

}