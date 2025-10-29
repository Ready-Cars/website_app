<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class ZeptomailTransport extends AbstractTransport
{
    protected string $apiKey;

    protected string $endpoint = 'https://api.zeptomail.com/v1.1/email';

    public function __construct(string $apiKey, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null)
    {
        $this->apiKey = $apiKey;
        parent::__construct($dispatcher, $logger);
    }

    public function __toString(): string
    {
        return 'zeptomail://api';
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $payload = [
            'from' => [
                'address' => $email->getFrom()[0]->getAddress(),
                'name' => $email->getFrom()[0]->getName() ?? '',
            ],
            'to' => [],
            'subject' => $email->getSubject(),
        ];

        // Add recipients
        foreach ($email->getTo() as $recipient) {
            $payload['to'][] = [
                'email_address' => [
                    'address' => $recipient->getAddress(),
                    'name' => $recipient->getName() ?? '',
                ],
            ];
        }

        // Add CC recipients
        if ($email->getCc()) {
            $payload['cc'] = [];
            foreach ($email->getCc() as $recipient) {
                $payload['cc'][] = [
                    'email_address' => [
                        'address' => $recipient->getAddress(),
                        'name' => $recipient->getName() ?? '',
                    ],
                ];
            }
        }

        // Add BCC recipients
        if ($email->getBcc()) {
            $payload['bcc'] = [];
            foreach ($email->getBcc() as $recipient) {
                $payload['bcc'][] = [
                    'email_address' => [
                        'address' => $recipient->getAddress(),
                        'name' => $recipient->getName() ?? '',
                    ],
                ];
            }
        }

        // Add reply-to
        if ($email->getReplyTo()) {
            $replyTo = $email->getReplyTo()[0];
            $payload['reply_to'] = [
                'address' => $replyTo->getAddress(),
                'name' => $replyTo->getName() ?? '',
            ];
        }

        // Set email body
        if ($email->getHtmlBody()) {
            $payload['htmlbody'] = $email->getHtmlBody();
        }

        if ($email->getTextBody()) {
            $payload['textbody'] = $email->getTextBody();
        }

        // Handle attachments
        if ($email->getAttachments()) {
            $payload['attachments'] = [];
            foreach ($email->getAttachments() as $attachment) {
                $payload['attachments'][] = [
                    'content' => base64_encode($attachment->getBody()),
                    'mime_type' => $attachment->getContentType(),
                    'name' => $attachment->getName() ?? 'attachment',
                ];
            }
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Zoho-enczapikey '.$this->apiKey,
        ])->post($this->endpoint, $payload);

        if (! $response->successful()) {
            throw new \Exception('ZeptoMail API request failed: '.$response->body());
        }

    }
}
