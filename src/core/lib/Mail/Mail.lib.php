<?php
/**
 * $Id: Mail.lib.php 208 2016-01-12 07:44:19Z radu $
 *
 * Mail library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebMail
 * @version    $Revision: 208 $
 * @since      Version 5
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (! defined('_WEB')) {
	/**
	 * Stop script execution.
	 */
	exit();
}

/**
 * Mail
 * Mail library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebMail
 */
class Mail extends Library {
	
	/**
	 * Stores mail content type.
	 *
	 * @var string
	 */
	protected $_mContentType;
	
	/**
	 * Stores mail encoding.
	 *
	 * @var string
	 */
	protected $_mEncoding;
	
	/**
	 * Stores mail charset.
	 *
	 * @var unknown_type
	 */
	protected $_mCharset;
	
	/**
	 * Should we use sender envelope?
	 *
	 * @var bool
	 */
	protected $_mEnvelope;
	
	/**
	 * Should we use SMTP?
	 *
	 * @var array
	 */
	protected $_mSmtp;
	
	/**
	 * Should we add the Date header?
	 *
	 * @var array
	 */
	protected $_mDateHeader;
	
	/**
	 * Stores mail sender.
	 *
	 * @var array
	 */
	protected $_mMailFrom = array();
	
	/**
	 * Stores mail reply-to.
	 *
	 * @var array
	 */
	protected $_mMailReplyTo = array();
	
	/**
	 * Stores mail recipients.
	 *
	 * @var array
	 */
	protected $_mMailTo = array();
	
	/**
	 * Stores mail cc recipients.
	 *
	 * @var array
	 */
	protected $_mMailCc = array();
	
	/**
	 * Stores mail bcc recipients.
	 *
	 * @var array
	 */
	protected $_mMailBcc = array();
	
	/**
	 * Stores mail attachments.
	 *
	 * @var array
	 */
	protected $_mAttachments = array();
	
	/**
	 * Known attachment types.
	 *
	 * @var array
	 */
	protected $_mAttachKnown = array();
	
	/**
	 * Allowed attachemnt types.
	 *
	 * @var array
	 */
	protected $_mAttachAllowed = array();
	
	/**
	 * Stores mail headers.
	 *
	 * @var array
	 */
	protected $_mHeaders = array();
	
	/**
	 * Stores mail line ending.
	 *
	 * @var string
	 */
	protected $_mEol;

	/**
	 * Implement GetInstance() method.
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className
	 *        	class name to instantiate
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {

		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	/**
	 * Set default data (resets any existing data).
	 *
	 * @return bool
	 */
	public function SetDefaults() {

		/**
		 *
		 * @var array
		 */
		$config = Core::Config()->Get('mail');
		
		/**
		 *
		 * @var string
		 */
		$this->_mContentType = ! empty($config['content_type']) ? $config['content_type'] : 'plain';
		
		/**
		 * Configuration is ignored and we use base64 as default.
		 *
		 * @var string
		 */
		$this->_mEncoding = 'base64';
		
		/**
		 *
		 * @var string
		 */
		$this->_mCharset = ! empty($config['charset']) ? $config['charset'] : 'utf-8';
		
		/**
		 *
		 * @var string
		 */
		$this->_mEnvelope = (1 == $config['envelope']) ? TRUE : FALSE;
		
		/**
		 *
		 * @var string
		 */
		$this->_mSmtp = (1 == $config['smtp']) ? TRUE : FALSE;
		
		/**
		 *
		 * @var string
		 */
		$this->_mDateHeader = ! empty($config['date_header']) ? TRUE : FALSE;
		
		/**
		 *
		 * @var array
		 */
		$this->_mAttachKnown = array(
				'bin' => 'application/octet-stream',
				'gif' => 'image/gif',
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
				'zip' => 'application/zip',
				'gzip' => 'application/x-gzip',
				'text' => 'text/plain',
				'html' => 'text/html',
				'xml' => 'text/xml',
				'xhtml' => 'application/xhtml+xml',
				'pdf' => 'application/pdf',
				'odt' => 'application/vnd.oasis.opendocument.text',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
				'odp' => 'application/vnd.oasis.opendocument.presentation',
				'odg' => 'application/vnd.oasis.opendocument.graphics',
				'xls' => 'application/vnd.ms-excel',
				'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'ppt' => 'application/vnd.ms-powerpoint',
				'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'doc' => 'application/msword',
				'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		);
		
		/**
		 *
		 * @var array
		 */
		$this->_mAttachAllowed = explode(',', $config['attach']);
		
		/**
		 *
		 * @var string
		 */
		$this->_mEol = PHP_EOL;
		
		$this->_mMailFrom = array();
		
		$this->_mMailReplyTo = array();
		
		$this->_mMailCc = array();
		
		$this->_mMailBcc = array();
		
		$this->_mMailTo = array();
		
		$this->_mHeaders = array();
		
		$this->_mAttachments = array();
		
		return TRUE;
	}

	/**
	 * Add an email address.
	 *
	 * @param string $addEmail        	
	 * @param string $addType        	
	 * @param string $addName        	
	 *
	 * @return bool
	 */
	public function AddAddress($addEmail, $addType = 'to', $addName = NULL) {

		if (empty($this->_mEol)) {
			$this->SetDefaults();
		}
		
		if (FALSE === ($addEmail = filter_var($addEmail, FILTER_VALIDATE_EMAIL))) {
			/**
			 *
			 * @var string
			 */
			$msg = sprintf(__('Mail Library: Error adding address "%s". Invalid format.', 'WEb'), $addEmail);
			
			Core::Log()->Write($msg, 'error');
			
			return FALSE;
		}
		
		/**
		 *
		 * @var string
		 */
		$msg = sprintf(__('Mail Library: Adding address of type "%s".', 'WEb'), $addType);
		
		Core::Log()->Write($msg, 'debug');
		
		switch ($addType) {
			case 'from' :
				/**
				 * There can be only one...
				 *
				 * @var array
				 */
				$this->_mMailFrom = array(
						$addEmail => $addName
				);
				
				break;
			
			case 'replyto' :
				/**
				 * There can be only one...
				 *
				 * @var array
				 */
				$this->_mMailReplyTo = array(
						$addEmail => $addName
				);
				
				break;
			
			case 'cc' :
				/**
				 *
				 * @var array
				 */
				$this->_mMailCc[$addEmail] = $addName;
				
				break;
			
			case 'bcc' :
				/**
				 *
				 * @var array
				 */
				$this->_mMailBcc[$addEmail] = $addName;
				
				break;
			
			case 'to' :
			default :
				/**
				 *
				 * @var array
				 */
				$this->_mMailTo[$addEmail] = $addName;
				
				break;
		}
		
		return TRUE;
	}

	/**
	 * Add attachment.
	 *
	 * @param string $attachPath        	
	 * @param string $attachType        	
	 * @param string $attachName        	
	 *
	 * @return bool
	 */
	public function AddAttachment($attachPath, $attachType = NULL, $attachName = NULL) {

		if (empty($this->_mEol)) {
			$this->SetDefaults();
		}
		
		if (! is_readable($attachPath)) {
			/**
			 *
			 * @var string
			 */
			$msg = sprintf(__('Mail Library: Error adding attachment "%s". File not found.', 'WEb'), $attachPath);
			
			Core::Log()->Write($msg, 'error');
			
			return FALSE;
		}
		
		/**
		 *
		 * @var string
		 */
		$attach_type = (! empty($attachType)) ? $attachType : 'bin';
		
		/**
		 * Check if attachment type is allowed.
		 */
		if (! in_array($attach_type, $this->_mAttachAllowed)) {
			/**
			 *
			 * @var string
			 */
			$msg = sprintf(__('Mail Library: Error adding attachment "%s". Format not allowed.', 'WEb'), $attachPath);
			
			Core::Log()->Write($msg, 'error');
			
			return FALSE;
		}
		
		/**
		 * Check if attachment type is known.
		 */
		if (! array_key_exists($attach_type, $this->_mAttachKnown)) {
			/**
			 *
			 * @var string
			 */
			$msg = sprintf(__('Mail Library: Error adding attachment "%s". Unknown format.', 'WEb'), $attachPath);
			
			Core::Log()->Write($msg, 'error');
			
			return FALSE;
		}
		
		/**
		 *
		 * @var string
		 */
		$attach_name = (! empty($attachName)) ? $attachName : basename($attachPath);
		
		/**
		 *
		 * @var array
		 */
		$this->_mAttachments[$attachPath] = array(
				$attach_type,
				$attach_name
		);
		
		/**
		 *
		 * @var string
		 */
		$msg = sprintf(__('Mail Library: Added attachment "%s".', 'WEb'), $attachPath);
		
		Core::Log()->Write($msg, 'debug');
		
		return TRUE;
	}

	/**
	 * Add header.
	 *
	 * @param string $headerName        	
	 * @param string $headerValue        	
	 *
	 * @return bool
	 */
	public function AddHeader($headerName, $headerValue) {

		if (empty($this->_mEol)) {
			$this->SetDefaults();
		}
		
		/**
		 *
		 * @var array
		 */
		$this->_mHeaders[$headerName] = $headerValue;
		
		return TRUE;
	}

	/**
	 * Sent email after setting all data.
	 *
	 * @param string $msgSubject        	
	 * @param string $msgBody        	
	 *
	 * @return boolean
	 */
	public function Send($msgSubject, $msgBody) {

		if (empty($this->_mEol)) {
			$this->SetDefaults();
		}
		
		if (TRUE !== $this->_CheckAddreses()) {
			Core::Log()->Write(__('Mail Library: Error sending mail. One or more required addresses are missing.', 'WEb'), 'error');
			
			return FALSE;
		}
		
		$this->AddHeader('MIME-Version', '1.0');
		
		mb_internal_encoding('UTF-8');
		
		$msg_subject = mb_encode_mimeheader($msgSubject, 'UTF-8', 'B', "\n");
		
		/**
		 *
		 * @var string
		 */
		$msg_body = $this->_GetMessageBody($msgBody);
		
		/**
		 *
		 * @var string
		 */
		$msg_to = $this->_GetAddress('to');
		
		/**
		 *
		 * @var string
		 */
		$msg_from = $this->_GetAddress('from');
		
		/**
		 * We don't need the value, just make sure the header is set.
		 */
		$this->_GetAddress('replyto');
		
		/**
		 * Check if we have Cc.
		 */
		if (! empty($this->_mMailCc)) {
			/**
			 * We don't need the value, just make sure the header is set.
			 */
			$this->_GetAddress('cc');
		}
		
		/**
		 * Check if we have Bcc.
		 */
		if (! empty($this->_mMailBcc)) {
			/**
			 * We don't need the value, just make sure the header is set.
			 */
			$this->_GetAddress('bcc');
		}
		
		/**
		 * Add application fingerprint.
		 */
		$this->AddHeader('X-Mailer', 'WEb Application Framework (http://web.webserv.co)');
		
		/**
		 * Check if we should add the Date header.
		 */
		if (FALSE !== $this->_mDateHeader) {
			
			$this->AddHeader('Date', date('r'));
		}
		
		/**
		 *
		 * @var string
		 */
		$msg_from_address = key($this->_mMailFrom);
		
		/**
		 * Check if we should use SMTP instead of regular mail.
		 */
		if (FALSE !== $this->_mSmtp) {
			/**
			 *
			 * @var array
			 */
			$msg_recipients = array_merge($this->_mMailTo, $this->_mMailCc, $this->_mMailBcc);
			
			if (TRUE !== Core::Smtp()->Send($msg_recipients, $msg_from_address, $msg_subject, $msg_body, $this->_mHeaders)) {
				Core::Log()->Write(__('Mail Library: Error sending message.', 'WEb'), 'error');
				
				return FALSE;
			}
			
			Core::Log()->Write(__('Mail Library: Message sent successfully.', 'WEb'), 'debug');
			
			return TRUE;
		}
		
		/**
		 * Set sender.
		 */
		ini_set('sendmail_from', $msg_from_address);
		
		/**
		 * Remove the To value from header.
		 * If this is left, it will appear twice in email headers.
		 */
		unset($this->_mHeaders['To']);
		
		/**
		 *
		 * @var string
		 */
		$headers = $this->_GetHeaders();
		
		/**
		 *
		 * @var string
		 */
		$envelope = (FALSE !== $this->_mEnvelope) ? "-r {$msg_from_address}" : NULL;
		
		if (TRUE !== mail($msg_to, $msg_subject, $msg_body, $headers, $envelope)) {
			Core::Log()->Write(__('Mail Library: Error sending message.', 'WEb'), 'error');
			
			return FALSE;
		}
		
		Core::Log()->Write(__('Mail Library: Message sent successfully.', 'WEb'), 'debug');
		
		return TRUE;
	}

	/**
	 * Simple mail sending in one go.
	 *
	 * @param string $msgTo        	
	 * @param string $msgSubject        	
	 * @param string $msgBody        	
	 *
	 * @return boolean
	 */
	public function SendSimple($msgTo, $msgSubject, $msgBody, $fromAddress = NULL, $fromName = NULL) {

		$this->SetDefaults();
		
		$mail_settings = Core::Config()->Get('mail');
		
		$from_address = $mail_settings['sender']['address'];
		
		$from_name = $mail_settings['sender']['name'];
		
		if (empty($msgTo)) {
			
			$msgTo = $mail_settings['recipient']['address'];
		}
		
		if (! empty($fromAddress)) {
			
			$from_name = $fromName;
			
			$replyto_address = $fromAddress;
			
			$this->AddAddress($replyto_address, 'replyto', $fromName);
		}
		
		$this->AddAddress($from_address, 'from', $from_name);

        $bccAddress = $mail_settings['bcc']['address'];
        if (!empty($bccAddress)) {
            $this->AddAddress($bccAddress, 'bcc', $mail_settings['bcc']['name']);
        }

		$this->AddAddress($msgTo, 'to', NULL);
		
		return $this->Send($msgSubject, $msgBody);
	}

	/**
	 * Check addresses before sending mail.
	 *
	 * @return bool
	 */
	private function _CheckAddreses() {

		if (empty($this->_mEol)) {
			$this->SetDefaults();
		}
		
		if (empty($this->_mMailTo)) {
			/**
			 *
			 * @var array
			 */
			$recipient = Core::Config()->Get('mail.recipient');
			
			if (empty($recipient['address'])) {
				Core::Log()->Write(__('Mail Library: Error sending mail. You have to set at least one recipient.', 'WEb'), 'error');
				
				return FALSE;
			}
			
			/**
			 * Add default recipient.
			 */
			$this->AddAddress($recipient['address'], 'to', $recipient['name']);
		}
		
		if (empty($this->_mMailFrom)) {
			/**
			 *
			 * @var array
			 */
			$sender = Core::Config()->Get('mail.sender');
			
			if (empty($sender['address'])) {
				Core::Log()->Write(__('Mail Library: Error sending mail. You have to set sender.', 'WEb'), 'error');
				
				return FALSE;
			}
			
			/**
			 * Add default sender.
			 */
			$this->AddAddress($sender['address'], 'from', $sender['name']);
		}
		
		if (empty($this->_mMailReplyTo)) {
			/**
			 *
			 * @var string
			 */
			$reply_to_address = key($this->_mMailFrom);
			
			$this->AddAddress($reply_to_address, 'replyto', $this->_mMailFrom[$reply_to_address]);
		}
		
		return TRUE;
	}

	/**
	 * Get address list formatted for mail.
	 * Also sents corresponding headers.
	 *
	 * @param string $addType        	
	 *
	 * @return string
	 */
	private function _GetAddress($addType = 'to') {

		switch ($addType) {
			case 'from' :
				/**
				 *
				 * @var array
				 */
				$address_data = $this->_mMailFrom;
				
				/**
				 *
				 * @var string
				 */
				$header_type = 'From';
				
				break;
			
			case 'replyto' :
				/**
				 *
				 * @var array
				 */
				$address_data = $this->_mMailReplyTo;
				
				/**
				 *
				 * @var string
				 */
				$header_type = 'Reply-To';
				
				break;
			
			case 'cc' :
				/**
				 *
				 * @var array
				 */
				$address_data = $this->_mMailCc;
				
				/**
				 *
				 * @var string
				 */
				$header_type = 'Cc';
				
				break;
			
			case 'bcc' :
				/**
				 *
				 * @var array
				 */
				$address_data = $this->_mMailBcc;
				
				/**
				 *
				 * @var string
				 */
				$header_type = 'Bcc';
				
				break;
			
			case 'to' :
			default :
				/**
				 *
				 * @var array
				 */
				$address_data = $this->_mMailTo;
				
				/**
				 *
				 * @var string
				 */
				$header_type = 'To';
				
				break;
		}
		
		/**
		 *
		 * @var array
		 */
		$data = array();
		
		foreach ($address_data as $to => $name) {
			
			if (! empty($name)) {
				
				mb_internal_encoding('UTF-8');
				
				$data[] = mb_encode_mimeheader($name, 'UTF-8', 'B') . "<{$to}>";
			} else {
				
				$data[] = $to;
			}
		}
		
		/**
		 *
		 * @var string
		 */
		$address = implode(', ', $data);
		
		/**
		 * Add header.
		 */
		$this->AddHeader($header_type, $address);
		
		return $address;
	}

	/**
	 * Format and return mail message body.
	 * Also sets appropriate header.
	 *
	 * @param string $msgBody        	
	 *
	 * @return string
	 */
	private function _GetMessageBody($msgBody) {

		$message_boundary = sha1(uniqid());
		
		/**
		 *
		 * @var string
		 */
		$msg_pre = ('html' == $this->_mContentType) ? "<html><body>{$this->_mEol}" : NULL;
		
		/**
		 *
		 * @var string
		 */
		$msg_suf = ('html' == $this->_mContentType) ? "{$this->_mEol}</body></html>" : NULL;
		
		if (! empty($this->_mAttachments)) {
			
			$this->AddHeader('Content-type', "multipart/mixed; boundary=\"{$message_boundary}\"");
		} else {
			
			$this->AddHeader('Content-type', "multipart/alternative; boundary=\"{$message_boundary}\"");
		}
		
		$msg_body = NULL;
		$msg_body .= "--{$message_boundary}{$this->_mEol}";
		$msg_body .= "Content-type:text/{$this->_mContentType}; charset={$this->_mCharset}{$this->_mEol}";
		$msg_body .= "Content-Transfer-Encoding: {$this->_mEncoding}{$this->_mEol}{$this->_mEol}";
		$msg_body .= rtrim(chunk_split(base64_encode("{$msg_pre}{$msgBody}{$msg_suf}"))) . "{$this->_mEol}{$this->_mEol}"; // XXX
		                                                                                                                   // $msg_body_encoded .= "--{$message_boundary}--{$this->_mEol}";
		
		/**
		 * Check if we have attachments.
		 */
		if (! empty($this->_mAttachments)) {
			
			$msg_body = "{$this->_mEol}This is a multi-part message in MIME format.{$this->_mEol}{$msg_body}";
			
			foreach ($this->_mAttachments as $attach_path => $attach_info) {
				
				/**
				 *
				 * @var mixed
				 */
				$data = file_get_contents($attach_path);
				
				/**
				 *
				 * @var string
				 */
				$attach_data = chunk_split(base64_encode($data));
				
				/**
				 *
				 * @var string
				 */
				$msg_body .= "--{$message_boundary}{$this->_mEol}";
				$msg_body .= "Content-Type: {$attach_info[0]}; name=\"{$attach_info[1]}\"{$this->_mEol}";
				$msg_body .= "Content-Transfer-Encoding: {$this->_mEncoding}{$this->_mEol}";
				$msg_body .= "Content-Disposition: attachment; filename=\"{$attach_info[1]}\"{$this->_mEol}{$this->_mEol}";
				$msg_body .= "{$attach_data}{$this->_mEol}{$this->_mEol}";
			}
		
		/**
		 *
		 * @var string
		 */
		}
		
		$msg_body .= "--{$message_boundary}--";
		
		return $msg_body;
	}

	/**
	 * Get all headers as string
	 *
	 * @return string
	 */
	private function _GetHeaders() {

		if (empty($this->_mEol)) {
			$this->SetDefaults();
		}
		
		/**
		 *
		 * @var array
		 */
		$headers = array();
		
		foreach ($this->_mHeaders as $header_name => $header_value) {
			/**
			 *
			 * @var array
			 */
			$headers[] = "{$header_name}: {$header_value}";
		}
		
		return implode($this->_mEol, $headers) . $this->_mEol;
	}
}
?>